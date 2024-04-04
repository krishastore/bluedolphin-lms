<?php
/**
 * Template: Quiz Setting Metabox.
 *
 * @package BlueDolphin\Lms
 */

?>
<div class="bdlms-quiz-settings">
	<?php do_action( 'bdlms_quiz_settings_fields_before', $settings, $post_id, $this ); ?>
	<ul>
		<li>
			<div class="bdlms-setting-label"><?php esc_html_e( 'Duration', 'bluedolphin-lms' ); ?></div>
			<div class="bdlms-setting-option">
				<input name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[settings][duration]" type="number" class="bdlms-setting-number-input" step="1" min="0" value="<?php echo (int) $settings['duration']; ?>">
				<select name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[settings][duration_type]">
					<option value="minute"<?php selected( 'minute', $settings['duration_type'] ); ?>><?php esc_html_e( 'Minute(s)', 'bluedolphin-lms' ); ?></option>
					<option value="hour"<?php selected( 'hour', $settings['duration_type'] ); ?>><?php esc_html_e( 'Hour(s)', 'bluedolphin-lms' ); ?></option>
					<option value="day"<?php selected( 'day', $settings['duration_type'] ); ?>><?php esc_html_e( 'Day(s)', 'bluedolphin-lms' ); ?></option>
					<option value="week"<?php selected( 'week', $settings['duration_type'] ); ?>><?php esc_html_e( 'Week(s)', 'bluedolphin-lms' ); ?></option>
				</select>
			</div>
		</li>
		<li>
			<div class="bdlms-setting-label"><?php esc_html_e( 'Passing Marks', 'bluedolphin-lms' ); ?></div>
			<div class="bdlms-setting-option">
				<input name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[settings][passing_marks]" type="number" class="bdlms-setting-number-input" step="1" min="1" value="<?php echo (int) $settings['passing_marks']; ?>">
			</div>
		</li>
		<li>
			<div class="bdlms-setting-label"><?php esc_html_e( 'Negative Marking', 'bluedolphin-lms' ); ?></div>
			<div class="bdlms-setting-option">
				<div class="bdlms-setting-checkbox">
					<input name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[settings][negative_marking]" type="checkbox" id="bdlms-neg-mark"<?php checked( 1, $settings['negative_marking'] ); ?>>
					<label for="bdlms-neg-mark"><?php esc_html_e( 'Each question that answer wrongly, the total point is deducted exactly from the question\'s point.', 'bluedolphin-lms' ); ?></label>
				</div>
			</div>
		</li>
		<li>
			<div class="bdlms-setting-label"><?php esc_html_e( 'Review', 'bluedolphin-lms' ); ?></div>
			<div class="bdlms-setting-option">
				<div class="bdlms-setting-checkbox">
					<input name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[settings][review]" type="checkbox" id="bdlms-review"<?php checked( 1, $settings['review'] ); ?>>
					<label for="bdlms-review"><?php esc_html_e( 'Allow students to review this quiz after they finish the quiz.', 'bluedolphin-lms' ); ?></label>
				</div>
			</div>
		</li>
		<li>
			<div class="bdlms-setting-label"><?php esc_html_e( 'Show Correct Answer', 'bluedolphin-lms' ); ?></div>
			<div class="bdlms-setting-option">
				<div class="bdlms-setting-checkbox">
					<input name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[settings][show_correct_review]" type="checkbox" id="bdlms-show-ans"<?php checked( 1, $settings['show_correct_review'] ); ?>>
					<label for="bdlms-show-ans">
						<?php
							esc_html_e( 'Allow students to view the correct answer to the question in reviewing this quiz.', 'bluedolphin-lms' );
						?>
					</label>
				</div>
			</div>
		</li>
		<?php do_action( 'bdlms_quiz_setting_field', $settings, $post_id, $this ); ?>
	</ul>
	<?php do_action( 'bdlms_quiz_settings_fields_after', $settings, $post_id, $this ); ?>
</div>