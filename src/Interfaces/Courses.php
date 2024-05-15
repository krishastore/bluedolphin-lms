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

interface Courses {

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

	/**
	 * Fetch course data.
	 *
	 * @param int $course_id Course ID.
	 */
	public function fetch_course_data( $course_id );

	/**
	 * Flush current course data.
	 */
	public function flush_course_data();

	/**
	 * Filter courses single page template.
	 *
	 * @param string $template Template path.
	 * @return string
	 */
	public function courses_single_page( $template );
}
