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

use BlueDolphin\Lms\ErrorLog as EL;
/**
 * Shortcode register manage class.
 */
class Courses extends \BlueDolphin\Lms\Shortcode\Register implements \BlueDolphin\Lms\Interfaces\Courses {

	/**
	 * Init.
	 */
	public function init() {
		$this->shortcode_tag = 'bdlms_courses';
	}

	/**
	 * Register shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 */
	public function register_shortcode( $atts ) {
		wp_enqueue_script( $this->handler );
		wp_enqueue_style( $this->handler );
		$args = shortcode_atts(
			array(
				'filter'     => 'yes',
				'pagination' => 'yes',
			),
			$atts,
			$this->shortcode_tag
		);
		ob_start();
		load_template( \BlueDolphin\Lms\locate_template( 'courses.php' ), false, $args );
		$content = ob_get_clean();
		return $content;
	}
}
