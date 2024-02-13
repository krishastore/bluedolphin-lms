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
 * @package    BlueDolphin\Lms
 */

namespace BlueDolphin\Lms;

use BlueDolphin\Lms\ErrorLog;
use BlueDolphin\Lms\Collections\PostTypes;
use BlueDolphin\Lms\Collections\Taxonomies;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 */
final class BlueDolphin {

	/**
	 * Define parent menu const.
	 */
	const PARENT_MENU_SLUG = 'bluedolphin-lms';

	/**
	 * Plugin version.
	 *
	 * @var int Plugin version. Default `BDLMS_VERSION`
	 * @since 1.0.0
	 */
	private $version = BDLMS_VERSION;

	/**
	 * Admin instance.
	 *
	 * @var object Admin class instance.
	 * @since 1.0.0
	 */
	private $admin_instance;

	/**
	 * The main instance var.
	 *
	 * @var BlueDolphin The one BlueDolphin instance.
	 * @since 1.0.0
	 */
	private static $instance;

	/**
	 * Core collections list.
	 *
	 * @var array $collections
	 */
	private $collections = array();

	/**
	 * Init the main singleton instance class.
	 *
	 * @return BlueDolphin Return the instance class
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof BlueDolphin ) ) {
			self::$instance = new BlueDolphin();
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
			'bluedolphin/collections',
			array(
				'PostTypes',
				'Taxonomies',
			)
		);
		$this->load_collections();
		$this->admin_instance = new \BlueDolphin\Lms\Admin\Core( $this->version, self::instance() );
	}

	/**
	 * Load collections.
	 */
	private function load_collections() {
		foreach ( $this->collections as $collection ) {
			$class_name = "\BlueDolphin\Lms\Collections\\$collection";
			if ( class_exists( $class_name ) ) {
				$class = new $class_name();
				$class->init();
			}
		}
	}
}
