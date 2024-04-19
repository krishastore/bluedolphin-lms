<?php
/**
 * Template: Lesson Settings Metabox.
 *
 * @package BlueDolphin\Lms
 */

$duration      = $settings['duration'];
$duration_type = $settings['duration_type'];
?>

<div class="bdlms-lesson-duration">
	<label><?php esc_html_e( 'Duration', 'bluedolphin-lms' ); ?></label>
	<div class="bdlms-duration-input">
		<input type="number" value="<?php echo (int) $duration; ?>" step="1" min="0" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[settings][duration]">
		<select name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[settings][duration_type]">
			<option value="minute"<?php selected( 'minute', $duration_type ); ?>><?php esc_html_e( 'Minute(s)', 'bluedolphin-lms' ); ?></option>
			<option value="hour"<?php selected( 'hour', $duration_type ); ?>><?php esc_html_e( 'Hour(s)', 'bluedolphin-lms' ); ?></option>
			<option value="day"<?php selected( 'day', $duration_type ); ?>><?php esc_html_e( 'Day(s)', 'bluedolphin-lms' ); ?></option>
			<option value="week"<?php selected( 'week', $duration_type ); ?>><?php esc_html_e( 'Week(s)', 'bluedolphin-lms' ); ?></option>
		</select>
	</div>
</div>
<?php do_action( 'bdlms_lesson_before_material_box', $settings, $post_id, $this ); ?>
<div class="bdlms-materials-box brd-0">
	<div class="bdlms-materials-box__header">
		<h3><?php esc_html_e( 'Materials', 'bluedolphin-lms' ); ?></h3>
		<p><?php printf( esc_html__( 'Max Size: %s   |   Format: .PDF, .TXT', 'bluedolphin-lms' ), esc_html( size_format( $max_upload_size ) ) ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment ?></p>
	</div>
</div>
<div class="bdlms-materials-box">
	<div class="bdlms-materials-box__body">
		<div class="bdlms-materials-list">
			<ul>
				<li><strong>File Title</strong></li>
				<li><strong>Method</strong></li>
				<li><strong>Action</strong></li>
			</ul>
			<ul>
				<li>Assignment</li>
				<li>Upload</li>
				<li>
					<div class="bdlms-materials-list-action">
						<a href="javascript:;">
							<svg class="icon" width="12" height="12">
								<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#edit"></use>
							</svg>
							<?php esc_html_e( 'Edit', 'bluedolphin-lms' ); ?>
						</a>
						<a href="javascript:;" class="bdlms-delete-link">
							<svg class="icon" width="12" height="12">
								<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
							</svg>
							<?php esc_html_e( 'Remove', 'bluedolphin-lms' ); ?>
						</a>
					</div>
				</li>
			</ul>
		</div>
	</div>	
	<div class="bdlms-materials-box__footer">
		<button type="button" class="button"><?php esc_html_e( 'Add More Materials', 'bluedolphin-lms' ); ?></button>
	</div>
</div>

<div class="bdlms-materials-box">
	<div class="bdlms-materials-box__body">
		<?php
			require_once BDLMS_TEMPLATEPATH . '/admin/lesson/materials-item.php';
		?>
	</div>	
	<div class="bdlms-materials-box__footer">
		<button type="button" class="button"><?php esc_html_e( 'Add More Materials', 'bluedolphin-lms' ); ?></button>
	</div>
</div>
<?php do_action( 'bdlms_lesson_after_material_box', $settings, $post_id, $this ); ?>

<script id="materials_item_tmpl" type="text/template">
	<div class="bdlms-materials-item">
		<div class="bdlms-media-choose">
			<label><?php esc_html_e( 'File Title', 'bluedolphin-lms' ); ?></label>
			<input type="text" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[material][0][title]" placeholder="<?php esc_attr_e( 'Enter File Title', 'bluedolphin-lms' ); ?>">
		</div>
		<div class="bdlms-media-choose material-type">
			<label><?php esc_html_e( 'Method', 'bluedolphin-lms' ); ?></label>
			<select name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[material][0][method]">">
				<option value="upload"><?php esc_html_e( 'Upload', 'bluedolphin-lms' ); ?></option>
				<option value="external"><?php esc_html_e( 'External', 'bluedolphin-lms' ); ?></option>
			</select>
		</div>
		<div class="bdlms-media-choose" data-media_type="choose_file">
			<label><?php esc_html_e( 'Choose File', 'bluedolphin-lms' ); ?></label>
			<div class="bdlms-media-file">
				<a href="javascript:;" class="bdlms-open-media button" data-library_type="application/pdf, text/plain" data-ext="<?php echo esc_attr( apply_filters( 'bdlms_lesson_allowed_material_types', 'pdf,txt' ) ); ?>"><?php esc_html_e( 'Choose File', 'bluedolphin-lms' ); ?></a>
				<span class="bdlms-media-name"><?php esc_html_e( 'No File Chosen', 'bluedolphin-lms' ); ?></span>
				<input type="hidden" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[material][0][media_id]">
			</div>
		</div>
		<div class="bdlms-media-choose hidden" data-media_type="file_url">
			<label><?php esc_html_e( 'File URL', 'bluedolphin-lms' ); ?></label>
			<input type="text" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[material][0][external_url]" placeholder="<?php esc_attr_e( 'Enter File URL', 'bluedolphin-lms' ); ?>">
		</div>
		<?php
		do_action(
			'bdlms_lesson_material_item',
			array(
				'method'       => 'upload',
				'title'        => '',
				'media_id'     => 0,
				'external_url' => '',
			),
			$this
		);
		?>
		<div class="bdlms-media-choose">
			<button type="button" class="bdlms-remove-material">
				<svg class="icon" width="12" height="12">
					<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
				</svg>
				<?php esc_html_e( 'Delete', 'bluedolphin-lms' ); ?>
			</button>
		</div>
	</div>
</script>