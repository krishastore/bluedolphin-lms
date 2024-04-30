<?php
/**
 * The file that defines the login shortcode functionality.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BlueDolphin\Lms\Shortcode
 */

namespace BlueDolphin\Lms\Shortcode;

/**
 * Shortcode register manage class.
 */
class Login extends \BlueDolphin\Lms\Shortcode\Register {

	/**
	 * Init.
	 */
	public function init() {
		$this->shortcode_tag = 'bdlms_login';
	}

	/**
	 * Register shortcode.
	 */
	public function register_shortcode() {
		wp_enqueue_script( $this->handler );
		wp_enqueue_style( $this->handler );
		ob_start();
		load_template( \BlueDolphin\Lms\locate_template( 'login.php' ), false, array() );
		$content = ob_get_clean();
		return $content;
	}
}
