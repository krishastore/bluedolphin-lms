<?php
/**
 * Template: Add media Metabox.
 *
 * @package BlueDolphin\Lms
 */

$media_type = 'video';
?>

<div class="media-type-select">
	<label><input type="radio" name="mediatype" value="video"<?php checked( 'video', $media_type ); ?>> <?php esc_html_e( 'Video', 'bluedolphin-lms' ); ?></label>
	<label><input type="radio" name="mediatype" value="text"<?php checked( 'text', $media_type ); ?>> <?php esc_html_e( 'Text', 'bluedolphin-lms' ); ?></label></label>
</div>
<div class="bdlms-video-type-box<?php echo esc_attr( 'text' === $media_type ? ' hidden' : '' ); ?>">
	<div class="bdlms-media-choose">
		<label><?php esc_html_e( 'Choose File', 'bluedolphin-lms' ); ?></label>
		<input type="file">
	</div>
	<div class="bdlms-or">
		<span><?php esc_html_e( 'OR', 'bluedolphin-lms' ); ?></span>
	</div>
	<div class="bdlms-media-choose">
		<label><?php esc_html_e( 'Add Embed Video URL', 'bluedolphin-lms' ); ?></label>
		<input type="text" placeholder="Link">
	</div>
</div>
<div class="lesson-media-editor">
	<?php wp_editor( '', 'lesson_media', array() ); ?>
</div>
