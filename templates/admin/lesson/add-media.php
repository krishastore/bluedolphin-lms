<?php
/**
 * Template: Add media Metabox.
 *
 * @package BlueDolphin\Lms
 */

?>

<div class="media-type-select">
	<label>
		<input type="radio" name="mediatype"> Video
	</label>
	<label>
		<input type="radio" name="mediatype"> text
	</label>
</div>
<div class="bdlms-video-type-box">
	<div class="bdlms-media-choose">
		<label>Choose File</label>
		<input type="file">
	</div>
	<div class="bdlms-or">
		<span>OR</span>
	</div>
	<div class="bdlms-media-choose">
		<label>Add Embed Video URL</label>
		<input type="text" placeholder="Link">
	</div>
</div>
<?php wp_editor( '', 'lesson_media', array() ); ?>