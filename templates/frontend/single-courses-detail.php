<?php
/**
 * Template: Course Details Page
 *
 * @package BlueDolphin\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
$course_id = get_the_ID();

/**
 * Before course content action.
 *
 * @param int $course_id Course ID
 */
do_action( 'bdlms_before_single_course', $course_id );

global $course_data;
load_template(
	\BlueDolphin\Lms\locate_template( 'course-detail.php' ),
	true,
	array(
		'course_id'   => $course_id,
		'course_data' => $course_data,
	)
);

/**
 * After course content action.
 *
 * @param int $course_id Course ID
 */
do_action( 'bdlms_after_single_course', $course_id );

get_footer();
