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
		// Call `course-content.php` template part.
		load_template( \BlueDolphin\Lms\locate_template( 'course-content.php' ), true, array() );
	?>
	<footer class="wp-block-template-part site-footer">
		<?php echo $footer; // phpcs:ignore ?>
	</footer>
</div>
<?php wp_footer(); ?>
</body>
</html>