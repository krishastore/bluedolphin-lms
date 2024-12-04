<?php
/**
 * Template: Popup html template.
 *
 * @package BD\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="course_list_modal" class="hidden" style="max-width:463px">
	<div class="bdlms-qus-bank-modal">
		<input type="text" placeholder="<?php esc_attr_e( 'Type here to search for the course', 'bluedolphin-lms' ); ?>" class="bdlms-qus-bank-search">
		<div class="bdlms-qus-list" id="bdlms_course_list">
			<?php
			if ( ! empty( $fetch_request ) ) :
					$lesson_id = isset( $lesson_id ) ? $lesson_id : 0;
					$args      = array(
						'posts_per_page' => -1,
						'post_type'      => \BD\Lms\BDLMS_COURSE_CPT,
						'post_status'    => 'publish',
					);
					$courses   = get_posts( $args );
					?>
				<?php if ( ! empty( $courses ) ) : ?>
					<ul class="bdlms-qus-list-scroll">
						<?php
						foreach ( $courses as $key => $course ) :
							?>
							<li>
								<div class="bdlms-setting-checkbox">
									<input type="checkbox" class="bdlms-choose-course" id="bdlms-qus-<?php echo (int) $key; ?>" value="<?php echo (int) $course->ID; ?>">
									<label for="bdlms-qus-<?php echo (int) $key; ?>"><?php echo esc_html( $course->post_title ); ?></label>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php else : ?>
					<p><?php esc_html_e( 'No course found.', 'bluedolphin-lms' ); ?></p>
				<?php endif; ?>
			<?php else : ?>
				<span class="spinner is-active"></span>
			<?php endif; ?>
		</div>

		<div class="bdlms-qus-bank-add">
			<button class="button button-primary bdlms-add-course" disabled><?php esc_html_e( 'Save', 'bluedolphin-lms' ); ?></button>
			<span class="bdlms-qus-selected"><?php echo esc_html( sprintf( __( '%d Selected', 'bluedolphin-lms' ), 0 ) ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment ?></span>
			<span class="spinner"></span>
		</div>
		<p class="bdlms-notice"><?php esc_html_e( 'Note: It will be added in the last curriculum section.', 'bluedolphin-lms' ); ?></p>
	</div>
</div>
