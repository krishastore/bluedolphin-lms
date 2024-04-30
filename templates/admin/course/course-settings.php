<?php
/**
 * Template: Course Settings Metabox.
 *
 * @package BlueDolphin\Lms
 */

$setting_tags = apply_filters(
	'bdlms_course_setting_tabs',
	array(
		'course-info'            => array(
			'name'     => esc_html__( 'Course Information', 'bluedolphin-lms' ),
			'template' => BDLMS_TEMPLATEPATH . '/admin/course/setting-course-info.php',
		),
		'assessment'             => array(
			'name'     => esc_html__( 'Assessment', 'bluedolphin-lms' ),
			'template' => BDLMS_TEMPLATEPATH . '/admin/course/setting-assessment.php',
		),
		'author'                 => array(
			'name'     => esc_html__( 'Author', 'bluedolphin-lms' ),
			'template' => BDLMS_TEMPLATEPATH . '/admin/course/setting-author.php',
		),
		'downloadable-materials' => array(
			'name'     => esc_html__( 'Downloadable Materials', 'bluedolphin-lms' ),
			'template' => BDLMS_TEMPLATEPATH . '/admin/course/setting-downloadable-materials.php',
		),
	)
);
?>
<div class="bdlms-course-settings">
	<div class="bdlms-tab-container">
		<?php if ( is_array( $setting_tags ) ) : ?>
			<div class="bdlms-tabs-nav">
				<?php
				$count = 0;
				foreach ( $setting_tags as $key => $setting_tag ) :
					?>
					<a href="javascript:;" class="bdlms-tab<?php echo 0 === $count ? ' active' : ''; ?>" data-tab="<?php echo esc_attr( $key ); ?>"><?php echo isset( $setting_tag['name'] ) ? esc_html( $setting_tag['name'] ) : ''; ?></a>
					<?php
					++$count;
				endforeach;
				?>
			</div>
			<?php
			$setting_templates = array_column( $setting_tags, 'template' );
			foreach ( $setting_templates as $key => $setting_template ) {
				if ( is_readable( $setting_template ) ) {
					$active_class = 0 === $key ? ' active' : '';
					require $setting_template;
				}
			}
			?>
		<?php endif; ?>
	</div>	
</div>
<?php if ( isset( $setting_tags['downloadable-materials'] ) ) : ?>
<script id="materials_item_tmpl" type="text/template">
	<div class="bdlms-materials-list-item material-add-new">
		<ul class="hidden">
			<li class="assignment-title"></li>
			<li class="assignment-type"><?php esc_html_e( 'Upload', 'bluedolphin-lms' ); ?></li>
			<li>
				<div class="bdlms-materials-list-action">
					<a href="javascript:;" class="edit-material">
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
		<div class="bdlms-materials-item">
			<div class="bdlms-media-choose">
				<label><?php esc_html_e( 'File Title', 'bluedolphin-lms' ); ?></label>
				<input type="text" class="material-file-title" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[material][0][title]" placeholder="<?php esc_attr_e( 'Enter File Title', 'bluedolphin-lms' ); ?>">
			</div>
			<div class="bdlms-media-choose material-type">
				<label><?php esc_html_e( 'Method', 'bluedolphin-lms' ); ?></label>
				<select name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[material][0][method]">
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
				'bdlms_course_material_item',
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
				<button type="button" class="button button-primary bdlms-save-material">
					<?php esc_html_e( 'Done', 'bluedolphin-lms' ); ?>
				</button>
				<button type="button" class="bdlms-remove-material">
					<svg class="icon" width="12" height="12">
						<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
					</svg>
					<?php esc_html_e( 'Delete', 'bluedolphin-lms' ); ?>
				</button>
			</div>
		</div>
	</div>
</script>
	<?php
endif;
