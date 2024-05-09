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
		add_action( 'template_redirect', array( $this, 'template_redirect' ) );
		add_action( 'bdlms_before_single_course', array( $this, 'fetch_course_data' ) );
		add_action( 'bdlms_after_single_course', array( $this, 'flush_course_data' ) );
		add_action( 'bdlms_single_course_action_bar', array( $this, 'single_course_action_bar' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'bdlms_after_single_course', array( $this, 'update_user_course_view_status' ), 15, 1 );
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
		// Plyr.
		wp_enqueue_script( $this->handler . '-plyr' );
		wp_enqueue_style( $this->handler . '-plyr' );
		// SmartWizard.
		wp_enqueue_script( $this->handler . '-smartwizard' );
		wp_enqueue_style( $this->handler . '-smartwizard' );
		// Frontend.
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
		$current_item = isset( $course_data['current_curriculum']['item_id'] ) ? $course_data['current_curriculum']['item_id'] : 0;
		load_template(
			\BlueDolphin\Lms\locate_template( 'action-bar.php' ),
			true,
			array(
				'course_id'    => $course_id,
				'curriculums'  => \BlueDolphin\Lms\merge_curriculum_items( $curriculums ),
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
		$curriculums                = array_map( '\BlueDolphin\Lms\get_curriculum_section_items', $curriculums );
		$current_curriculum         = \BlueDolphin\Lms\get_current_curriculum( $curriculums );
		$course_data['curriculums'] = $curriculums;
		if ( isset( $current_curriculum['media'] ) ) {
			$current_curriculum['media'] = array_filter( $current_curriculum['media'] );
		}
		$course_data['current_curriculum'] = $current_curriculum;
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
	 * Handle template redirect hook.
	 */
	public function template_redirect() {
		if ( ! is_user_logged_in() && is_singular( \BlueDolphin\Lms\BDLMS_COURSE_CPT ) ) {
			wp_safe_redirect( \BlueDolphin\Lms\get_page_url( 'login' ) );
			exit;
		}
		$this->set_404_page();
	}

	/**
	 * Set 404 page.
	 */
	public function set_404_page() {
		global $wp_query;
		$curriculum_type = get_query_var( 'curriculum_type', '' );
		if ( in_array( $curriculum_type, array( 'quiz', 'lesson' ), true ) ) {
			$item_id = (int) get_query_var( 'item_id', 0 );
			if ( ! get_post( $item_id ) ) {
				$wp_query->set_404();
			}
		}
	}

	/**
	 * Update current user course view status in metadata.
	 *
	 * @param int $course_id Course ID.
	 */
	public function update_user_course_view_status( $course_id ) {
		$meta_key = sprintf( \BlueDolphin\Lms\BDLMS_COURSE_STATUS, $course_id );
		$item_id  = get_query_var( 'curriculum_type' ) ? get_query_var( 'item_id' ) : 0;
		if ( is_user_logged_in() && $item_id ) {
			$section_id     = get_query_var( 'section' ) ? get_query_var( 'section' ) : 1;
			$item_id        = $section_id . '_' . $item_id;
			$user_id        = get_current_user_id();
			$current_status = get_user_meta( $user_id, $meta_key, true );
			if ( $current_status === $item_id ) {
				return;
			}
			update_user_meta( $user_id, $meta_key, $item_id );
		}
	}
}
