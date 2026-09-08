<?php
/**
 * Template: Add media Metabox.
 *
 * @package ST\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$media_type     = $media['media_type'];
$video_id       = $media['video_id'];
$video_url      = $media['embed_video_url'];
$text           = $media['text'];
$file_id        = $media['file_id'];
$file_url       = $media['file_url'];
$has_ai_chatbot = ! empty( $media['ai_chatbot'] ) ? $media['ai_chatbot'] : 0;
$ingestion_data = get_transient( 'stlms_video_ingestion_in_progress' );
?>
<?php do_action( 'stlms_lesson_media_before', $media, $this ); ?>
<input type="hidden" name="stlms_nonce" value="<?php echo esc_attr( wp_create_nonce( STLMS_BASEFILE ) ); ?>">
<div class="media-type-select">
	<label><input type="radio" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[media][media_type]" value="video"<?php checked( 'video', $media_type ); ?>> <?php esc_html_e( 'Video', 'skilltriks' ); ?></label>
	<label><input type="radio" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[media][media_type]" value="text"<?php checked( 'text', $media_type ); ?>> <?php esc_html_e( 'Text', 'skilltriks' ); ?></label>
	<label><input type="radio" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[media][media_type]" value="file"<?php checked( 'file', $media_type ); ?>> <?php esc_html_e( 'File', 'skilltriks' ); ?></label>
	<?php do_action( 'stlms_lesson_after_media_type', $media, $this ); ?>
</div>
<div id="media_video" class="stlms-video-type-box<?php echo in_array( $media_type, array( 'text', 'file' ), true ) ? ' hidden' : ''; ?>">
	<?php
	if ( class_exists( \LFI\Stlms\LicenseIntegration::class ) ) :
		if ( \LFI\Stlms\LicenseIntegration::instance()->is_pro() ) :
			?>
	<div class="stlms-media-choose ai-chatbot-option">
		<label>
			<?php esc_html_e( 'Enable AI Chatbot', 'skilltriks' ); ?>
		</label>
		<div>
			<label>
				<input type="checkbox" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[media][ai_chatbot]" value="1" <?php checked( $has_ai_chatbot, 1 ); ?> <?php disabled( ! empty( $ingestion_data ), true ); ?>>
				<?php
				if ( ! empty( $ingestion_data ) ) {
					echo esc_html( __( 'Transcription already in progress. Please wait for it to finish before adding another video.', 'skilltriks' ) );
				} else {
					esc_html_e( 'Yes', 'skilltriks' );
				}
				?>
			</label>
		</div>
	</div>
			<?php
		endif;
	endif;
	?>
	<div class="stlms-media-choose">
		<label><?php esc_html_e( 'Choose File', 'skilltriks' ); ?></label>
		<div class="stlms-media-file">
			<?php
			if ( $video_id ) :
				$fileurl = wp_get_attachment_url( $video_id );
				?>
				<a href="javascript:;" class="stlms-open-media button" data-library_type="video" data-ext="<?php echo esc_attr( apply_filters( 'stlms_lesson_allowed_video_types', 'mp4, m4v, mpg, mov, vtt, avi, ogv, wmv, 3gp, 3g2' ) ); ?>"><?php esc_html_e( 'Change File', 'skilltriks' ); ?></a>
				<span class="stlms-media-name"><a href="<?php echo esc_url( $fileurl ); ?>" target="_blank"><?php echo esc_html( basename( $fileurl ) ); ?></a></span>
			<?php else : ?>
				<a href="javascript:;" class="stlms-open-media button" data-library_type="video" data-ext="<?php echo esc_attr( apply_filters( 'stlms_lesson_allowed_video_types', 'mp4, m4v, mpg, mov, vtt, avi, ogv, wmv, 3gp, 3g2' ) ); ?>"><?php esc_html_e( 'Choose File', 'skilltriks' ); ?></a>
				<span class="stlms-media-name"><?php esc_html_e( 'No File Chosen', 'skilltriks' ); ?></span>
			<?php endif; ?>
			<input type="hidden" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[media][video_id]" value="<?php echo (int) $video_id; ?>">
		</div>
	</div>
	<div class="stlms-or">
		<span><?php esc_html_e( 'OR', 'skilltriks' ); ?></span>
	</div>
	<div class="stlms-media-choose">
		<label><?php esc_html_e( 'Add Embed Video URL', 'skilltriks' ); ?></label>
		<input type="text" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[media][embed_video_url]" placeholder="<?php esc_attr_e( 'Link', 'skilltriks' ); ?>" value="<?php echo esc_url( $video_url ); ?>">
	</div>
</div>
<div id="media_file" class="stlms-video-type-box<?php echo in_array( $media_type, array( 'video', 'file' ), true ) ? ' hidden' : ''; ?>">
	<div class="stlms-media-choose">
		<label><?php esc_html_e( 'Choose File', 'skilltriks' ); ?></label>
		<div class="stlms-media-file">
			<?php
			if ( $file_id ) :
				$fileurl = wp_get_attachment_url( $file_id );
				?>
				<a href="javascript:;" class="stlms-open-media button" data-library_type="application/pdf, text/plain" data-ext="<?php echo esc_attr( apply_filters( 'stlms_lesson_allowed_file_types', 'pdf,txt' ) ); ?>"><?php esc_html_e( 'Change File', 'skilltriks' ); ?></a>
				<span class="stlms-media-name"><a href="<?php echo esc_url( $fileurl ); ?>" target="_blank"><?php echo esc_html( basename( $fileurl ) ); ?></a></span>
			<?php else : ?>
				<a href="javascript:;" class="stlms-open-media button" data-library_type="application/pdf, text/plain" data-ext="<?php echo esc_attr( apply_filters( 'stlms_lesson_allowed_file_types', 'pdf,txt' ) ); ?>"><?php esc_html_e( 'Choose File', 'skilltriks' ); ?></a>
				<span class="stlms-media-name"><?php esc_html_e( 'No File Chosen', 'skilltriks' ); ?></span>
			<?php endif; ?>
			<input type="hidden" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[media][file_id]" value="<?php echo (int) $file_id; ?>">
		</div>
	</div>
	<div class="stlms-or">
		<span><?php esc_html_e( 'OR', 'skilltriks' ); ?></span>
	</div>
	<div class="stlms-media-choose">
		<label><?php esc_html_e( 'Add File URL', 'skilltriks' ); ?></label>
		<input type="text" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[media][file_url]" placeholder="<?php esc_attr_e( 'File URL', 'skilltriks' ); ?>" value="<?php echo esc_url( $file_url ); ?>">
	</div>
</div>
<div id="media_text" class="lesson-media-editor">
	<textarea name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[media][text]" id="media_text_editor" rows="15" class="wp-editor-area" style="width: 100%;"><?php echo esc_textarea( $text ); ?></textarea>
</div>
<?php do_action( 'stlms_lesson_media_after', $media, $this ); ?>
