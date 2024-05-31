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
?>
<div class="bdlms-wrap">
	<div class="bdlms-lesson-view active">
		<?php
		/**
		 * Action bar.
		 *
		 * @param int $course_id Course ID
		 */
		do_action( 'bdlms_single_course_action_bar', $course_id );
		?>
		<?php
		global $course_data;
		if ( ! empty( $course_data['current_curriculum'] ) ) {
			load_template(
				\BlueDolphin\Lms\locate_template( 'course-content.php' ),
				true,
				array(
					'course_id'   => $course_id,
					'course_data' => $course_data,
				)
			);
		} else {
			load_template(
				\BlueDolphin\Lms\locate_template( 'content-none.php' ),
				true
			);
		}
		?>
	</div>
</div>
<?php

/**
 * After course content action.
 *
 * @param int $course_id Course ID
 */
do_action( 'bdlms_after_single_course', $course_id );

get_footer();
