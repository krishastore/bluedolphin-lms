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

use const BlueDolphin\Lms\BDLMS_SCRIPT_HANDLE;

/**
 * Shortcode register manage class.
 */
abstract class Register {

	/**
	 * Shortcode tagName.
	 *
	 * @var string $shortcode_tag
	 * @since 1.0.0
	 */
	public $shortcode_tag = '';

	/**
	 * Script/Style handler.
	 *
	 * @var string $handler Handler.
	 */
	public $handler = BDLMS_SCRIPT_HANDLE . 'frontend';

	/**
	 * Init hooks.
	 */
	public function __construct() {
		$this->init();
		// Calling hooks.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		if ( ! shortcode_exists( $this->shortcode_tag ) ) {
			add_shortcode( $this->shortcode_tag, array( $this, 'register_shortcode' ) );
		}
	}

	/**
	 * Register frontend scripts.
	 */
	public function enqueue_scripts() {
		wp_register_script( $this->handler, BDLMS_ASSETS . '/js/build/frontend.js', array( 'jquery' ), bdlms_run()->get_version(), true );
		wp_register_style( $this->handler, BDLMS_ASSETS . '/css/frontend.css', array(), bdlms_run()->get_version() );
	}
}
