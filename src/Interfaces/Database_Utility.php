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

interface Database_Utility {

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
}
