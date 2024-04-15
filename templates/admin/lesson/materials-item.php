<?php
/**
 * Template: Materials item template.
 *
 * @package BlueDolphin\Lms
 */

if ( empty( $materials ) ) {
	return;
}
?>
<?php
foreach ( $materials as $key => $material ) :
	$method   = isset( $material['method'] ) ? $material['method'] : 'upload';
	$media_id = isset( $material['media_id'] ) ? (int) $material['media_id'] : 0;
	?>
	<div class="bdlms-materials-item">
		<div class="bdlms-media-choose">
			<label><?php esc_html_e( 'File Title', 'bluedolphin-lms' ); ?></label>
			<input type="text" value="<?php echo isset( $material['title'] ) ? esc_attr( $material['title'] ) : ''; ?>" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[material][<?php echo (int) $key; ?>][title]" placeholder="<?php esc_attr_e( 'Enter File Title', 'bluedolphin-lms' ); ?>">
		</div>
		<div class="bdlms-media-choose material-type">
			<label><?php esc_html_e( 'Method', 'bluedolphin-lms' ); ?></label>
			<select name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[material][<?php echo (int) $key; ?>][method]">
				<option value="upload"<?php selected( 'upload', $method ); ?>><?php esc_html_e( 'Upload', 'bluedolphin-lms' ); ?></option>
				<option value="external"<?php selected( 'external', $method ); ?>><?php esc_html_e( 'External', 'bluedolphin-lms' ); ?></option>
			</select>
		</div>
		<div class="bdlms-media-choose<?php echo esc_attr( 'upload' !== $method ? ' hidden' : '' ); ?>" data-media_type="choose_file">
			<label><?php esc_html_e( 'Choose File', 'bluedolphin-lms' ); ?></label>
			<div class="bdlms-media-file">
			<?php if ( $media_id ) : ?>
				<?php $fileurl = wp_get_attachment_url( $media_id ); ?>
				<a href="javascript:;" class="bdlms-open-media button" data-library_type="application/pdf, text/plain" data-ext="<?php echo esc_attr( apply_filters( 'bdlms_lesson_allowed_material_types', 'pdf,txt' ) ); ?>"><?php esc_html_e( 'Change File', 'bluedolphin-lms' ); ?></a>
				<span class="bdlms-media-name"><a href="<?php echo esc_url( $fileurl ); ?>" target="_blank"><?php echo esc_html( basename( $fileurl ) ); ?></a></span>
			<?php else : ?>
				<a href="javascript:;" class="bdlms-open-media button" data-library_type="application/pdf, text/plain" data-ext="<?php echo esc_attr( apply_filters( 'bdlms_lesson_allowed_material_types', 'pdf,txt' ) ); ?>"><?php esc_html_e( 'Choose File', 'bluedolphin-lms' ); ?></a>
				<span class="bdlms-media-name"><?php esc_html_e( 'No File Chosen', 'bluedolphin-lms' ); ?></span>
			<?php endif; ?>
				<input type="hidden" value="<?php echo esc_attr( $media_id ); ?>" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[material][<?php echo (int) $key; ?>][media_id]">
			</div>
		</div>
		<div class="bdlms-media-choose<?php echo esc_attr( 'external' !== $method ? ' hidden' : '' ); ?>" data-media_type="file_url">
			<label><?php esc_html_e( 'File URL', 'bluedolphin-lms' ); ?></label>
			<input type="text" value="<?php echo isset( $material['external_url'] ) ? esc_url( $material['external_url'] ) : ''; ?>" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[material][<?php echo (int) $key; ?>][external_url]" placeholder="<?php esc_attr_e( 'Enter File URL', 'bluedolphin-lms' ); ?>">
		</div>
		<?php do_action( 'bdlms_lesson_material_item', $material, $this ); ?>
		<div class="bdlms-media-choose">
			<button type="button" class="bdlms-remove-material">
				<svg class="icon" width="12" height="12">
					<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
				</svg>
				<?php esc_html_e( 'Delete', 'bluedolphin-lms' ); ?>
			</button>
		</div>
	</div>
	<?php
endforeach;