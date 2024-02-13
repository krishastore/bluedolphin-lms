<?php
/**
 * The file that defines the user management functionality.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BlueDolphin\Lms\Admin
 */

namespace BlueDolphin\Lms\Admin\Users;

use const BlueDolphin\Lms\PARENT_MENU_SLUG;

/**
 * Register post types.
 */
class Users extends \BlueDolphin\Lms\Admin\Core {
	/**
	 * Init hooks.
	 */
	public function __construct() {
		new \BlueDolphin\Lms\Admin\Users\Capability();
		// Hooks.
		add_action( 'admin_menu', array( $this, 'register_submenu_page' ), 20 );
	}

	/**
	 * Register admin submenu page.
	 */
	public function register_submenu_page() {
		add_submenu_page(
			'bluedolphin-lms',
			__( 'User Role Editor', 'bluedolphin-lms' ),
			__( 'User Role Editor', 'bluedolphin-lms' ),
			apply_filters( 'bluedolphin/menu/capability', 'manage_options' ),
			'bdlms_manage_caps',
			array( $this, 'render_menu_page' )
		);
	}

	/**
	 * Render admin menu page.
	 */
	public function render_menu_page() {
		echo 'User Role Editor';
	}
}
