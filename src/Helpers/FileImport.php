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

use BlueDolphin\Lms\BlueDolphin;
use BlueDolphin\Lms\ErrorLog as EL;
use BlueDolphin\Lms\Helpers\SettingOptions as Options;
use OpenSpout\Reader\Common\Creator\ReaderEntityFactory;

/**
 * Helpers utility class.
 */
abstract class FileImport {

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
	 * Taxonomy tag name.
	 *
	 * @var string $taxonomy_tag
	 */
	public $taxonomy_tag;

	/**
	 * Import type.
	 *
	 * @var int $import_type
	 */
	public $import_type;

	/**
	 * Import file header.
	 *
	 * @return array
	 */
	abstract public function file_header();

	/**
	 * Insert import data.
	 *
	 * @param array $value import file data.
	 *
	 * @return int
	 */
	abstract public function insert_import_data( $value );

	/**
	 * Init function.
	 */
	public function init() {
		add_action( 'wp_ajax_bdlms_get_file_attachment_id', array( $this, 'get_file_attachment_id' ) );
		add_action( 'wp_ajax_bdlms_get_import_cancel_data', array( $this, 'get_import_cancel_data' ) );
		add_action( 'init', array( $this, 'bdlms_schedule_cron_event' ) );
		add_action( 'admin_notices', array( $this, 'check_extension' ) );
	}

	/**
	 * Check extension is present or not.
	 */
	public function check_extension() {
		if ( ! extension_loaded( 'zip' ) && isset( $_GET['tab'] ) && 'bulk-import' === $_GET['tab'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$class   = 'notice notice-error inline is-dismissible';
			$message = __( 'Bluedolphin required PHP `zip` extension to run background process.', 'bluedolphin-lms' );

			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );

		}
	}

	/**
	 * Schedule cron event.
	 */
	public function bdlms_schedule_cron_event() {
		$cron_hook = '';

		$import_data = \BlueDolphin\Lms\fetch_import_data();

		foreach ( $import_data as $data ) {
			if ( 1 === (int) $data['import_status'] && $this->import_type === (int) $data['import_type'] ) {
				$cron_hook = 'bdlms_cron_import_' . $data['id'];

				add_action( $cron_hook, array( $this, 'import_data' ), 10, 2 );
			}
		}
	}

	/**
	 * Get file attachment id.
	 */
	public function get_file_attachment_id() {
		global $wpdb;

		check_ajax_referer( BDLMS_BASEFILE, '_nonce' );
		$attachment_id = isset( $_POST['attachment_id'] ) ? (int) $_POST['attachment_id'] : 0;
		$import_type   = isset( $_POST['import_type'] ) ? (int) $_POST['import_type'] : 0;
		$file_name     = basename( get_attached_file( $attachment_id ) );
		$status        = 1;
		$progress      = 0;
		$args          = array();

		// Table name.
		$table_name = $wpdb->prefix . 'bdlms_cron_jobs';
		// insert a new record in a table.
		$result = $wpdb->query( //phpcs:ignore.
			$wpdb->prepare(
				'INSERT INTO ' . $table_name . '(attachment_id, import_type, file_name, progress, import_status ) VALUES (%d, %d, %s, %d, %d)', //phpcs:ignore.
				$attachment_id,
				$import_type,
				$file_name,
				$progress,
				$status
			)
		);

		if ( false !== $result && $import_type && $attachment_id ) {
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
	 * Insert data.
	 *
	 * @param int $args_1 cron table id.
	 * @param int $args_2 attachment id.
	 */
	public function import_data( $args_1, $args_2 ) {

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
			$file_header   = $this->file_header();

			// Count the total number of rows.
			foreach ( $reader->getSheetIterator() as $sheet ) {
				foreach ( $sheet->getRowIterator() as $key => $row ) {
					if ( $key > 1 ) {
						++$total_rows;
					} else {
						$value = $row->toArray();

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
						$value        = $row->toArray();
						$value        = array_filter( $value );
						$terms_id     = array();
						$taxonomy_tag = $this->taxonomy_tag;
						$import_id    = 0;
						$post_type    = \BlueDolphin\Lms\import_post_type();

						if ( empty( $value[0] ) ) {
							continue;
						}

						$import_id = $this->insert_import_data( $value );

						if ( ! empty( $value[4] ) ) {
							$terms = explode( '|', $value[4] );
							$terms = array_map( 'trim', $terms );

							foreach ( $terms as $_term ) {
								if ( term_exists( $_term, $taxonomy_tag ) ) {
									$existing_term = get_term_by( 'name', $_term, $taxonomy_tag );
									$terms_id[]    = $existing_term->term_id;
								} else {
									$terms      = wp_insert_term( $_term, $taxonomy_tag );
									$terms_id[] = $terms['term_id'];
								}
							}
						}

						if ( $import_id ) {
							wp_set_post_terms( $import_id, $terms_id, $taxonomy_tag );
							update_post_meta( $import_id, \BlueDolphin\Lms\META_KEY_IMPORT, $args_1 );
							++$success_cnt;
							EL::add( sprintf( '%1$s: %2$s, %3$s ID: %4$d', $post_type[ $this->import_type ], get_the_title( $import_id ), $post_type[ $this->import_type ], $import_id ), 'info', __FILE__, __LINE__ );
						} else {
							++$fail_cnt;
							EL::add( sprintf( 'Failed to import:- %s', $value[0] ), 'error', __FILE__, __LINE__ );
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
		$import_type   = isset( $_POST['import_type'] ) ? (int) $_POST['import_type'] : '';
		$post_type     = \BlueDolphin\Lms\import_post_type();
		$cron_hook     = 'bdlms_cron_import_' . $id;
		$status        = 3;

		if ( ! empty( $id ) && ! empty( $attachment_id ) ) {
			wp_clear_scheduled_hook( $cron_hook, array( $id, $attachment_id ) );
			EL::add( sprintf( 'File import cancelled cleared the cron hook: %s', $cron_hook ), 'info', __FILE__, __LINE__ );

			$imported_data = get_posts(
				array(
					'post_type'    => $post_type[ $import_type ],
					'numberposts'  => -1,
					// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
					'meta_key'     => \BlueDolphin\Lms\META_KEY_IMPORT,
					// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
					'meta_value'   => (string) $id,
					'meta_compare' => '=',
					'fields'       => 'ids',
				)
			);

			$rows = count( $imported_data );

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
			if ( ! empty( $imported_data ) ) {
				foreach ( $imported_data as $data_id ) {
					wp_delete_post( $data_id, true );
				}
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
				EL::add( sprintf( '%1$s deleted: %2$s, %3$s ID: %4$d', $post_type[ $import_type ], print_r( $imported_data, true ), $post_type[ $import_type ], $data_id ), 'info', __FILE__, __LINE__ );
			}
		}

		wp_send_json(
			array(
				'data' => 1,
			)
		);
	}
}
