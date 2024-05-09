<?php
/**
 * Template: Course Details Page - Block Theme
 *
 * @package BlueDolphin\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<?php
		ob_start();
		block_header_area();
		$header = ob_get_clean();
	?>
	<?php
		ob_start();
		block_footer_area();
		$footer = ob_get_clean();
	?>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="wp-site-blocks">
	<header class="wp-block-template-part site-header">
		<?php echo $header; // phpcs:ignore ?>
	</header>
	<?php
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
		?>
	<footer class="wp-block-template-part site-footer">
		<?php echo $footer; // phpcs:ignore ?>
	</footer>
</div>
<?php wp_footer(); ?>
</body>
</html>