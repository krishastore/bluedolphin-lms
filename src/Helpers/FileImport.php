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
			if ( 'In-Progress' === $data['import_status'] ) {
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
		$time          = date( 'Y-m-d H:i:s', time() ); //phpcs:ignore.
		$status        = 'In-Progress';
		$progress      = 0;
		$args          = array();
		$args_1        = '';
		$args_2        = '';

		// Table name.
		$table_name = $wpdb->prefix . 'bdlms_cron_jobs';
		// insert a new record in a table.
		$result = $wpdb->query( //phpcs:ignore.
			$wpdb->prepare(
				'INSERT INTO ' . $table_name . '(attachment_id, file_name	, progress, import_status, import_date ) VALUES (%d, %s, %d, %s, %s)', //phpcs:ignore.
				$attachment_id,
				$file_name,
				$progress,
				$status,
				$time
			)
		);

		if ( false !== $result ) {
			delete_transient( 'import_data' );

			$args_1    = $wpdb->insert_id;
			$args_2    = $attachment_id;
			$args      = array( $args_1, $args_2 );
			$cron_hook = 'bdlms_cron_import_' . $args_1;
			$run_time  = strtotime( '+2 minutes', strtotime( $time ) );

			if ( ! wp_next_scheduled( $cron_hook, $args ) ) {
				wp_schedule_single_event( $run_time, $cron_hook, $args );
			}
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
			$status        = 'Complete';
			$curr_progress = 0;

			// Count the total number of rows.
			foreach ( $reader->getSheetIterator() as $sheet ) {
				foreach ( $sheet->getRowIterator() as $key => $row ) {
					if ( 1 !== $key ) {
						++$total_rows;
					}
				}
			}

			foreach ( $reader->getSheetIterator() as $sheet ) {
				foreach ( $sheet->getRowIterator() as $key => $row ) {
					if ( 1 !== $key ) {
						$value    = $row->toArray();
						$terms    = explode( '|', $value[2] );
						$terms    = array_map( 'trim', $terms );
						$terms_id = array();

						foreach ( $terms as $_term ) {
							if ( term_exists( $_term, \BlueDolphin\Lms\BDLMS_QUESTION_TAXONOMY_TAG ) ) {
								$existing_term = get_term_by( 'name', $_term, \BlueDolphin\Lms\BDLMS_QUESTION_TAXONOMY_TAG );
								$terms_id[]    = $existing_term->term_id;
							} else {
								$terms      = wp_insert_term( $_term, \BlueDolphin\Lms\BDLMS_QUESTION_TAXONOMY_TAG );
								$terms_id[] = $terms['term_id'];
							}
						}

						$question = array(
							'post_title'   => $value[0],
							'post_content' => $value[1],
							'post_status'  => 'publish',
							'post_type'    => \BlueDolphin\Lms\BDLMS_QUESTION_CPT,
							'meta_input'   => array(
								\BlueDolphin\Lms\META_KEY_QUESTION_TYPE => $value[8],
								\BlueDolphin\Lms\META_KEY_QUESTION_SETTINGS => array(),
							),
						);

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
						} else {
							++$fail_cnt;
						}
					}

					// Calculate progress.
					$progress = ( $key / $total_rows ) * 100;

					if ( $progress >= 25 && $progress < 50 && $key % ( $total_rows / 4 ) === 0 ) {
						$curr_progress = 25;
					} elseif ( $progress >= 50 && $progress < 75 && $key % ( $total_rows / 2 ) === 0 ) {
						$curr_progress = 50;
					} elseif ( $progress >= 75 && $progress < 100 && $key % ( 3 * $total_rows / 4 ) === 0 ) {
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
							delete_transient( 'import_data' );
						}
					}
				}
			}
		}

		if ( $fail_cnt > ceil( $total_rows / 2 ) ) {
			$status        = 'Failed';
			$curr_progress = 0;
		}
			$result = $wpdb->query( //phpcs:ignore.
				$wpdb->prepare(
					"UPDATE $table_name SET import_status = %s, progress = %d, total_rows = %d, success_rows = %d, fail_rows = %d WHERE id = %d", //phpcs:ignore.
					$status,
					$curr_progress,
					$total_rows,
					$success_cnt,
					$fail_cnt,
					$args_1
				)
			);

		if ( false !== $result ) {
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
		$status        = 'Cancelled';

		if ( ! empty( $id ) && ! empty( $attachment_id ) ) {
			wp_clear_scheduled_hook( $cron_hook, array( $id, $attachment_id ) );

			$result = $wpdb->query( //phpcs:ignore.
				$wpdb->prepare(
					"UPDATE $table_name SET import_status = %s WHERE id = %d", //phpcs:ignore.
					$status,
					$id
				)
			);

			if ( false !== $result ) {
				delete_transient( 'import_data' );
			}
		}

		if ( 'remove' === $data ) {

			$imported_question = get_posts(
				array(
					'post_type'    => \BlueDolphin\Lms\BDLMS_QUESTION_CPT,
					'numberposts'  => -1,
					// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
					'meta_key'     => \BlueDolphin\Lms\META_KEY_IMPORT,
					// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
					'meta_value'   => $id,
					'meta_compare' => '=',
					'fields'       => 'ids',
				)
			);

			if ( ! empty( $imported_question ) ) {
				foreach ( $imported_question as $question_id ) {
					wp_delete_post( $question_id, true );
				}
			}
		}

		wp_send_json(
			array(
				'data' => 'Import Cancelled Successfuly',
			)
		);
	}
}
