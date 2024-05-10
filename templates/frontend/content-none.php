<?php
/**
 * Template: Course - None Content.
 *
 * @package BlueDolphin\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended
 */

?>
<div class="bdlms-wrap">
	<div class="bdlms-lesson-view__body">
		<div class="bdlms-lesson-note">
			<div class="bdlms-text-xl bdlms-p-16 bdlms-bg-gray bdlms-text-center bdlms-text-primary-dark">
				<?php
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
					?>
			</div>
		</div>
	</div>
</div>