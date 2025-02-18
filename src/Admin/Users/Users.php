<?php
/**
 * The file that defines the user management functionality.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BD\Lms\Admin\Users
 */

namespace BD\Lms\Admin\Users;

use const BD\Lms\PARENT_MENU_SLUG;

/**
 * Users manage class.
 */
class Users extends \BD\Lms\Admin\Core implements \BD\Lms\Interfaces\AdminCore {

	/**
	 * Init hooks.
	 */
	public function __construct() {
		// Hooks.
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ), 20 );
	}
}
