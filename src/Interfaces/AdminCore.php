<?php
/**
 * Declare the interface for `BlueDolphin\Lms\Admin\Core` class.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BlueDolphin\Lms
 */

namespace BlueDolphin\Lms\Interfaces;

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
