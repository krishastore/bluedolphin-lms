<?php
/**
 * The file that defines the courses shortcode functionality.
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
		add_filter( 'template_include', array( $this, 'courses_single_page' ) );
		add_action( 'template_redirect', array( $this, 'redirect_to_login_page' ) );
		add_action( 'bdlms_before_single_course', array( $this, 'fetch_course_data' ) );
		add_action( 'bdlms_after_single_course', array( $this, 'flush_course_data' ) );
		add_action( 'bdlms_single_course_action_bar', array( $this, 'single_course_action_bar' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
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

	/**
	 * Filter courses single page template.
	 *
	 * @param string $template Template path.
	 * @return string
	 */
	public function courses_single_page( $template ) {
		if ( is_singular( \BlueDolphin\Lms\BDLMS_COURSE_CPT ) ) {
			$prefix = '';
			if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {
				$prefix = 'block-theme-';
			}
			$template = \BlueDolphin\Lms\locate_template( $prefix . 'single-courses.php' );
		}
		return $template;
	}

	/**
	 * Enqueue scripts.
	 */
	public function enqueue_scripts() {
		if ( ! is_singular( \BlueDolphin\Lms\BDLMS_COURSE_CPT ) ) {
			return;
		}
		wp_enqueue_script( $this->handler );
		wp_enqueue_style( $this->handler );
	}

	/**
	 * Action bar.
	 *
	 * @param int $course_id Course ID.
	 */
	public function single_course_action_bar( $course_id ) {
		global $course_data;
		$curriculums  = isset( $course_data['curriculums'] ) ? $course_data['curriculums'] : array();
		$curriculums  = \BlueDolphin\Lms\get_curriculums( $curriculums, '' );
		$current_item = reset( $curriculums );
		load_template(
			\BlueDolphin\Lms\locate_template( 'action-bar.php' ),
			true,
			array(
				'course_id'    => $course_id,
				'curriculums'  => $curriculums,
				'current_item' => $current_item,
			)
		);
	}

	/**
	 * Fetch course data.
	 *
	 * @param int $course_id Course ID.
	 */
	public function fetch_course_data( $course_id ) {
		global $course_data;
		$curriculums                = get_post_meta( $course_id, \BlueDolphin\Lms\META_KEY_COURSE_CURRICULUM, true );
		$curriculums                = ! empty( $curriculums ) ? $curriculums : array();
		$course_data['curriculums'] = $curriculums;
	}

	/**
	 * Flush current course data.
	 */
	public function flush_course_data() {
		global $course_data;
		if ( apply_filters( 'bdlms_flush_course_data', true ) ) {
			$course_data = array();
		}
	}

	/**
	 * Force a redirect to the login page if a user is not logged in.
	 */
	public function redirect_to_login_page() {
		if ( ! is_user_logged_in() && is_singular( \BlueDolphin\Lms\BDLMS_COURSE_CPT ) ) {
			wp_safe_redirect( \BlueDolphin\Lms\get_page_url( 'login' ) );
			exit;
		}
	}
}
