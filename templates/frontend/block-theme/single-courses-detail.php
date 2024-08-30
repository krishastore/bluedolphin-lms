<?php
/**
 * Template: Course Details Page - Block Theme
 *
 * @package BlueDolphin\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
		<?php
		remove_filter( 'the_content', 'wpautop' );
		$header = apply_filters( 'the_content', $header );
		echo $header; // phpcs:ignore
		?>
	</header>
	<?php
	$course_id = get_the_ID();

	/**
	 * Before course content action.
	 *
	 * @param int $course_id Course ID
	 */
	do_action( 'bdlms_before_single_course', $course_id );

	global $bdlms_course_data;
	load_template(
		\BlueDolphin\Lms\locate_template( 'course-detail.php' ),
		true,
		array(
			'course_id'   => $course_id,
			'course_data' => $bdlms_course_data,
		)
	);

	/**
	 * After course content action.
	 *
	 * @param int $course_id Course ID
	 */
	do_action( 'bdlms_after_single_course', $course_id );
	?>
	<footer class="wp-block-template-part site-footer">
		<?php
		$footer = apply_filters( 'the_content', $footer );
		echo $footer; // phpcs:ignore
		add_filter( 'the_content', 'wpautop' );
		?>
	</footer>
</div>
<?php wp_footer(); ?>
</body>
</html>