<?php
/**
 * Template: Question Settings.
 *
 * @package BlueDolphin\Lms\Admin
 */

?>
<div class="bdlms-qus-setting-wrap">
	<div class="bdlms-qus-setting-header">
		<div>
			<label for="points_field">
				<?php esc_html_e( 'Marks/Points: ', 'bluedolphin-lms' ); ?>
			</label>
			<input type="number" name="<?php echo esc_attr( $this->meta_key ); ?>[settings][points]" value="<?php echo isset( $settings['points'] ) ? (int) $settings['points'] : 1; ?>" step="1" min="1">
		</div>
		<div>
			<label for="levels_field">
				<?php esc_html_e( 'Difficulty Level', 'bluedolphin-lms' ); ?>
			</label>
			<select name="<?php echo esc_attr( $this->meta_key ); ?>[settings][levels]">
				<?php
				foreach ( \BlueDolphin\Lms\question_levels() as $key => $level ) {
					?>
						<option value="<?php echo esc_attr( $key ); ?>"<?php selected( $levels, $key ); ?>><?php echo esc_html( $level ); ?></option>
					<?php
				}
				?>
			</select>
		</div>
	</div>
	<div class="bdlms-qus-setting-body">
		<h3><?php esc_html_e( 'Show Feedback/Hint ', 'bluedolphin-lms' ); ?></h3>

		<div class="bdlms-hint-box">
			<label for="hint_field">
				<?php esc_html_e( 'Correctly Answered Feedback: ', 'bluedolphin-lms' ); ?>
				<div class="bdlms-tooltip">
					<svg class="icon" width="12" height="12">
						<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#help"></use>
					</svg>
					<span class="bdlms-tooltiptext">
						<?php esc_html_e( 'The instructions for the user to select the right answer. The text will be shown when users click the \'Hint\' button.', 'bluedolphin-lms' ); ?>
					</span>
				</div>
			</label>
			<textarea name="<?php echo esc_attr( $this->meta_key ); ?>[settings][hint]"><?php echo isset( $settings['hint'] ) ? esc_textarea( $settings['hint'] ) : ''; ?></textarea>
		</div>
		<div class="bdlms-hint-box">
			<label for="explanation_field" style="color: #B20000;">
				<?php esc_html_e( 'Incorrectly Answered Feedback: ', 'bluedolphin-lms' ); ?>
				<div class="bdlms-tooltip">
					<svg class="icon" width="12" height="12">
						<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#help"></use>
					</svg>
					<span class="bdlms-tooltiptext">
						<?php esc_html_e( 'The explanation will be displayed when students click the "Check Answer" button.', 'bluedolphin-lms' ); ?>
					</span>
				</div>
			</label>
			<textarea name="<?php echo esc_attr( $this->meta_key ); ?>[settings][explanation]"><?php echo isset( $settings['explanation'] ) ? esc_textarea( $settings['explanation'] ) : ''; ?></textarea>
		</div>
	</div>
</div>