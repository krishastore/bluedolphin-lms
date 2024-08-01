<?php
/**
 * The file that manage the file import functionality.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BlueDolphin\Lms
 */

namespace BlueDolphin\Lms\Helpers;

use BlueDolphin\Lms\ErrorLog as EL;
use BlueDolphin\Lms\Helpers\SettingOptions as Options;
use OpenSpout\Reader\Common\Creator\ReaderEntityFactory;

/**
 * Helpers utility class.
 */
class FileImport {

	/**
	 * Global options.
	 *
	 * @var string $options
	 */
	public $file;

	/**
	 * File reader.
	 *
	 * @var array $reader
	 */
	protected $reader = array();

	/**
	 * The main instance var.
	 *
	 * @var FileImport|null $instance The one FileImport instance.
	 * @since 1.0.0
	 */
	private static $instance = null;

	/**
	 * Init the main singleton instance class.
	 *
	 * @return FileImport Return the instance class
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new FileImport();
		}
		return self::$instance;
	}


	/**
	 * Init function.
	 */
	public function init() {
		add_action( 'wp_ajax_bdlms_get_file_attachment_id', array( $this, 'get_file_attachment_id' ) );
		add_action( 'wp_ajax_bdlms_get_import_cancel_data', array( $this, 'get_import_cancel_data' ) );
		add_action( 'init', array( $this, 'bdlms_schedule_cron_event' ) );
	}

	/**
	 * Schedule cron event.
	 */
	public function bdlms_schedule_cron_event() {
		$cron_hook = '';

		$import_data = \BlueDolphin\Lms\fetch_import_data();

		foreach ( $import_data as $data ) {
			if ( 1 === (int) $data['import_status'] ) {
				$cron_hook = 'bdlms_cron_import_' . $data['id'];

				add_action( $cron_hook, array( $this, 'import_question_data' ), 10, 2 );
			}
		}
	}

	/**
	 * Get file attachment id.
	 */
	public function get_file_attachment_id() {
		global $wpdb;

		check_ajax_referer( BDLMS_BASEFILE, '_nonce' );
		$attachment_id = isset( $_POST['attachment_id'] ) ? (int) $_POST['attachment_id'] : '';
		$file_name     = basename( get_attached_file( $attachment_id ) );
		$status        = 1;
		$progress      = 0;
		$args          = array();
		$args_1        = '';
		$args_2        = '';

		// Table name.
		$table_name = $wpdb->prefix . 'bdlms_cron_jobs';
		// insert a new record in a table.
		$result = $wpdb->query( //phpcs:ignore.
			$wpdb->prepare(
				'INSERT INTO ' . $table_name . '(attachment_id, file_name, progress, import_status ) VALUES (%d, %s, %d, %d)', //phpcs:ignore.
				$attachment_id,
				$file_name,
				$progress,
				$status
			)
		);

		if ( false !== $result ) {
			delete_transient( 'import_data' );

			$args_1    = $wpdb->insert_id;
			$args_2    = $attachment_id;
			$args      = array( $args_1, $args_2 );
			$cron_hook = 'bdlms_cron_import_' . $args_1;
			$run_time  = strtotime( '+1 minutes', time() );

			if ( ! wp_next_scheduled( $cron_hook, $args ) ) {
				wp_schedule_single_event( $run_time, $cron_hook, $args );
				EL::add( sprintf( 'Cron schedule at: %s, cron hook: %s', $run_time, $cron_hook ), 'info', __FILE__, __LINE__ );
			}
		} else {
			EL::add( sprintf( 'Failed to insert new record, File Name: %s', $file_name ), 'error', __FILE__, __LINE__ );
		}
		wp_send_json(
			array(
				'message' => $result,
			)
		);
	}

	/**
	 * Insert question data.
	 *
	 * @param int $args_1 cron table id.
	 * @param int $args_2 attachment id.
	 */
	public function import_question_data( $args_1, $args_2 ) {

		global $wpdb;

		// Table name.
		$table_name = $wpdb->prefix . 'bdlms_cron_jobs';
		$status     = '';

		if ( null !== $args_2 ) {
			$file = get_attached_file( (int) $args_2 );

			$reader = ReaderEntityFactory::createReaderFromFile( $file );

			$reader->open( $file );

			$total_rows    = 0;
			$success_cnt   = 0;
			$fail_cnt      = 0;
			$status        = 2;
			$curr_progress = 0;
			$flag          = false;

			// Count the total number of rows.
			foreach ( $reader->getSheetIterator() as $sheet ) {
				foreach ( $sheet->getRowIterator() as $key => $row ) {
					if ( $key > 1 ) {
						++$total_rows;
					} else {
						$value       = $row->toArray();
						$file_header = array( 'title', 'question_type', 'answers', 'right_answers' );
						foreach ( $file_header as $header ) {
							if ( ! in_array( $header, $value, true ) ) {
								$flag = true;
							}
						}
					}
				}
			}

			if ( $flag ) {
				$status = 4;
				$result = $wpdb->query( //phpcs:ignore.
					$wpdb->prepare(
						"UPDATE $table_name SET import_status = %d WHERE id = %d", //phpcs:ignore.
						$status,
						$args_1
					)
				);
				if ( false !== $result ) {
					EL::add( sprintf( 'File import status updated to failed: %d', $status ), 'info', __FILE__, __LINE__ );
					delete_transient( 'import_data' );
					return;
				}
			}

			foreach ( $reader->getSheetIterator() as $sheet ) {
				foreach ( $sheet->getRowIterator() as $key => $row ) {
					if ( $key > 1 ) {
						$value    = $row->toArray();
						$value    = array_filter( $value );
						$terms_id = array();

						if ( empty( $value[0] ) ) {
							continue;
						}

						$question = array(
							'post_title'   => $value[0],
							'post_content' => ! empty( $value[1] ) ? $value[1] : '',
							'post_status'  => 'publish',
							'post_type'    => \BlueDolphin\Lms\BDLMS_QUESTION_CPT,
							'meta_input'   => array(
								\BlueDolphin\Lms\META_KEY_QUESTION_TYPE => $value[8],
								\BlueDolphin\Lms\META_KEY_QUESTION_SETTINGS => array(),
							),
						);

						if ( ! empty( $value[2] ) ) {
							$terms = explode( '|', $value[2] );
							$terms = array_map( 'trim', $terms );

							foreach ( $terms as $_term ) {
								if ( term_exists( $_term, \BlueDolphin\Lms\BDLMS_QUESTION_TAXONOMY_TAG ) ) {
									$existing_term = get_term_by( 'name', $_term, \BlueDolphin\Lms\BDLMS_QUESTION_TAXONOMY_TAG );
									$terms_id[]    = $existing_term->term_id;
								} else {
									$terms      = wp_insert_term( $_term, \BlueDolphin\Lms\BDLMS_QUESTION_TAXONOMY_TAG );
									$terms_id[] = $terms['term_id'];
								}
							}
						}

						if ( ! empty( $value[9] ) ) {

							$choices = explode( '|', $value[9] );
							$choices = array_map( 'trim', $choices );

							if ( isset( $value[8] ) && 'single_choice' === $value[8] ) {
								$question['meta_input'][ \BlueDolphin\Lms\META_KEY_QUESTION_PREFIX . '_single_choice' ] = $choices;
							} elseif ( isset( $value[8] ) && 'multi_choice' === $value[8] ) {
								$question['meta_input'][ \BlueDolphin\Lms\META_KEY_QUESTION_PREFIX . '_multi_choice' ] = $choices;
							} elseif ( isset( $value[8] ) && 'true_or_false' === $value[8] ) {
								$question['meta_input'][ \BlueDolphin\Lms\META_KEY_QUESTION_PREFIX . '_true_or_false' ] = $choices;
							}
						}
						if ( ! empty( $value[5] ) ) {
							$value[5] = 'no' === $value[5] ? 0 : 1;
							$question['meta_input'][ \BlueDolphin\Lms\META_KEY_QUESTION_SETTINGS ]['status'] = $value[5];
						}

						$question['meta_input'][ \BlueDolphin\Lms\META_KEY_QUESTION_SETTINGS ]['points']      = ! empty( $value[3] ) ? $value[3] : 1;
						$question['meta_input'][ \BlueDolphin\Lms\META_KEY_QUESTION_SETTINGS ]['levels']      = ! empty( $value[4] ) ? $value[4] : 'easy';
						$question['meta_input'][ \BlueDolphin\Lms\META_KEY_QUESTION_SETTINGS ]['hint']        = ! empty( $value[6] ) ? $value[6] : '';
						$question['meta_input'][ \BlueDolphin\Lms\META_KEY_QUESTION_SETTINGS ]['explanation'] = ! empty( $value[7] ) ? $value[7] : '';

						if ( isset( $value[8] ) && 'multi_choice' === $value[8] ) {
							$right_ans = sprintf( \BlueDolphin\Lms\META_KEY_RIGHT_ANSWERS, $value[8] );
							$ans       = isset( $value[10] ) && ! empty( $value[10] ) ? explode( '|', $value[10] ) : array();

							if ( ! empty( $ans ) ) {

								$ans = array_map(
									function ( $v ) {
										return wp_hash( trim( $v ) );
									},
									$ans
								);
							}

							$question['meta_input'][ $right_ans ] = $ans;

						} elseif ( isset( $value[8] ) && 'fill_blank' === $value[8] ) {

							$mandatory_ans = explode( '|', $value[11] );
							$question['meta_input'][ \BlueDolphin\Lms\META_KEY_MANDATORY_ANSWERS ] = array_shift( $mandatory_ans );
							$optional_ans = $mandatory_ans;
							$question['meta_input'][ \BlueDolphin\Lms\META_KEY_OPTIONAL_ANSWERS ] = $optional_ans;

						} elseif ( isset( $value[8] ) && 'true_or_false' === $value[8] ) {
							$right_ans = sprintf( \BlueDolphin\Lms\META_KEY_RIGHT_ANSWERS, $value[8] );

							$ans = ! empty( $value[10] ) ? wp_hash( ucfirst( trim( strtolower( $value[10] ) ) ) ) : '';

							$question['meta_input'][ $right_ans ] = $ans;

						} else {
							$right_ans = sprintf( \BlueDolphin\Lms\META_KEY_RIGHT_ANSWERS, $value[8] );

							$ans = ! empty( $value[10] ) ? wp_hash( trim( $value[10] ) ) : '';

							$question['meta_input'][ $right_ans ] = $ans;
						}

						$question_id = wp_insert_post( $question );
						if ( $question_id ) {
							wp_set_post_terms( $question_id, $terms_id, \BlueDolphin\Lms\BDLMS_QUESTION_TAXONOMY_TAG );
							update_post_meta( $question_id, \BlueDolphin\Lms\META_KEY_IMPORT, $args_1 );
							++$success_cnt;
							EL::add( sprintf( 'Question: %s, Question ID: %d', get_the_title( $question_id ), $question_id ), 'info', __FILE__, __LINE__ );
						} else {
							++$fail_cnt;
							EL::add( sprintf( 'Failed to import question:- %s', $value[0] ), 'error', __FILE__, __LINE__ );
						}
					}

					// Calculate progress.
					$progress = (int) ( ( $key / $total_rows ) * 100 );

					if ( $progress >= 25 && $progress < 50 ) {
						$curr_progress = 25;
					} elseif ( $progress >= 50 && $progress < 75 ) {
						$curr_progress = 50;
					} elseif ( $progress >= 75 && $progress < 100 ) {
						$curr_progress = 75;
					} elseif ( 100 === $progress ) {
						$curr_progress = 100;
					}

					if ( $progress === $curr_progress ) {
						$result = $wpdb->query( //phpcs:ignore.
							$wpdb->prepare(
								"UPDATE $table_name SET progress = %d WHERE id = %d", //phpcs:ignore.
								$curr_progress,
								$args_1
							)
						);

						if ( false !== $result ) {
							EL::add( sprintf( 'File import progress : %d', $progress ), 'info', __FILE__, __LINE__ );
							delete_transient( 'import_data' );
						}
					}
				}
			}
		}

		if ( $fail_cnt > ceil( $total_rows / 2 ) ) {
			$status        = 4;
			$curr_progress = 0;
		}
			$result = $wpdb->query( //phpcs:ignore.
				$wpdb->prepare(
					"UPDATE $table_name SET import_status = %d, progress = %d, total_rows = %d, success_rows = %d, fail_rows = %d WHERE id = %d", //phpcs:ignore.
					$status,
					$curr_progress,
					$total_rows,
					$success_cnt,
					$fail_cnt,
					$args_1
				)
			);

		if ( false !== $result ) {
			EL::add( sprintf( 'File import status updated to : %d', $status ), 'info', __FILE__, __LINE__ );
			delete_transient( 'import_data' );
		}
	}

	/**
	 * Cancel the cron event.
	 */
	public function get_import_cancel_data() {
		global $wpdb;

		// Table name.
		$table_name = $wpdb->prefix . 'bdlms_cron_jobs';

		check_ajax_referer( BDLMS_BASEFILE, '_nonce' );
		$id            = isset( $_POST['id'] ) ? (int) $_POST['id'] : '';
		$attachment_id = isset( $_POST['attachment_id'] ) ? (int) $_POST['attachment_id'] : '';
		$data          = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : '';
		$cron_hook     = 'bdlms_cron_import_' . $id;
		$status        = 3;

		if ( ! empty( $id ) && ! empty( $attachment_id ) ) {
			wp_clear_scheduled_hook( $cron_hook, array( $id, $attachment_id ) );
			EL::add( sprintf( 'File import cancelled cleared the cron hook: %s', $cron_hook ), 'info', __FILE__, __LINE__ );

			$imported_question = get_posts(
				array(
					'post_type'    => \BlueDolphin\Lms\BDLMS_QUESTION_CPT,
					'numberposts'  => -1,
					// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
					'meta_key'     => \BlueDolphin\Lms\META_KEY_IMPORT,
					// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
					'meta_value'   => (string) $id,
					'meta_compare' => '=',
					'fields'       => 'ids',
				)
			);

			$rows = count( $imported_question );

			$result = $wpdb->query( //phpcs:ignore.
				$wpdb->prepare(
					"UPDATE $table_name SET import_status = %d, total_rows = %d, success_rows = %d WHERE id = %d", //phpcs:ignore.
					$status,
					$rows,
					$rows,
					$id
				)
			);

			if ( false !== $result ) {
				EL::add( sprintf( 'File import status updated to failed: %d', $status ), 'info', __FILE__, __LINE__ );
				delete_transient( 'import_data' );
			}
		}

		if ( 'remove' === $data ) {
			if ( ! empty( $imported_question ) ) {
				foreach ( $imported_question as $question_id ) {
					wp_delete_post( $question_id, true );
				}
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
				EL::add( sprintf( 'Question deleted: %s, Question ID: %d', print_r( $imported_question, true ), $question_id ), 'info', __FILE__, __LINE__ );
			}
		}

		wp_send_json(
			array(
				'data' => 1,
			)
		);
	}
}
