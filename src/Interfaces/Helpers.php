<?php
/**
 * Declare the interface for Database utility class.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BlueDolphin\Lms
 */

namespace BlueDolphin\Lms\Interfaces;

interface Helpers {

	/**
	 * On plugin activation hook.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function activation_hook();

	/**
	 * On plugin deactivation hook.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function deactivation_hook();

	/**
	 * Create default pages..
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function create_pages();

	/**
	 * Create LP static page.
	 *
	 * @param array  $args Custom args.
	 * @param string $key_option Global option key.
	 * @throws \Exception Errors.
	 *
	 * @return bool|int|WP_Error
	 */
	public static function create_page( $args = array(), $key_option = '' );
}
