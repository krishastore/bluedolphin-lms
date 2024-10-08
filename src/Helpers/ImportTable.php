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

if ( ! class_exists( '\WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/screen.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
/**
 * To create a table to your needs you have to derive a class from WP_List_Table..
 */
class ImportTable extends \WP_List_Table {

	/**
	 * The main instance var.
	 *
	 * @var ImportTable|null $instance The one SettingOptions instance.
	 * @since 1.0.0
	 */
	private static $instance = null;

	/**
	 * Init the main singleton instance class.
	 *
	 * @return ImportTable Return the instance class
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new ImportTable();
		}
		return self::$instance;
	}

	/**
	 * Global import log.
	 *
	 * @var array $import_log
	 */
	protected $import_log = array();

	/**
	 * Calling class construct.
	 */
	public function __construct() {
		// Set parent defaults.
		parent::__construct(
			array(
				'singular' => __( 'Import', 'bluedolphin-lms' ),
				'plural'   => __( 'Imports', 'bluedolphin-lms' ),
				'ajax'     => false,
			)
		);

		$this->import_log = \BlueDolphin\Lms\fetch_import_data();

		add_action( 'admin_head', array( &$this, 'admin_header' ) );
	}

	/**
	 * Shows the text when import log table has no data.
	 */
	public function no_items() {
		esc_html_e( 'No Import Log found.', 'bluedolphin-lms' );
	}

	/**
	 * Get a list of sortable columns.
	 *
	 * @return array An associative array containing all the columns that should be sortable.
	 */
	protected function get_sortable_columns() {
		$sortable_columns = array(
			'title' => array( 'file_name', false ),
		);
		return $sortable_columns;
	}

	/**
	 * The 'cb' column is treated differently than the rest. If including a checkbox
	 * column in your table you must create a `column_cb()` method. If you don't need
	 * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
	 *
	 * @see WP_List_Table::::single_row_columns()
	 * @return array An associative array containing column information.
	 */
	public function get_columns() {
		$columns = array(
			'cb'       => '<input type="checkbox" />',
			'title'    => __( 'Log Name', 'bluedolphin-lms' ),
			'type'     => __( 'Import Type', 'bluedolphin-lms' ),
			'progress' => __( 'Progress', 'bluedolphin-lms' ),
			'status'   => __( 'Status', 'bluedolphin-lms' ),
			'date'     => __( 'Date', 'bluedolphin-lms' ),
			'action'   => __( 'Actions', 'bluedolphin-lms' ),
		);
		return $columns;
	}

	/**
	 * Custom columns must be provided by the developer and can be used to handle each type column individually.
	 *
	 * @see WP_List_Table::::single_row_columns()
	 * @param array $item is array of import data.
	 * @return string An action column button.
	 */
	public function column_action( $item ) {
		if ( 1 === (int) $item['import_status'] ) {
			$item = '<a href="javascript:;" data-id="' . (int) $item['id'] . '" data-fileId="' . (int) $item['attachment_id'] . '" data-import="' . $item['import_type'] . '" class="bdlms-bulk-import-cancel">' . __( 'Cancel', 'bluedolphin-lms' ) . '</a> | <a href="javascript:;" data-id="' . (int) $item['id'] . '" data-status="' . (int) $item['import_status'] . '"  data-file="' . esc_html( $item['file_name'] ) . '" data-path="' . wp_get_attachment_url( $item['attachment_id'] ) . '" data-date="' . date_i18n( 'Y-m-d', strtotime( $item['import_date'] ) ) . '" data-progress="' . (int) $item['progress'] . '" data-total="' . (int) $item['total_rows'] . '" data-success="' . (int) $item['success_rows'] . '" data-fail="' . (int) $item['fail_rows'] . '" class="bdlms-bulk-import">' . __( 'View', 'bluedolphin-lms' ) . '</a>';
		} else {
			$item = '<a href="javascript:;" data-id="' . (int) $item['id'] . '" data-status="' . (int) $item['import_status'] . '" data-import="' . $item['import_type'] . '" data-file="' . esc_html( $item['file_name'] ) . '" data-path="' . wp_get_attachment_url( $item['attachment_id'] ) . '" data-date="' . date_i18n( 'Y-m-d', strtotime( $item['import_date'] ) ) . '" data-progress="' . (int) $item['progress'] . '" data-total="' . (int) $item['total_rows'] . '" data-success="' . (int) $item['success_rows'] . '" data-fail="' . (int) $item['fail_rows'] . '" class="bdlms-bulk-import">' . __( 'View', 'bluedolphin-lms' ) . '</a>';
		}
		return $item;
	}

	/**
	 * Callback to allow sorting of example data.
	 *
	 * @param string $a First value.
	 * @param string $b Second value.
	 *
	 * @return int
	 */
	protected function usort_reorder( $a, $b ) {
		// If no sort, default to title.
		$orderby = ! empty( $_REQUEST['orderby'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['orderby'] ) ) : 'id'; //phpcs:ignore WordPress.Security.NonceVerification.Recommended

		// If no order, default to asc.
		$order = ! empty( $_REQUEST['order'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['order'] ) ) : 'desc'; //phpcs:ignore WordPress.Security.NonceVerification.Recommended

		// Determine sort order.
		$result = strcmp( $a[ $orderby ], $b[ $orderby ] ); // @phpstan-ignore-line

		return ( 'asc' === $order ) ? $result : - $result;
	}

	/**
	 * Bulk Actions.
	 *
	 * @return array An associative array containing all the bulk actions.
	 */
	protected function get_bulk_actions() {
		$actions = array(
			'delete' => __( 'Delete', 'bluedolphin-lms' ),
		);
		return $actions;
	}

	/**
	 * Handle bulk actions.
	 *
	 * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
	 * For this example package, we will handle it in the class to keep things
	 * clean and organized.
	 *
	 * @see $this->prepare_items()
	 */
	protected function process_bulk_action() {
		global $wpdb;
		$table_name = $wpdb->prefix . \BlueDolphin\Lms\BDLMS_CRON_TABLE;

		// security check!
		if ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) {

			$nonce  = sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) );
			$action = 'bulk-' . $this->_args['plural'];

			if ( ! wp_verify_nonce( $nonce, $action ) ) {
				EL::add( 'Failed nonce verification', 'error', __FILE__, __LINE__ );
				return;
			}
		}

		if ( 'delete' === $this->current_action() ) {
			$ids = isset( $_REQUEST['id'] ) ? array_filter( array_map( 'intval', $_REQUEST['id'] ) ) : array();
			if ( is_array( $ids ) ) {
				$placeholders = implode( ', ', array_fill( 0, count( $ids ), '%d' ) );
			}

			if ( ! empty( $ids ) ) {

				$result = $wpdb->query( $wpdb->prepare( "DELETE FROM $table_name WHERE id IN ($placeholders)", $ids) ); //phpcs:ignore
				if ( false !== $result ) {
					delete_transient( 'bdlms_import_data' );
					// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
					EL::add( sprintf( 'Import log deleted, Deleted ids:- %s', implode( ',', $ids ) ), 'info', __FILE__, __LINE__ );
				}
			}
		}
	}

	/**
	 * Get value for checkbox column.
	 *
	 * @param object $item A singular item (one full row's worth of data).
	 * @return string Text to be placed inside the column <td>.
	 */
	protected function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="id[]" value="%s" />',
			$item['id']
		);
	}

	/**
	 * Gets the filter view of tables.
	 *
	 * @return array filter views to be placed below the title<td>.
	 */
	protected function get_views() {

		$views     = array();
		$current   = ! empty( $_REQUEST['status'] ) ? (int) $_REQUEST['status'] : 'all'; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$cnt_class = 'count';
		$status    = \Bluedolphin\Lms\import_job_status();
		$cnt       = array_count_values( array_column( \BlueDolphin\Lms\fetch_import_data( 0, true ), 'import_status' ) );
		$cnt_all   = array_sum( $cnt );

		$class   = ( 'all' === $current ? 'current' : '' );
		$all_url = remove_query_arg( 'status' );

		$views['all'] = sprintf(
			__( '<a href="%1$s" class="%2$s">All <span class="%3$s">(%4$s)</span></a>', 'bluedolphin-lms' ), //phpcs:ignore.
			esc_url( $all_url ),
			esc_attr( $class ),
			esc_attr( $cnt_class ),
			$cnt_all
		);

		foreach ( $status as $key => $value ) {
			$count           = ! empty( $cnt[ $key ] ) ? (int) $cnt[ $key ] : 0;
			$url             = add_query_arg( 'status', $key );
			$class           = ( $key === $current ? 'current' : '' );
			$views[ $value ] = sprintf(
				__( '<a href="%1$s" class="%2$s">%3$s <span class="%4$s">(%5$d)</span></a>', 'bluedolphin-lms' ), //phpcs:ignore.
				esc_url( $url ),
				esc_attr( $class ),
				esc_html( $value ),
				esc_attr( $cnt_class ),
				$count
			);

		}

		return $views;
	}

	/**
	 * Prepares the list of items for displaying.
	 *
	 * REQUIRED! This is where you prepare your data for display. This method will
	 * usually be used to query the database, sort and filter the data, and generally
	 * get it ready to be displayed. At a minimum, we should set $this->items and
	 * $this->set_pagination_args(), although the following properties and methods
	 * are frequently interacted with here.
	 *
	 * @global wpdb $wpdb
	 * @uses $this->items
	 * @uses $this->get_columns()
	 * @uses $this->get_sortable_columns()
	 * @uses $this->get_pagenum()
	 * @uses $this->set_pagination_args()
	 */
	public function prepare_items() {
		global $wpdb;

		$table_name            = $wpdb->prefix . \BlueDolphin\Lms\BDLMS_CRON_TABLE;
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->process_bulk_action();

		usort( $this->import_log, array( $this, 'usort_reorder' ) );

		if ( ! empty( $_REQUEST['s'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$search           = sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$this->import_log = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE `file_name` LIKE '%%%s%%'", $wpdb->esc_like( $search ) ), ARRAY_A ); //phpcs:ignore.
		}

		$data         = '';
		$per_page     = (int) get_user_meta( get_current_user_id(), 'imports_per_page', true );
		$per_page     = ! empty( $per_page ) ? $per_page : 10;
		$current_page = $this->get_pagenum();
		$total_items  = count( $this->import_log );

		$data = array_slice( $this->import_log, ( ( $current_page - 1 ) * $per_page ), $per_page );

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
			)
		);

		$this->items = $data;
	}

	/**
	 * For more detailed insight into how columns are handled, take a look at
	 * WP_List_Table::single_row_columns()
	 *
	 * @param object $item        A singular item (one full row's worth of data).
	 * @param string $column_name The name/slug of the column to be processed.
	 * @return string Text or HTML to be placed inside the column <td>.
	 */
	protected function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'title':
				return $item['file_name'];
			case 'type':
				$import  = \Bluedolphin\Lms\import_post_type();
				$db_type = $item['import_type'];
				$type    = array_key_exists( $db_type, $import ) ? ucfirst( str_replace( 'bdlms_', '', $import[ $db_type ] ) ) : '';
				return $type;
			case 'progress':
				return $item['progress'] . '%';
			case 'status':
				$status    = \Bluedolphin\Lms\import_job_status();
				$db_status = $item['import_status'];
				return array_key_exists( $db_status, $status ) ? $status[ $db_status ] : '';
			case 'date':
				return date_i18n( 'Y-m-d', strtotime( $item['import_date'] ) );
			default:
				return ''; // Show the whole array for troubleshooting purposes.
		}
	}
}
