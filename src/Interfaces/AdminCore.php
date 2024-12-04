<?php
/**
 * Declare the interface for `BD\Lms\Admin\Core` class.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BD\Lms
 */

namespace BD\Lms\Interfaces;

interface AdminCore {

	/**
	 * Register admin menu.
	 */
	public function register_admin_menu();

	/**
	 * Render admin page.
	 */
	public function render_menu_page();
}
