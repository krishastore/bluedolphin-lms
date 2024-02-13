<?php
/**
 * Declare the interface for `BlueDolphin\Lms\PostTypes` class.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BlueDolphin\Lms
 */

namespace BlueDolphin\Lms\Interfaces;

interface PostTypes {

	/**
	 * Init hooks.
	 */
	public function init();

	/**
	 * Register post types.
	 */
	public function register();
}
