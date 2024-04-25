<?php
/**
 * Template: Curriculum Metabox.
 *
 * @package BlueDolphin\Lms
 */

?>
<?php wp_nonce_field( BDLMS_BASEFILE, 'bdlms_nonce', false ); ?>
<div class="bdlms-quiz-qus-wrap">
	<div class="bdlms-snackbar-notice"><p></p></div>
	<ul class="bdlms-quiz-qus-list">
		<li>
			<div class="bdlms-quiz-qus-item">
				<div class="bdlms-quiz-qus-item__header">
					<div class="bdlms-options-drag">
						<svg class="icon" width="8" height="13">
							<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
						</svg>
					</div>
					<div class="bdlms-quiz-qus-name">
						<input type="text" placeholder="<?php esc_attr_e( 'Create New Section Name', 'bluedolphin-lms' ); ?>" value="">
						<div class="bdlms-quiz-qus-point">
							<ul>
								<li class="lesson-count">
									<svg class="icon" width="16" height="16">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#book-bookmark"></use>
									</svg>
									0
								</li>
								<li class="quiz-count">
									<svg class="icon" width="16" height="16">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#clock"></use>
									</svg>
									0
								</li>
								<li class="files-count">
									<svg class="icon" width="16" height="16">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#clip"></use>
									</svg>
									0
								</li>
							</ul>
						</div>
					</div>
					<div class="bdlms-quiz-qus-toggle" data-accordion="true">
						<svg class="icon" width="18" height="18">
							<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#down-arrow"></use>
						</svg>
					</div>
				</div>
				<div class="bdlms-quiz-qus-item__body">
					<div class="bdlms-curriculum-desc">
						<textarea placeholder="<?php esc_attr_e( 'Section description..', 'bluedolphin-lms' ); ?>"></textarea>
					</div>
					<div class="bdlms-curriculum-item-list">
						<div class="bdlms-curriculum-item">
							<div class="bdlms-curriculum-item-drag">
								<svg class="icon" width="8" height="13">
									<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
								</svg>
							</div>
							<div class="bdlms-curriculum-dd">
								<button class="bdlms-curriculum-dd-button">
									<svg class="icon lesson-icon" width="16" height="16">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#book-bookmark"></use>
									</svg>
									<svg class="icon quiz-icon hidden" width="16" height="16">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#clock"></use>
									</svg>
									<svg class="icon files-icon hidden" width="16" height="16">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#clip"></use>
									</svg>
									<svg class="icon down-arrow-icon" width="18" height="18">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#down-arrow2"></use>
									</svg>
								</button>
								<ul class="bdlms-curriculum-type">									
									<li class="active" data-type="lesson">
										<svg class="icon" width="16" height="16">
											<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#book-bookmark"></use>
										</svg>
										<span><?php esc_html_e( 'Lesson', 'bluedolphin-lms' ); ?></span>
									</li>
									<li data-type="quiz">
										<svg class="icon" width="16" height="16">
											<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#clock"></use>
										</svg>
										<span><?php esc_html_e( 'Quiz', 'bluedolphin-lms' ); ?></span>
									</li>
									<li data-type="files">
										<svg class="icon" width="16" height="16">
											<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#clip"></use>
										</svg>
										<span><?php esc_html_e( 'Files', 'bluedolphin-lms' ); ?></span>
									</li>
								</ul>
							</div>
							<input type="text" class="bdlms-curriculum-item-name" readonly placeholder="Add A New Attachment" value="Introduction to Design">
							<div class="bdlms-curriculum-item-action">
								<a href="javascript:;" class="curriculum-toggle-item">
									<svg class="icon" width="12" height="12">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#eye"></use>
									</svg>
								</a>
								<a href="javascript:;" class="curriculum-edit-item">
									<svg class="icon" width="12" height="12">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#file-edit"></use>
									</svg>
								</a>
								<a href="javascript:;" class="curriculum-remove-item">
									<svg class="icon" width="12" height="12">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
									</svg>
								</a>
							</div>
						</div>
						<div class="bdlms-curriculum-item">
							<div class="bdlms-curriculum-item-drag">
								<svg class="icon plus-icon" width="8" height="13">
									<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#plus-icon"></use>
								</svg>
								<svg class="icon drag-icon hidden" width="8" height="13">
									<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
								</svg>
							</div>
							<div class="bdlms-curriculum-dd">
								<button class="bdlms-curriculum-dd-button">
									<svg class="icon lesson-icon" width="16" height="16">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#book-bookmark"></use>
									</svg>
									<svg class="icon quiz-icon hidden" width="16" height="16">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#clock"></use>
									</svg>
									<svg class="icon files-icon hidden" width="16" height="16">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#clip"></use>
									</svg>
									<svg class="icon down-arrow-icon" width="18" height="18">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#down-arrow2"></use>
									</svg>
								</button>
								<ul class="bdlms-curriculum-type">									
									<li class="active" data-type="lesson">
										<svg class="icon" width="16" height="16">
											<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#book-bookmark"></use>
										</svg>
										<span><?php esc_html_e( 'Lesson', 'bluedolphin-lms' ); ?></span>
									</li>
									<li data-type="quiz">
										<svg class="icon" width="16" height="16">
											<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#clock"></use>
										</svg>
										<span><?php esc_html_e( 'Quiz', 'bluedolphin-lms' ); ?></span>
									</li>
									<li data-type="files">
										<svg class="icon" width="16" height="16">
											<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#clip"></use>
										</svg>
										<span><?php esc_html_e( 'Files', 'bluedolphin-lms' ); ?></span>
									</li>
								</ul>
							</div>
							<input type="text" class="bdlms-curriculum-item-name" placeholder="<?php esc_attr_e( 'Add A New Attachment', 'bluedolphin-lms' ); ?>">
							<div class="bdlms-curriculum-item-action hidden">
								<a href="javascript:;" class="curriculum-toggle-item">
									<svg class="icon" width="12" height="12">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#eye"></use>
									</svg>
								</a>
								<a href="javascript:;" class="curriculum-edit-item">
									<svg class="icon" width="12" height="12">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#file-edit"></use>
									</svg>
								</a>
								<a href="javascript:;" class="curriculum-remove-item">
									<svg class="icon" width="12" height="12">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
									</svg>
								</a>
							</div>
						</div>
					</div>
					<div class="bdlms-quiz-qus-item__footer">
						<a href="javascript:;" class="button"><?php esc_html_e( 'Select Items', 'bluedolphin-lms' ); ?></a>
						<a href="javascript:;" class="bdlms-delete-link">
							<svg class="icon" width="12" height="12">
								<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
							</svg>
							<?php esc_html_e( 'Delete', 'bluedolphin-lms' ); ?>
						</a>
					</div>
				</div>
			</div>
		</li>
	</ul>
	<div class="bdlms-quiz-qus-footer">
		<a href="javascript:;" class="button button-primary"><?php esc_html_e( 'Add New Section', 'bluedolphin-lms' ); ?></a>
	</div>
</div>
