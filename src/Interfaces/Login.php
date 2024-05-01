<?php
/**
 * Declare the interface for `BlueDolphin\Lms\Shortcode\Login` class.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BlueDolphin\Lms
 */

namespace BlueDolphin\Lms\Interfaces;

interface Login {

	/**
	 * Init.
	 */
	public function init();

	/**
	 * Register shortcode.
	 */
	public function register_shortcode();

	/**
	 * Login process.
	 */
	public function login_process();

	/**
	 * Register frontend scripts.
	 */
	public function enqueue_scripts();
}
