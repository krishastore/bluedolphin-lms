<?php
/**
 * Template: Course Settings Metabox.
 *
 * @package BlueDolphin\Lms
 */

?>
<div class="bdlms-course-settings">
	<div class="bdlms-tab-container">
		<div class="bdlms-tabs-nav">
			<a href="javascript:;" class="bdlms-tab active" data-tab="course-info"><?php esc_html_e( 'Course Information', 'bluedolphin-lms' ); ?></a>
			<a href="javascript:;" class="bdlms-tab" data-tab="assessment"><?php esc_html_e( 'Assessment', 'bluedolphin-lms' ); ?></a>
			<a href="javascript:;" class="bdlms-tab" data-tab="author"><?php esc_html_e( 'Author', 'bluedolphin-lms' ); ?></a>
			<a href="javascript:;" class="bdlms-tab" data-tab="downloadable-materials"><?php esc_html_e( 'Downloadable Materials', 'bluedolphin-lms' ); ?></a>
		</div>
		<div class="bdlms-tab-content active" data-tab="course-info">
			<div class="bdlms-cs-row">
				<div class="bdlms-cs-col-left"><?php esc_html_e( 'Course Requirement', 'bluedolphin-lms' ); ?></div>
				<div class="bdlms-cs-col-right">
					<div class="bdlms-cs-drag-list">
						<ul class="cs-drag-list cs-drag-list-group">
							<?php
							$requirement       = $information['requirement'];
							$requirement_count = count( $requirement );
							foreach ( $requirement as $requirement ) :
								?>
								<li>
									<div class="bdlms-cs-drag-field">
										<div class="bdlms-options-drag">
											<svg class="icon" width="8" height="13">
												<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
											</svg>
										</div>
										<input type="text" value="<?php echo esc_attr( $requirement ); ?>" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[information][requirement][]" placeholder="<?php echo esc_attr_e( 'Add details..', 'bluedolphin-lms' ); ?>" class="bdlms-cs-input">
										<div class="bdlms-cs-action<?php echo $requirement_count <= 1 ? ' hidden' : ''; ?>">
											<a href="javascript:;">
												<svg class="icon" width="12" height="12">
													<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
												</svg>											
											</a>
										</div>	
									</div>
								</li>
							<?php endforeach; ?>
						</ul>
						<button class="button" data-add_more="true"><?php esc_html_e( 'Add More', 'bluedolphin-lms' ); ?></button>
					</div>
				</div>
			</div>
			<div class="bdlms-cs-row">
				<div class="bdlms-cs-col-left"><?php esc_html_e( 'What You\'ll Learn', 'bluedolphin-lms' ); ?></div>
				<div class="bdlms-cs-col-right">
					<div class="bdlms-cs-drag-list">
						<ul class="cs-drag-list cs-drag-list-group">
							<?php
							$what_you_learn       = $information['what_you_learn'];
							$what_you_learn_count = count( $what_you_learn );
							foreach ( $what_you_learn as $value ) :
								?>
							<li>
								<div class="bdlms-cs-drag-field">
									<div class="bdlms-options-drag">
										<svg class="icon" width="8" height="13">
											<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
										</svg>
									</div>
									<input type="text" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[information][what_you_learn][]" placeholder="<?php echo esc_attr_e( 'Add details..', 'bluedolphin-lms' ); ?>" class="bdlms-cs-input">
									<div class="bdlms-cs-action<?php echo $what_you_learn_count <= 1 ? ' hidden' : ''; ?>">
										<a href="javascript:;">
											<svg class="icon" width="12" height="12">
												<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
											</svg>											
										</a>
									</div>	
								</div>
							</li>
							<?php endforeach; ?>
						</ul>
						<button class="button" data-add_more="true"><?php esc_html_e( 'Add More', 'bluedolphin-lms' ); ?></button>
					</div>
				</div>
			</div>
			<div class="bdlms-cs-row">
				<div class="bdlms-cs-col-left"><?php esc_html_e( 'Skills You\'ll Gain', 'bluedolphin-lms' ); ?></div>
				<div class="bdlms-cs-col-right">
					<div class="bdlms-cs-drag-list">
						<ul class="cs-drag-list cs-drag-list-group">
						<?php
						$skills_you_gain       = $information['skills_you_gain'];
						$skills_you_gain_count = count( $skills_you_gain );
						foreach ( $skills_you_gain as $value ) :
							?>
							<li>
								<div class="bdlms-cs-drag-field">
									<div class="bdlms-options-drag">
										<svg class="icon" width="8" height="13">
											<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
										</svg>
									</div>
									<input type="text" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[information][skills_you_gain][]" placeholder="<?php echo esc_attr_e( 'Add details..', 'bluedolphin-lms' ); ?>" class="bdlms-cs-input">
									<div class="bdlms-cs-action<?php echo $skills_you_gain_count <= 1 ? ' hidden' : ''; ?>">
										<a href="javascript:;">
											<svg class="icon" width="12" height="12">
												<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
											</svg>											
										</a>
									</div>	
								</div>
							</li>
							<?php endforeach; ?>
						</ul>
						<button class="button" data-add_more="true"><?php esc_html_e( 'Add More', 'bluedolphin-lms' ); ?></button>
					</div>
				</div>
			</div>
			<div class="bdlms-cs-row">
				<div class="bdlms-cs-col-left"><?php esc_html_e( 'This Course Includes', 'bluedolphin-lms' ); ?></div>
				<div class="bdlms-cs-col-right">
					<div class="bdlms-cs-drag-list">
						<ul class="cs-drag-list cs-drag-list-group">
						<?php
						$course_includes       = $information['course_includes'];
						$course_includes_count = count( $course_includes );
						foreach ( $course_includes as $value ) :
							?>
							<li>
								<div class="bdlms-cs-drag-field">
									<div class="bdlms-options-drag">
										<svg class="icon" width="8" height="13">
											<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
										</svg>
									</div>
									<input type="text" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[information][course_includes][]" placeholder="<?php echo esc_attr_e( 'Add details..', 'bluedolphin-lms' ); ?>" class="bdlms-cs-input">
									<div class="bdlms-cs-action<?php echo $course_includes_count <= 1 ? ' hidden' : ''; ?>">
										<a href="javascript:;">
											<svg class="icon" width="12" height="12">
												<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
											</svg>											
										</a>
									</div>	
								</div>
							</li>
							<?php endforeach; ?>
						</ul>
						<button class="button" data-add_more="true"><?php esc_html_e( 'Add More', 'bluedolphin-lms' ); ?></button>
					</div>
				</div>
			</div>
			<div class="bdlms-cs-row">
				<div class="bdlms-cs-col-left"><?php esc_html_e( 'FAQs', 'bluedolphin-lms' ); ?></div>
				<div class="bdlms-cs-col-right">
					<div class="bdlms-cs-drag-list">
						<ul class="cs-drag-list cs-drag-list-group cs-no-drag">
						<?php
						$faq_question       = $information['faq_question'];
						$faq_answer         = $information['faq_answer'];
						$faq_question_count = count( $faq_question );
						foreach ( $faq_question as $key => $value ) :
							?>
							<li>
								<ul class="cs-drag-list cs-no-drag">
									<li>
										<div class="bdlms-cs-drag-field">
											<input type="text" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[information][faq_question][]" placeholder="<?php echo esc_attr_e( 'Question', 'bluedolphin-lms' ); ?>" class="bdlms-cs-input">
											<div class="bdlms-cs-action<?php echo $faq_question_count <= 1 ? ' hidden' : ''; ?>">
												<a href="javascript:;">
													<svg class="icon" width="12" height="12">
														<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
													</svg>											
												</a>
											</div>	
										</div>
									</li>
									<li>
										<div class="bdlms-cs-drag-field">
											<textarea name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[information][faq_answer][]" placeholder="<?php esc_attr_e( 'Answer', 'bluedolphin-lms' ); ?>" class="bdlms-cs-input"><?php echo isset( $faq_answer[ $key ] ) ? esc_textarea( $faq_answer[ $key ] ) : ''; ?></textarea>
										</div>
									</li>
								</ul>	
							</li>
							<?php endforeach; ?>
						</ul>						
						<button class="button" data-add_more="true"><?php esc_html_e( 'Add More', 'bluedolphin-lms' ); ?></button>
					</div>
				</div>
			</div>
		</div>
		<div class="bdlms-tab-content" data-tab="assessment">
			<div class="bdlms-cs-row">
				<div class="bdlms-cs-col-left"><?php esc_html_e( 'Evaluation', 'bluedolphin-lms' ); ?></div>
				<div class="bdlms-cs-col-right">
					<div class="bdlms-cs-drag-list">
						<ul class="cs-drag-list">
							<?php foreach ( BlueDolphin\Lms\bdlms_evaluation_list() as $key => $evaluation ) : ?>
								<li>
									<label><input type="radio" value="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[assessment][evaluation]"<?php checked( $key, $assessment['evaluation'] ); ?>> <?php echo isset( $evaluation['label'] ) ? esc_html( $evaluation['label'] ) : ''; ?></label>
									<?php if ( ! empty( $evaluation['notice'] ) ) : ?>
										<div class="bdlms-cs-passing-grade">
											<?php
											echo wp_kses(
												$evaluation['notice'],
												array(
													'a' => array(
														'href'   => true,
														'target' => true,
														'class'  => true,
													),
												)
											);
											?>
										</div>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</div>
			<div class="bdlms-cs-row">
				<div class="bdlms-cs-col-left"><?php esc_html_e( 'Passing Grade', 'bluedolphin-lms' ); ?> (%)</div>
				<div class="bdlms-cs-col-right">
					<div class="bdlms-cs-drag-list">
						<ul class="cs-drag-list">
							<li>
								<input type="number" value="<?php echo esc_attr( $assessment['passing_grade'] ); ?>" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[assessment][passing_grade]" min="0" step="1">
							</li>
							<li><?php esc_html_e( 'The conditions that must be achieved to finish the course.', 'bluedolphin-lms' ); ?></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div class="bdlms-tab-content" data-tab="author">
			<div class="bdlms-cs-row">
				<div class="bdlms-cs-col-left"><?php esc_html_e( 'Author', 'bluedolphin-lms' ); ?></div>
				<div class="bdlms-cs-col-right">
					<div class="bdlms-cs-drag-list">
						<ul class="cs-drag-list">
							<li>
								<?php
									wp_dropdown_users(
										array(
											'capability' => array( $post_type_object->cap->edit_posts ),
											'name'       => 'post_author_override',
											'selected'   => empty( $post->ID ) ? $user_ID : $post->post_author,
											'include_selected' => true,
											'show'       => 'display_name_with_login',
										)
									);
									?>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div class="bdlms-tab-content" data-tab="downloadable-materials">
			<div class="bdlms-cs-download">
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
								<li><strong><?php esc_html_e( 'File Title', 'bluedolphin-lms' ); ?></strong></li>
								<li><strong><?php esc_html_e( 'Method', 'bluedolphin-lms' ); ?></strong></li>
								<li><strong><?php esc_html_e( 'Action', 'bluedolphin-lms' ); ?></strong></li>
							</ul>
							<?php
								require_once BDLMS_TEMPLATEPATH . '/admin/course/materials-item.php';
							?>
						</div>
					</div>
					<div class="bdlms-materials-box__footer">
						<button type="button" class="button"><?php esc_html_e( 'Add More Materials', 'bluedolphin-lms' ); ?></button>
					</div>
				</div>				
			</div>
		</div>
	</div>	
</div>
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