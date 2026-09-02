<?php
/**
 * Template: Materials item template.
 *
 * @package ST\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( empty( $materials ) ) {
	return;
}
?>
<?php
foreach ( $materials as $key => $material ) :

	$method   = isset( $material['method'] ) ? $material['method'] : 'upload';
	$media_id = isset( $material['media_id'] ) ? (int) $material['media_id'] : 0;
	?>
	<div class="stlms-materials-list-item">
		<ul>
			<li class="assignment-title"><?php echo isset( $material['title'] ) ? esc_attr( $material['title'] ) : ''; ?></li>
			<li class="assignment-type"><?php echo 'upload' === $method ? esc_html__( 'Upload', 'skilltriks' ) : esc_html__( 'External', 'skilltriks' ); ?></li>
			<li>
				<div class="stlms-materials-list-action">
					<a href="javascript:;" class="edit-material">
						<svg class="icon" width="12" height="12">
							<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#edit"></use>
						</svg>
						<?php esc_html_e( 'Edit', 'skilltriks' ); ?>
					</a>
					<a href="javascript:;" class="stlms-delete-link">
						<svg class="icon" width="12" height="12">
							<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
						</svg>
						<?php esc_html_e( 'Remove', 'skilltriks' ); ?>
					</a>
				</div>
			</li>
		</ul>
		<div class="stlms-materials-item hidden">
			<div class="stlms-media-choose">
				<label><?php esc_html_e( 'File Title', 'skilltriks' ); ?></label>
				<input type="text" class="material-file-title" value="<?php echo isset( $material['title'] ) ? esc_attr( $material['title'] ) : ''; ?>" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[material][<?php echo (int) $key; ?>][title]" placeholder="<?php esc_attr_e( 'Enter File Title', 'skilltriks' ); ?>">
			</div>
			<div class="stlms-media-choose material-type">
				<label><?php esc_html_e( 'Method', 'skilltriks' ); ?></label>
				<select name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[material][<?php echo (int) $key; ?>][method]">
					<option value="upload"<?php selected( 'upload', $method ); ?>><?php esc_html_e( 'Upload', 'skilltriks' ); ?></option>
					<option value="external"<?php selected( 'external', $method ); ?>><?php esc_html_e( 'External', 'skilltriks' ); ?></option>
				</select>
			</div>
			<div class="stlms-media-choose<?php echo esc_attr( 'upload' !== $method ? ' hidden' : '' ); ?>" data-media_type="choose_file">
				<label><?php esc_html_e( 'Choose File', 'skilltriks' ); ?></label>
				<div class="stlms-media-file">
				<?php if ( $media_id ) : ?>
					<?php $fileurl = wp_get_attachment_url( $media_id ); ?>
					<a href="javascript:;" class="stlms-open-media button" data-library_type="application/pdf, text/plain" data-ext="<?php echo esc_attr( apply_filters( 'stlms_lesson_allowed_material_types', 'pdf,txt' ) ); ?>"><?php esc_html_e( 'Change File', 'skilltriks' ); ?></a>
					<span class="stlms-media-name"><a href="<?php echo esc_url( $fileurl ); ?>" target="_blank"><?php echo esc_html( basename( $fileurl ) ); ?></a></span>
				<?php else : ?>
					<a href="javascript:;" class="stlms-open-media button" data-library_type="application/pdf, text/plain" data-ext="<?php echo esc_attr( apply_filters( 'stlms_lesson_allowed_material_types', 'pdf,txt' ) ); ?>"><?php esc_html_e( 'Choose File', 'skilltriks' ); ?></a>
					<span class="stlms-media-name"><?php esc_html_e( 'No File Chosen', 'skilltriks' ); ?></span>
				<?php endif; ?>
					<input type="hidden" value="<?php echo esc_attr( $media_id ); ?>" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[material][<?php echo (int) $key; ?>][media_id]">
				</div>
			</div>
			<div class="stlms-media-choose<?php echo esc_attr( 'external' !== $method ? ' hidden' : '' ); ?>" data-media_type="file_url">
				<label><?php esc_html_e( 'File URL', 'skilltriks' ); ?></label>
				<input type="text" value="<?php echo isset( $material['external_url'] ) ? esc_url( $material['external_url'] ) : ''; ?>" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[material][<?php echo (int) $key; ?>][external_url]" placeholder="<?php esc_attr_e( 'Enter File URL', 'skilltriks' ); ?>">
			</div>
			<?php do_action( 'stlms_lesson_material_item', $material, $this ); ?>
			<div class="stlms-media-choose">
				<button type="button" class="button button-primary stlms-save-material">
					<?php esc_html_e( 'Done', 'skilltriks' ); ?>
				</button>
				<button type="button" class="stlms-remove-material">
					<svg class="icon" width="12" height="12">
						<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
					</svg>
					<?php esc_html_e( 'Delete', 'skilltriks' ); ?>
				</button>
			</div>
		</div>
	</div>
	<?php
endforeach;
