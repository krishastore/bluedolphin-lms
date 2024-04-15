<?php
/**
 * Template: Add media Metabox.
 *
 * @package BlueDolphin\Lms
 */

$media_type = $media['media_type'];
$video_id   = $media['video_id'];
$video_url  = $media['embed_video_url'];
$text       = $media['text'];
?>
<?php do_action( 'bdlms_lesson_media_before', $media, $this ); ?>
<input type="hidden" name="bdlms_nonce" value="<?php echo esc_attr( wp_create_nonce( BDLMS_BASEFILE ) ); ?>">
<div class="media-type-select">
	<label><input type="radio" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[media][media_type]" value="video"<?php checked( 'video', $media_type ); ?>> <?php esc_html_e( 'Video', 'bluedolphin-lms' ); ?></label>
	<label><input type="radio" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[media][media_type]" value="text"<?php checked( 'text', $media_type ); ?>> <?php esc_html_e( 'Text', 'bluedolphin-lms' ); ?></label></label>
	<?php do_action( 'bdlms_lesson_after_media_type', $media, $this ); ?>
</div>
<div class="bdlms-video-type-box<?php echo esc_attr( 'text' === $media_type ? ' hidden' : '' ); ?>">
	<div class="bdlms-media-choose">
		<label><?php esc_html_e( 'Choose File', 'bluedolphin-lms' ); ?></label>
		<div class="bdlms-media-file">
			<?php
			if ( $video_id ) :
				$fileurl = wp_get_attachment_url( $video_id );
				?>
				<a href="javascript:;" class="bdlms-open-media button" data-library_type="video" data-ext="<?php echo esc_attr( apply_filters( 'bdlms_lesson_allowed_video_types', 'mp4, m4v, mpg, mov, vtt, avi, ogv, wmv, 3gp, 3g2' ) ); ?>"><?php esc_html_e( 'Change File', 'bluedolphin-lms' ); ?></a>
				<span class="bdlms-media-name"><a href="<?php echo esc_url( $fileurl ); ?>" target="_blank"><?php echo esc_html( basename( $fileurl ) ); ?></a></span>
			<?php else : ?>
				<a href="javascript:;" class="bdlms-open-media button" data-library_type="video" data-ext="<?php echo esc_attr( apply_filters( 'bdlms_lesson_allowed_video_types', 'mp4, m4v, mpg, mov, vtt, avi, ogv, wmv, 3gp, 3g2' ) ); ?>"><?php esc_html_e( 'Choose File', 'bluedolphin-lms' ); ?></a>
				<span class="bdlms-media-name"><?php esc_html_e( 'No File Chosen', 'bluedolphin-lms' ); ?></span>
			<?php endif; ?>
			<input type="hidden" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[media][video_id]" value="<?php echo (int) $video_id; ?>">
		</div>
	</div>
	<div class="bdlms-or">
		<span><?php esc_html_e( 'OR', 'bluedolphin-lms' ); ?></span>
	</div>
	<div class="bdlms-media-choose">
		<label><?php esc_html_e( 'Add Embed Video URL', 'bluedolphin-lms' ); ?></label>
		<input type="text" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[media][embed_video_url]" placeholder="<?php esc_attr_e( 'Link', 'bluedolphin-lms' ); ?>" value="<?php echo esc_url( $video_url ); ?>">
	</div>
</div>
<div class="lesson-media-editor">
	<textarea name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[media][text]" id="media_text_editor" rows="15" class="wp-editor-area" style="width: 100%;"><?php echo esc_textarea( $text ); ?></textarea>
</div>
<?php do_action( 'bdlms_lesson_media_after', $media, $this ); ?>