<?php
/**
 * Template: Course - None Content.
 *
 * @package BlueDolphin\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="bdlms-wrap">
	<div class="bdlms-lesson-view__body">
		<div class="bdlms-lesson-note">
			<div class="bdlms-text-xl bdlms-p-16 bdlms-bg-gray bdlms-text-center bdlms-text-primary-dark">
				<?php
				if ( current_user_can( 'edit_post', get_the_ID() ) ) {
					printf(
						/* translators: %s is Link to new post */
						esc_html__( 'Ready to publish your first curriculum? %s.', 'bluedolphin-lms' ),
						sprintf(
							/* translators: %1$s is Link to new post, %2$s is Get started here */
							'<a href="%1$s" target="_blank">%2$s</a>',
							esc_url( get_edit_post_link( get_the_ID() ) ),
							esc_html__( 'Get started here', 'bluedolphin-lms' )
						)
					);
				} else {
					esc_html_e( 'No attached curriculum was found in this course.', 'bluedolphin-lms' );
				}
				?>
			</div>
		</div>
	</div>
</div>