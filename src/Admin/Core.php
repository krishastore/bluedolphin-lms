<?php
/**
 * The file that defines the admin plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BlueDolphin\Lms\Admin
 */

namespace BlueDolphin\Lms\Admin;

use const BlueDolphin\Lms\PARENT_MENU_SLUG;

/**
 * Admin class
 */
class Core {

	/**
	 * Plugin version.
	 *
	 * @var int Plugin version.
	 * @since 1.0.0
	 */
	public $version;

	/**
	 * The main instance.
	 *
	 * @var BlueDolphin Main class instance.
	 * @since 1.0.0
	 */
	public $instance;

	/**
	 * Calling class construct.
	 *
	 * @param string $version Plugin version.
	 * @param object $instance Plugin main instance.
	 */
	public function __construct( $version, \BlueDolphin\Lms\BlueDolphin $instance ) { // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint
		$this->version  = $version;
		$this->instance = $instance;

		// Load modules.
		new \BlueDolphin\Lms\Admin\Users\Users();

		// Hooks.
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
	}

	/**
	 * Register admin menu.
	 */
	public function register_admin_menu() {
		$hook = add_menu_page(
			__( 'BlueDolphin LMS', 'bluedolphin-lms' ),
			__( 'BlueDolphin LMS', 'bluedolphin-lms' ),
			apply_filters( 'bluedolphin/menu/capability', 'manage_options' ),
			PARENT_MENU_SLUG,
			array( $this, 'admin_page_render' ),
			'dashicons-welcome-learn-more',
			apply_filters( 'bluedolphin/menu/position', 4 )
		);
	}

	/**
	 * Render admin page.
	 */
	public function admin_page_render() {
		echo 'main page';
	}
}
