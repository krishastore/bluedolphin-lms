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
				<div class="bdlms-cs-col-left">
					Course Requirement
				</div>
				<div class="bdlms-cs-col-right">
					<div class="bdlms-cs-drag-list">
						<ul class="cs-drag-list">
							<li>
								<div class="bdlms-cs-drag-field">
									<div class="bdlms-options-drag">
										<svg class="icon" width="8" height="13">
											<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
										</svg>
									</div>
									<input type="text" placeholder="Add details.." class="bdlms-cs-input">
									<div class="bdlms-cs-action">
										<a href="javascript:;">
											<svg class="icon" width="12" height="12">
												<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
											</svg>											
										</a>
									</div>	
								</div>
							</li>
						</ul>
						<button class="button">Add More</button>
					</div>
				</div>
			</div>
			<div class="bdlms-cs-row">
				<div class="bdlms-cs-col-left">
					What You'll Learn
				</div>
				<div class="bdlms-cs-col-right">
					<div class="bdlms-cs-drag-list">
						<ul class="cs-drag-list">
							<li>
								<div class="bdlms-cs-drag-field">
									<div class="bdlms-options-drag">
										<svg class="icon" width="8" height="13">
											<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
										</svg>
									</div>
									<input type="text" placeholder="Add details.." class="bdlms-cs-input">
									<div class="bdlms-cs-action">
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
									<div class="bdlms-options-drag">
										<svg class="icon" width="8" height="13">
											<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
										</svg>
									</div>
									<input type="text" placeholder="Add details.." class="bdlms-cs-input">
									<div class="bdlms-cs-action">
										<a href="javascript:;">
											<svg class="icon" width="12" height="12">
												<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
											</svg>											
										</a>
									</div>	
								</div>
							</li>
						</ul>
						<button class="button">Add More</button>
					</div>
				</div>
			</div>
			<div class="bdlms-cs-row">
				<div class="bdlms-cs-col-left">
					Skills You'll Gain
				</div>
				<div class="bdlms-cs-col-right">
					<div class="bdlms-cs-drag-list">
						<ul class="cs-drag-list">
							<li>
								<div class="bdlms-cs-drag-field">
									<div class="bdlms-options-drag">
										<svg class="icon" width="8" height="13">
											<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
										</svg>
									</div>
									<input type="text" placeholder="Add details.." class="bdlms-cs-input">
									<div class="bdlms-cs-action">
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
									<div class="bdlms-options-drag">
										<svg class="icon" width="8" height="13">
											<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
										</svg>
									</div>
									<input type="text" placeholder="Add details.." class="bdlms-cs-input">
									<div class="bdlms-cs-action">
										<a href="javascript:;">
											<svg class="icon" width="12" height="12">
												<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
											</svg>											
										</a>
									</div>	
								</div>
							</li>
						</ul>
						<button class="button">Add More</button>
					</div>
				</div>
			</div>
			<div class="bdlms-cs-row">
				<div class="bdlms-cs-col-left">
					This Course Includes
				</div>
				<div class="bdlms-cs-col-right">
					<div class="bdlms-cs-drag-list">
						<ul class="cs-drag-list">
							<li>
								<div class="bdlms-cs-drag-field">
									<div class="bdlms-options-drag">
										<svg class="icon" width="8" height="13">
											<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
										</svg>
									</div>
									<input type="text" placeholder="Add details.." class="bdlms-cs-input">
									<div class="bdlms-cs-action">
										<a href="javascript:;">
											<svg class="icon" width="12" height="12">
												<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
											</svg>											
										</a>
									</div>	
								</div>
							</li>
						</ul>
						<button class="button">Add More</button>
					</div>
				</div>
			</div>
			<div class="bdlms-cs-row">
				<div class="bdlms-cs-col-left">
					FAQs
				</div>
				<div class="bdlms-cs-col-right">
					<div class="bdlms-cs-drag-list">
						<ul class="cs-drag-list">
							<li>
								<ul class="cs-drag-list">
									<li>
										<div class="bdlms-cs-drag-field">
											<input type="text" placeholder="Question" class="bdlms-cs-input">
											<div class="bdlms-cs-action">
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
											<textarea placeholder="Answer" class="bdlms-cs-input"></textarea>
										</div>
									</li>
								</ul>	
							</li>
							<li>
								<ul class="cs-drag-list">
									<li>
										<div class="bdlms-cs-drag-field">
											<input type="text" placeholder="Question" class="bdlms-cs-input">
											<div class="bdlms-cs-action">
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
											<textarea placeholder="Answer" class="bdlms-cs-input"></textarea>
										</div>
									</li>
								</ul>	
							</li>
						</ul>						
						<button class="button">Add More</button>
					</div>
				</div>
			</div>
		</div>
		<div class="bdlms-tab-content" data-tab="assessment">
			<div class="bdlms-cs-row">
				<div class="bdlms-cs-col-left">
					Evaluation
				</div>
				<div class="bdlms-cs-col-right">
					<div class="bdlms-cs-drag-list">
						<ul class="cs-drag-list">
							<li>
								<label><input type="radio" name="bdlms_cs_setting"> Evaluate via lessons</label>
							</li>
							<li>
								<label><input type="radio" name="bdlms_cs_setting"> Evaluate via results of the final quiz</label>
							</li>
							<li>
								<div class="bdlms-cs-passing-grade">
									Passing Grade: 80% - Edit <a href="#">Quiz Name</a>
								</div>
							</li>
							<li>
								<label><input type="radio" name="bdlms_cs_setting"> Evaluate via passed quizzes</label>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="bdlms-cs-row">
				<div class="bdlms-cs-col-left">
					Passing Grade (%)
				</div>
				<div class="bdlms-cs-col-right">
					<div class="bdlms-cs-drag-list">
						<ul class="cs-drag-list">
							<li>
								<input type="number">
							</li>
							<li>
								The conditions that must be achieved to finish the course.
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div class="bdlms-tab-content" data-tab="author">
			<div class="bdlms-cs-row">
				<div class="bdlms-cs-col-left">
					Author
				</div>
				<div class="bdlms-cs-col-right">
					<div class="bdlms-cs-drag-list">
						<ul class="cs-drag-list">
							<li>
								<select>
									<option>KrishaWeb</option>
									<option>option 1</option>
								</select>
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
						<p>Max File: 2   |   Max Size: 2MB   |   Format: .PDF, .TXT</p>
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
							<div class="bdlms-materials-list-item">
								<ul>
									<li class="assignment-title">Assignment</li>
									<li class="assignment-type">Upload</li>
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
								<div class="bdlms-materials-item hidden">
									<div class="bdlms-media-choose">
										<label><?php esc_html_e( 'File Title', 'bluedolphin-lms' ); ?></label>
										<input type="text" class="material-file-title" placeholder="<?php esc_attr_e( 'Enter File Title', 'bluedolphin-lms' ); ?>">
									</div>
									<div class="bdlms-media-choose material-type">
										<label><?php esc_html_e( 'Method', 'bluedolphin-lms' ); ?></label>
										<select>
											<option><?php esc_html_e( 'Upload', 'bluedolphin-lms' ); ?></option>
											<option><?php esc_html_e( 'External', 'bluedolphin-lms' ); ?></option>
										</select>
									</div>
									<div class="bdlms-media-choose">
										<label><?php esc_html_e( 'Choose File', 'bluedolphin-lms' ); ?></label>
										<div class="bdlms-media-file">
											<a href="javascript:;" class="bdlms-open-media button"><?php esc_html_e( 'Change File', 'bluedolphin-lms' ); ?></a>
											<span class="bdlms-media-name">
												<a href="#" target="_blank">test file name</a>
											</span>
											<input type="hidden">
										</div>
									</div>
									<div class="bdlms-media-choose">
										<label><?php esc_html_e( 'File URL', 'bluedolphin-lms' ); ?></label>
										<input type="text" placeholder="<?php esc_attr_e( 'Enter File URL', 'bluedolphin-lms' ); ?>">
									</div>
									<div class="bdlms-media-choose">
										<button type="button" class="button button-primary bdlms-save-material">
											<?php esc_html_e( 'Save', 'bluedolphin-lms' ); ?>
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
							<div class="bdlms-materials-list-item">
								<ul>
									<li class="assignment-title">Assignment</li>
									<li class="assignment-type">Upload</li>
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
								<div class="bdlms-materials-item hidden">
									<div class="bdlms-media-choose">
										<label><?php esc_html_e( 'File Title', 'bluedolphin-lms' ); ?></label>
										<input type="text" class="material-file-title" placeholder="<?php esc_attr_e( 'Enter File Title', 'bluedolphin-lms' ); ?>">
									</div>
									<div class="bdlms-media-choose material-type">
										<label><?php esc_html_e( 'Method', 'bluedolphin-lms' ); ?></label>
										<select>
											<option><?php esc_html_e( 'Upload', 'bluedolphin-lms' ); ?></option>
											<option><?php esc_html_e( 'External', 'bluedolphin-lms' ); ?></option>
										</select>
									</div>
									<div class="bdlms-media-choose">
										<label><?php esc_html_e( 'Choose File', 'bluedolphin-lms' ); ?></label>
										<div class="bdlms-media-file">
											<a href="javascript:;" class="bdlms-open-media button"><?php esc_html_e( 'Change File', 'bluedolphin-lms' ); ?></a>
											<span class="bdlms-media-name">
												<a href="#" target="_blank">test file name</a>
											</span>
											<input type="hidden">
										</div>
									</div>
									<div class="bdlms-media-choose">
										<label><?php esc_html_e( 'File URL', 'bluedolphin-lms' ); ?></label>
										<input type="text" placeholder="<?php esc_attr_e( 'Enter File URL', 'bluedolphin-lms' ); ?>">
									</div>
									<div class="bdlms-media-choose">
										<button type="button" class="button button-primary bdlms-save-material">
											<?php esc_html_e( 'Save', 'bluedolphin-lms' ); ?>
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
						</div>
						<div class="bdlms-materials-item">
							<div class="bdlms-media-choose">
								<label><?php esc_html_e( 'File Title', 'bluedolphin-lms' ); ?></label>
								<input type="text" class="material-file-title" placeholder="<?php esc_attr_e( 'Enter File Title', 'bluedolphin-lms' ); ?>">
							</div>
							<div class="bdlms-media-choose material-type">
								<label><?php esc_html_e( 'Method', 'bluedolphin-lms' ); ?></label>
								<select>
									<option><?php esc_html_e( 'Upload', 'bluedolphin-lms' ); ?></option>
									<option><?php esc_html_e( 'External', 'bluedolphin-lms' ); ?></option>
								</select>
							</div>
							<div class="bdlms-media-choose">
								<label><?php esc_html_e( 'Choose File', 'bluedolphin-lms' ); ?></label>
								<div class="bdlms-media-file">
									<a href="javascript:;" class="bdlms-open-media button"><?php esc_html_e( 'Change File', 'bluedolphin-lms' ); ?></a>
									<span class="bdlms-media-name">
										<a href="#" target="_blank">test file name</a>
									</span>
									<input type="hidden">
								</div>
							</div>
							<div class="bdlms-media-choose">
								<label><?php esc_html_e( 'File URL', 'bluedolphin-lms' ); ?></label>
								<input type="text" placeholder="<?php esc_attr_e( 'Enter File URL', 'bluedolphin-lms' ); ?>">
							</div>
							<div class="bdlms-media-choose">
								<button type="button" class="bdlms-remove-material">
									<svg class="icon" width="12" height="12">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
									</svg>
									<?php esc_html_e( 'Delete', 'bluedolphin-lms' ); ?>
								</button>
							</div>
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
