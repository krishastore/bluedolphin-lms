<?php
/**
 * The file that defines the shortcode register functionality.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BlueDolphin\Lms\Shortcode
 */

namespace BlueDolphin\Lms\Shortcode;

use const BlueDolphin\Lms\BDLMS_SCRIPT_PREFIX;

/**
 * Shortcode register manage class.
 */
abstract class Register {

	/**
	 * Store capability list class object.
	 *
	 * @var object|null $capability_list
	 * @since 1.0.0
	 */
	private $shortcode_list = array();

	/**
	 * Init hooks.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		$this->init();
	}

	/**
	 * Register frontend scripts.
	 */
	public function enqueue_scripts() {
		wp_register_script( BDLMS_SCRIPT_PREFIX . 'frontend', BDLMS_ASSETS . '/js/build/frontend.js', array( 'jquery' ), bdlms_run()->get_version(), true );
		wp_register_style( BDLMS_SCRIPT_PREFIX . 'frontend', BDLMS_ASSETS . '/js/build/frontend.js', array( 'jquery' ), bdlms_run()->get_version() );
	}
}
