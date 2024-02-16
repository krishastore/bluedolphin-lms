<?php
/**
 * The file that defines the user management functionality.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BlueDolphin\Lms\Admin\Users
 */

namespace BlueDolphin\Lms\Admin\Users;

use const BlueDolphin\Lms\PARENT_MENU_SLUG;

/**
 * Users manage class.
 */
class Users extends \BlueDolphin\Lms\Admin\Core implements \BlueDolphin\Lms\Interfaces\AdminCore {

	/**
	 * Store capability list class object.
	 *
	 * @var object|null $capability_list
	 * @since 1.0.0
	 */
	private $capability_list = null;

	/**
	 * Init hooks.
	 */
	public function __construct() {
		// Hooks.
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ), 20 );
	}

	/**
	 * Register admin submenu page.
	 */
	public function register_admin_menu() {
		$hook = add_submenu_page(
			'bluedolphin-lms',
			__( 'User Role Editor', 'bluedolphin-lms' ),
			__( 'User Role Editor', 'bluedolphin-lms' ),
			apply_filters( 'bluedolphin/menu/capability', 'manage_options' ),
			'bdlms_manage_caps',
			array( $this, 'render_menu_page' )
		);
		add_action( "load-$hook", array( $this, 'load_menu_page' ) );
	}

	/**
	 * Loan submenu page..
	 */
	public function load_menu_page() {
		// Include WP_List_Table class file.
		require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
		// Call the required model.
		$this->capability_list = new \BlueDolphin\Lms\Admin\Users\CapabilityList();
	}

	/**
	 * Render admin menu page.
	 */
	public function render_menu_page() {
		require_once BDLMS_TEMPLATEPATH . '/admin/capability-list.php';
	}
}
