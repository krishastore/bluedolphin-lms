<?php
/**
 * The file that manage the database related events.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BD\Lms
 */

namespace BD\Lms\Helpers;

use BD\Lms\ErrorLog as EL;

/**
 * Helpers utility class.
 */
class Utility implements \BD\Lms\Interfaces\Helpers {

	/**
	 * Default pages used by LP
	 *
	 * @var array
	 */
	private static $pages = array(
		'login',
		'courses',
		'term_conditions',
		'my_learning',
	);

	/**
	 * On plugin activation hook.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function activation_hook() {
		self::create_default_roles();
		self::create_pages();
		self::bdlms_custom_table();
	}

	/**
	 * On plugin deactivation hook.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function deactivation_hook() {
		$pages = self::$pages;
		try {
			foreach ( $pages as $page ) {
				$option_key = "bdlms_{$page}_page_id";
				$page_id    = (int) get_option( $option_key, false );
				if ( empty( $page_id ) ) {
					continue;
				}
				wp_delete_post( $page_id, true );
				delete_option( $option_key );
			}
			delete_option( 'bdlms_permalinks_flushed' );
		} catch ( \Exception $ex ) {
			EL::add( $ex->getMessage() );
		}
	}

	/**
	 * Create default pages..
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function create_pages() {
		$pages = self::$pages;
		try {
			foreach ( $pages as $page ) {
				// Check if page has already existed.
				$page_id = get_option( "bdlms_{$page}_page_id", false );

				if ( $page_id && 'page' === get_post_type( $page_id ) && 'publish' === get_post_status( $page_id ) ) {
					continue;
				}

				if ( 'courses' === $page ) {
					$page_title = 'All Courses';
					$page_slug  = $page;
				} else {
					$page_title = ucwords( str_replace( '_', ' ', $page ) );
					$page_slug  = 'bdlms-' . str_replace( '_', '-', $page );
				}

				$data_create_page = array(
					'post_title' => $page_title,
					'post_name'  => $page_slug,
				);
				self::create_page( $data_create_page, "bdlms_{$page}_page_id" );
			}

			flush_rewrite_rules();
		} catch ( \Exception $ex ) {
			EL::add( $ex->getMessage() );
		}
	}

	/**
	 * Create LP static page.
	 *
	 * @param array  $args Custom args.
	 * @param string $key_option Global option key.
	 * @throws \Exception Errors.
	 *
	 * @return bool|int
	 */
	public static function create_page( $args = array(), $key_option = '' ) {
		$page_id = 0;

		try {
			if ( ! isset( $args['post_title'] ) ) {
				throw new \Exception( __( 'Missing post title', 'bluedolphin-lms' ) );
			}

			if ( preg_match( '#^bdlms_login_page_id.*#', $key_option ) ) {
				$args['post_content'] = '<!-- wp:shortcode -->[bdlms_login]<!-- /wp:shortcode -->';
			} elseif ( preg_match( '#^bdlms_courses_page_id.*#', $key_option ) ) {
				$args['post_content'] = '<!-- wp:shortcode -->[bdlms_courses filter="yes" pagination="yes"]<!-- /wp:shortcode -->';
			} elseif ( preg_match( '#^bdlms_my_learning_page_id.*#', $key_option ) ) {
				$args['post_content'] = '<!-- wp:shortcode -->[bdlms_my_learning filter="yes" pagination="yes"]<!-- /wp:shortcode -->';
			}

			$args = array_merge(
				array(
					'post_title'     => '',
					'post_name'      => '',
					'post_status'    => 'publish',
					'post_type'      => 'page',
					'comment_status' => 'closed',
					'post_content'   => '',
					'post_author'    => get_current_user_id(),
				),
				$args
			);

			$page_id = wp_insert_post( $args );

			if ( ! is_int( $page_id ) ) {
				return 0;
			}

			update_option( $key_option, $page_id );
		} catch ( \Throwable $e ) {
			EL::add( __METHOD__ . ': ' . $e->getMessage() );
		}

		return $page_id;
	}

	/**
	 * Create default roles.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function create_default_roles() {
		$capabilities = get_role( 'subscriber' );
		add_role(
			'bdlms',
			esc_html__( 'BlueDolphin LMS', 'bluedolphin-lms' ),
			$capabilities->capabilities
		);
	}

	/**
	 * Create a table to store cron data.
	 *
	 * @throws \Exception Errors.
	 */
	public static function bdlms_custom_table() {
		global $wpdb;

		// Define the custom table name.
		$table_name = $wpdb->prefix . \BD\Lms\BDLMS_CRON_TABLE;

		// Check if the table already exists.
		if ( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) ) ) {

			$_charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
				id INT(11)  NOT NULL AUTO_INCREMENT,
				attachment_id INT(11)  NOT NULL,
				file_name VARCHAR(255) NOT NULL,
				progress INT(11)  NOT NULL,
				import_status INT(11)  NOT NULL,
				import_type INT(11) NOT NULL,
				total_rows INT(11)  NOT NULL,
				success_rows INT(11)  NOT NULL,
				fail_rows INT(11)  NOT NULL,
				import_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id)
			) $_charset_collate;";

			// Include the WordPress database upgrade script.
			require_once ABSPATH . '/wp-admin/includes/upgrade.php';

			// Create or update the table.
			dbDelta( $sql );
		}
	}
}
