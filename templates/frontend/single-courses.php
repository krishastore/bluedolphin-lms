<?php
/**
 * Template: Course Details Page
 *
 * @package BlueDolphin\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended
 */

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
	<div class="bdlms-lesson-view">
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
			load_template(
				\BlueDolphin\Lms\locate_template( 'course-content.php' ),
				true,
				array(
					'course_id'   => $course_id,
					'course_data' => $course_data,
				)
			);
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
