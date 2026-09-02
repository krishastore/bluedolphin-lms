<?php
/**
 * Template: Course - None Content.
 *
 * @package ST\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="stlms-wrap">
	<div class="stlms-lesson-view__body">
		<div class="stlms-lesson-note">
			<div class="stlms-text-xl stlms-p-16 stlms-bg-gray stlms-text-center stlms-text-primary-dark">
				<?php
				if ( current_user_can( 'edit_post', get_the_ID() ) ) {
					printf(
						/* translators: %s is Link to new post */
						esc_html__( 'Ready to publish your first curriculum? %s.', 'skilltriks' ),
						sprintf(
							/* translators: %1$s is Link to new post, %2$s is Get started here */
							'<a href="%1$s" target="_blank">%2$s</a>',
							esc_url( get_edit_post_link( get_the_ID() ) ),
							esc_html__( 'Get started here', 'skilltriks' )
						)
					);
				} else {
					esc_html_e( 'No attached curriculum was found in this course.', 'skilltriks' );
				}
				?>
			</div>
		</div>
	</div>
</div>
