<?php
/**
 * The file that manage the database related events.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BlueDolphin\Lms
 */

namespace BlueDolphin\Lms\Helpers;

use BlueDolphin\Lms\ErrorLog as EL;

/**
 * Helpers utility class.
 */
class Utility implements \BlueDolphin\Lms\Interfaces\Helpers {

	/**
	 * Default pages used by LP
	 *
	 * @var array
	 */
	private static $pages = array(
		'login',
		'courses',
		'term_conditions',
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
}
