<?php
/**
 * Template: Lesson Settings Metabox.
 *
 * @package BlueDolphin\Lms
 */

?>

<div class="bdlms-lesson-duration">
	<label><?php esc_html_e( 'Duration', 'bluedolphin-lms' ); ?></label>
	<div class="bdlms-duration-input">
		<input type="number" value="0" step="1" min="0">
		<select name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[settings][duration_type]">
			<option value="minute"<?php selected( 'minute', $settings['duration_type'] ); ?>><?php esc_html_e( 'Minute(s)', 'bluedolphin-lms' ); ?></option>
			<option value="hour"<?php selected( 'hour', $settings['duration_type'] ); ?>><?php esc_html_e( 'Hour(s)', 'bluedolphin-lms' ); ?></option>
			<option value="day"<?php selected( 'day', $settings['duration_type'] ); ?>><?php esc_html_e( 'Day(s)', 'bluedolphin-lms' ); ?></option>
			<option value="week"<?php selected( 'week', $settings['duration_type'] ); ?>><?php esc_html_e( 'Week(s)', 'bluedolphin-lms' ); ?></option>
		</select>
	</div>
</div>
<div class="bdlms-materials-box">
	<div class="bdlms-materials-box__header">
		<h3><?php esc_html_e( 'Materials', 'bluedolphin-lms' ); ?></h3>
		<p><?php esc_html_e( 'Max File: 2   |   Max Size: 2MB   |   Format: .PDF, .TXT', 'bluedolphin-lms' ); ?></p>
	</div>
	<div class="bdlms-materials-box__body">
		<?php
			require_once BDLMS_TEMPLATEPATH . '/admin/lesson/materials-item.php';
		?>
	</div>	
	<div class="bdlms-materials-box__footer">
		<button type="button" class="button"><?php esc_html_e( 'Add More Materials', 'bluedolphin-lms' ); ?></button>
	</div>
</div>

<script id="materials_item_tmpl" type="text/template">
	<div class="bdlms-materials-item">
		<div class="bdlms-media-choose">
			<label><?php esc_html_e( 'File Title', 'bluedolphin-lms' ); ?></label>
			<input type="text" placeholder="<?php esc_attr_e( 'Enter File Title', 'bluedolphin-lms' ); ?>">
		</div>
		<div class="bdlms-media-choose material-type">
			<label><?php esc_html_e( 'Method', 'bluedolphin-lms' ); ?></label>
			<select>
				<option value="upload"><?php esc_html_e( 'Upload', 'bluedolphin-lms' ); ?></option>
				<option value="external"><?php esc_html_e( 'External', 'bluedolphin-lms' ); ?></option>
			</select>
		</div>
		<div class="bdlms-media-choose" data-media_type="choose_file">
			<label><?php esc_html_e( 'Choose File', 'bluedolphin-lms' ); ?></label>
			<input type="file">
		</div>
		<div class="bdlms-media-choose hidden" data-media_type="file_url">
			<label><?php esc_html_e( 'File URL', 'bluedolphin-lms' ); ?></label>
			<input type="text" placeholder="<?php esc_attr_e( 'Enter File URL', 'bluedolphin-lms' ); ?>">
		</div>
		<div class="bdlms-media-choose">
			<button type="button" class="button button-primary"><?php esc_html_e( 'Save', 'bluedolphin-lms' ); ?></button>
			<button type="button" class="bdlms-remove-material">
				<svg class="icon" width="12" height="12">
					<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
				</svg>
				<?php esc_html_e( 'Delete', 'bluedolphin-lms' ); ?>
			</button>
		</div>
	</div>
</script>