<?php
/**
 * Template: Materials item template.
 *
 * @package BlueDolphin\Lms
 */

?>

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