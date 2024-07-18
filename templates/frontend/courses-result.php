<?php
/**
 * Template: Course Final Result Page
 *
 * @package BlueDolphin\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

load_template(
	\BlueDolphin\Lms\locate_template( 'course-result.php' ),
	true
);

get_footer();
