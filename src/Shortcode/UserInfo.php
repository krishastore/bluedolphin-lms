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
	 * Class constructor.
	 */
	public function __construct() {
		$this->set_shortcode_tag( 'bdlms_userinfo' );
		$this->init();
	}
	/**
	 * Register shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 */
	public function register_shortcode( $atts ) {
		wp_print_scripts( $this->handler );
		wp_print_styles( $this->handler );
		ob_start();
		load_template( \BlueDolphin\Lms\locate_template( 'userinfo.php' ), false, array() );
		$content = ob_get_clean();
		return $content;
	}
}
