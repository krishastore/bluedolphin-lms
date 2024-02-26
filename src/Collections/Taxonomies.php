<?php
/**
 * The file that register the taxonomies.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BlueDolphin\Lms
 *
 * phpcs:disable WordPress.NamingConventions.ValidHookName.UseUnderscores
 */

namespace BlueDolphin\Lms\Collections;

use const BlueDolphin\Lms\PARENT_MENU_SLUG;

/**
 * Register taxonomies.
 */
class Taxonomies {

	/**
	 * Taxonomies list.
	 *
	 * @var array $taxonomies
	 */
	private $taxonomies = array();

	/**
	 * Init hooks.
	 */
	public function init() {
		$this->register();
		add_action( 'parent_file', array( $this, 'filter_parent_file' ) );
		add_action( 'admin_menu', array( $this, 'register_submenu_page' ) );
	}

	/**
	 * Register taxonomies.
	 */
	private function register() {
		$this->taxonomies = apply_filters(
			'bluedolphin/collections/taxonomies',
			glob( plugin_dir_path( __FILE__ ) . '/taxonomies/*.php' )
		);
		if ( ! empty( $this->taxonomies ) ) {
			foreach ( $this->taxonomies as $path ) {
				if ( is_readable( $path ) ) {
					require $path;
				}
			}
		}
	}

	/**
	 * Filter parent file hook.
	 *
	 * @param string $parent_file Parent file slug.
	 * @return string
	 */
	public function filter_parent_file( $parent_file ) {
		global $current_screen;
		$taxonomy = $current_screen->taxonomy;
		if ( in_array( $taxonomy, array( 'bdlms_course_tag', 'bdlms_course_category' ), true ) ) {
			$parent_file = PARENT_MENU_SLUG;
		}
		return $parent_file;
	}

	/**
	 * Register submenu item.
	 */
	public function register_submenu_page() {
		add_submenu_page(
			PARENT_MENU_SLUG,
			__( 'Categories', 'bluedolphin-lms' ),
			__( 'Categories', 'bluedolphin-lms' ),
			apply_filters( 'bluedolphin/menu/capability', 'manage_options' ),
			'edit-tags.php?taxonomy=bdlms_course_category',
			'__return_null'
		);
		add_submenu_page(
			PARENT_MENU_SLUG,
			__( 'Tags', 'bluedolphin-lms' ),
			__( 'Tags', 'bluedolphin-lms' ),
			apply_filters( 'bluedolphin/menu/capability', 'manage_options' ),
			'edit-tags.php?taxonomy=bdlms_course_tag',
			'__return_null'
		);
	}
}
