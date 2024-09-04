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

interface MyLearning {

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
	 * Enqueue scripts.
	 */
	public function enqueue_scripts();
}
