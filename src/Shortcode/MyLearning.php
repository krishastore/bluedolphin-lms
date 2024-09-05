<?php
/**
 * The file that defines the my learning shortcode functionality.
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
class MyLearning extends \BlueDolphin\Lms\Shortcode\Register implements \BlueDolphin\Lms\Interfaces\MyLearning {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->set_shortcode_tag( 'my_learning' );
		$this->init();
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
		load_template( \BlueDolphin\Lms\locate_template( 'mylearning.php' ), false, $args );
		$content = ob_get_clean();
		return $content;
	}
}
