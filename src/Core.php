<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BD\Lms
 *
 * phpcs:disable WordPress.NamingConventions.ValidHookName.UseUnderscores
 */

namespace BD\Lms;

use BD\Lms\Collections\PostTypes as RegisterPostType;
use BD\Lms\Collections\Taxonomies as RegisterTaxonomies;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 */
final class Core {

	/**
	 * Plugin version.
	 *
	 * @var int|string Plugin version. Default `BDLMS_VERSION`
	 * @since 1.0.0
	 */
	private $version = BDLMS_VERSION;

	/**
	 * The main instance var.
	 *
	 * @var Core|null The one Core instance.
	 * @since 1.0.0
	 */
	private static $instance = null;

	/**
	 * Core collections list.
	 *
	 * @var array $collections
	 */
	private $collections = array();

	/**
	 * Init the main singleton instance class.
	 *
	 * @return Core Return the instance class
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new Core();
		}

		return self::$instance;
	}

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function init() {
		$this->collections = apply_filters(
			'bdlms/collections',
			array(
				new RegisterPostType(),
				new RegisterTaxonomies(),
			)
		);
		$this->load_collections();
		$admin_instance = new \BD\Lms\Admin\Core( $this->version, self::instance() );
	}

	/**
	 * Load collections.
	 */
	private function load_collections() {
		foreach ( $this->collections as $collection ) {
			if ( is_callable( array( $collection, 'init' ) ) ) {
				$collection->init();
			}
		}
	}

	/**
	 * Get plugin version.
	 */
	public function get_version() {
		return $this->version;
	}
}
