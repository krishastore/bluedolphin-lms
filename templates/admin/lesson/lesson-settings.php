<?php
/**
 * Template: Lesson Settings Metabox.
 *
 * @package BlueDolphin\Lms
 */

?>

<div class="bdlms-lesson-duration">
	<label>Duration</label>
	<div class="bdlms-duration-input">
		<input type="number">
		<select>
			<option>Minutes</option>
			<option>Option 1</option>
			<option>Option 2</option>
			<option>Option 3</option>
		</select>
	</div>
</div>
<div class="bdlms-materials-box">
	<div class="bdlms-materials-box__header">
		<h3>Materials</h3>
		<p>Max File: 2   |   Max Size: 2MB   |   Format: .PDF, .TXT</p>
	</div>
	<div class="bdlms-materials-box__body">
		<div class="bdlms-materials-item">
			<div class="bdlms-media-choose">
				<label>File Title</label>
				<input type="text" placeholder="Enter File Title">
			</div>
			<div class="bdlms-media-choose">
				<label>Method</label>
				<select>
					<option>Upload</option>
					<option>External</option>
				</select>
			</div>
			<div class="bdlms-media-choose">
				<label>Choose File</label>
				<input type="file">
			</div>
			<div class="bdlms-media-choose">
				<button type="button" class="button button-primary">
					Save
				</button>
				<button type="button" class="bdlms-remove-material">
					<svg class="icon" width="12" height="12">
						<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
					</svg>
					Delete
				</button>
			</div>
		</div>
		<div class="bdlms-materials-item">
			<div class="bdlms-media-choose">
				<label>File Title</label>
				<input type="text" placeholder="Enter File Title">
			</div>
			<div class="bdlms-media-choose">
				<label>Method</label>
				<select>
					<option>Upload</option>
					<option>External</option>
				</select>
			</div>
			<div class="bdlms-media-choose">
				<label>File URL</label>
				<input type="text" placeholder="Enter File URL">
			</div>
			<div class="bdlms-media-choose">
				<button type="button" class="button button-primary">
					Save
				</button>
				<button type="button" class="bdlms-remove-material">
					<svg class="icon" width="12" height="12">
						<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
					</svg>
					Delete
				</button>
			</div>
		</div>
	</div>	
	<div class="bdlms-materials-box__footer">
		<button type="button" class="button"><?php esc_html_e( 'Add More Materials', 'bluedolphin-lms' ); ?></button>
	</div>
</div>
