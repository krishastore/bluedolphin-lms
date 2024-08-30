<?php
/**
 * Template: Course Result Page - Block Theme
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
		// No need to use escaping for this variable as it is already escaped from `block_header_area();`.
		echo $header; // phpcs:ignore
		?>
	</header>

	<?php
	load_template(
		\BlueDolphin\Lms\locate_template( 'course-result.php' ),
		true
	);
	?>

	<footer class="wp-block-template-part site-footer">
		<?php
		$footer = apply_filters( 'the_content', $footer );
		// No need to use escaping for this variable as it is already escaped from `block_footer_area();`.
		echo $footer; // phpcs:ignore
		add_filter( 'the_content', 'wpautop' );
		wp_footer();
		?>
	</footer>
</div>
</body>
</html>