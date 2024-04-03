<?php
/**
 * Template: Popup html template.
 *
 * @package BlueDolphin\Lms
 */

?>

<div id="course_list_modal" class="hidden" style="max-width:463px">
	<div class="bdlms-qus-bank-modal">
		<input type="text" placeholder="<?php esc_attr_e( 'Type here to search for the course', 'bluedolphin-lms' ); ?>" class="bdlms-qus-bank-search">
		<?php
			$args = array(
				'posts_per_page' => 5,
				'orderby'        => 'rand',
				'post_type'      => \BlueDolphin\Lms\BDLMS_COURSE_CPT,
				'post_status'    => 'publish',
			);
			if ( isset( $s ) ) {
				$args['s'] = $s;
			}
			$courses = get_posts( $args );
			?>
		<div class="bdlms-qus-list" id="bdlms_qus_list">
			<?php if ( ! empty( $courses ) ) : ?>
				<ul>
					<?php
					foreach ( $courses as $key => $course ) :
						?>
						<li>
							<div class="bdlms-setting-checkbox">
								<input type="checkbox" class="bdlms-choose-existing" id="bdlms-qus-<?php echo (int) $key; ?>" value="<?php echo (int) $course->ID; ?>">
								<label for="bdlms-qus-<?php echo (int) $key; ?>"><?php echo esc_html( $course->post_title ); ?></label>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php else : ?>
				<p><?php esc_html_e( 'No course found.', 'bluedolphin-lms' ); ?></p>
			<?php endif; ?>
		</div>

		<div class="bdlms-qus-bank-add">
			<button class="button button-primary bdlms-add-question" disabled><?php esc_html_e( 'Add', 'bluedolphin-lms' ); ?></button>
			<span class="bdlms-qus-selected"><?php echo esc_html( sprintf( __( '%d Selected', 'bluedolphin-lms' ), 0 ) ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment ?></span>
			<span class="spinner"></span>
		</div>
	</div>
</div>