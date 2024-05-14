<?php
/**
 * The file that defines the user info shortcode functionality.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BlueDolphin\Lms\Shortcode
 */

namespace BlueDolphin\Lms\Shortcode;

use BlueDolphin\Lms\ErrorLog as EL;
/**
 * Shortcode register manage class.
 */
class UserInfo extends \BlueDolphin\Lms\Shortcode\Register {

	/**
	 * Init.
	 */
	public function init() {
		$this->shortcode_tag = 'bdlms_userinfo';
	}
	/**
	 * Register shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 */
	public function register_shortcode( $atts ) {
		wp_enqueue_script( $this->handler );
		wp_enqueue_style( $this->handler );
		ob_start();
		load_template( \BlueDolphin\Lms\locate_template( 'userinfo.php' ), false, array() );
		$content = ob_get_clean();
		return $content;
	}
}
