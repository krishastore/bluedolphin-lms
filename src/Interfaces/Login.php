<?php
/**
 * Declare the interface for `BD\Lms\Shortcode\Login` class.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BD\Lms
 */

namespace BD\Lms\Interfaces;

interface Login {

	/**
	 * Main construct.
	 */
	public function __construct();

	/**
	 * Init.
	 */
	public function init();

	/**
	 * Register shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 */
	public function register_shortcode( $atts );

	/**
	 * Login process.
	 */
	public function login_process();

	/**
	 * Register frontend scripts.
	 */
	public function enqueue_scripts();
}
