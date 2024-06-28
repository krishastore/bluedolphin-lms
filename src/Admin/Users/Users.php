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
	 * Init hooks.
	 */
	public function __construct() {
		// Hooks.
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ), 20 );
	}
}
