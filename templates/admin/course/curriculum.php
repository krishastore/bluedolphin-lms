<?php
/**
 * Template: Curriculum Metabox.
 *
 * @package BlueDolphin\Lms
 */

?>
<div class="bdlms-quiz-qus-wrap">
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
						<input type="text" placeholder="Create New Section Name" value="Create New Section Name">
						<div class="bdlms-quiz-qus-point">
							<ul>
								<li>
									<svg class="icon" width="16" height="16">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#book-bookmark"></use>
									</svg>
									1
								</li>
								<li>
									<svg class="icon" width="16" height="16">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#clock"></use>
									</svg>
									1
								</li>
								<li>
									<svg class="icon" width="16" height="16">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#clip"></use>
									</svg>
									1
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
						<textarea placeholder="Section description.."></textarea>
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
									<svg class="icon" width="16" height="16">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#book-bookmark"></use>
									</svg>
									<svg class="icon" width="18" height="18">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#down-arrow2"></use>
									</svg>
								</button>
								<ul>									
									<li class="active">
										<svg class="icon" width="16" height="16">
											<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#book-bookmark"></use>
										</svg>
										<span>Lesson</span>
									</li>
									<li>
										<svg class="icon" width="16" height="16">
											<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#clock"></use>
										</svg>
										<span>Quiz</span>
									</li>
									<li>
										<svg class="icon" width="16" height="16">
											<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#clip"></use>
										</svg>
										<span>Files</span>
									</li>
								</ul>
							</div>
							<input type="text" class="bdlms-curriculum-item-name" readonly placeholder="Add A New Attachment" value="Introduction to Design">
							<div class="bdlms-curriculum-item-action">
								<a href="javascript:;">
									<svg class="icon" width="12" height="12">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#eye"></use>
									</svg>
								</a>
								<a href="javascript:;">
									<svg class="icon" width="12" height="12">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#file-edit"></use>
									</svg>
								</a>
								<a href="javascript:;">
									<svg class="icon" width="12" height="12">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
									</svg>
								</a>
							</div>
						</div>
						<div class="bdlms-curriculum-item">
							<div class="bdlms-curriculum-item-drag">
								<svg class="icon" width="8" height="13">
									<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
								</svg>
							</div>
							<div class="bdlms-curriculum-dd">
								<button class="bdlms-curriculum-dd-button">
									<svg class="icon" width="16" height="16">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#book-bookmark"></use>
									</svg>
									<svg class="icon" width="18" height="18">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#down-arrow2"></use>
									</svg>
								</button>
								<ul>									
									<li class="active">
										<svg class="icon" width="16" height="16">
											<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#book-bookmark"></use>
										</svg>
										<span>Lesson</span>
									</li>
									<li>
										<svg class="icon" width="16" height="16">
											<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#clock"></use>
										</svg>
										<span>Quiz</span>
									</li>
									<li>
										<svg class="icon" width="16" height="16">
											<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#clip"></use>
										</svg>
										<span>Files</span>
									</li>
								</ul>
							</div>
							<input type="text" class="bdlms-curriculum-item-name" readonly placeholder="Add A New Attachment" value="Introduction to Design">
							<div class="bdlms-curriculum-item-action">
								<a href="javascript:;">
									<svg class="icon" width="12" height="12">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#eye"></use>
									</svg>
								</a>
								<a href="javascript:;">
									<svg class="icon" width="12" height="12">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#file-edit"></use>
									</svg>
								</a>
								<a href="javascript:;">
									<svg class="icon" width="12" height="12">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
									</svg>
								</a>
							</div>
						</div>
						<div class="bdlms-curriculum-item">
							<div class="bdlms-curriculum-item-drag">
								<svg class="icon" width="8" height="13">
									<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
								</svg>
							</div>
							<div class="bdlms-curriculum-dd">
								<button class="bdlms-curriculum-dd-button">
									<svg class="icon" width="16" height="16">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#book-bookmark"></use>
									</svg>
									<svg class="icon" width="18" height="18">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#down-arrow2"></use>
									</svg>
								</button>
								<ul>									
									<li class="active">
										<svg class="icon" width="16" height="16">
											<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#book-bookmark"></use>
										</svg>
										<span>Lesson</span>
									</li>
									<li>
										<svg class="icon" width="16" height="16">
											<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#clock"></use>
										</svg>
										<span>Quiz</span>
									</li>
									<li>
										<svg class="icon" width="16" height="16">
											<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#clip"></use>
										</svg>
										<span>Files</span>
									</li>
								</ul>
							</div>
							<input type="text" class="bdlms-curriculum-item-name" readonly placeholder="Add A New Attachment" value="Introduction to Design">
							<div class="bdlms-curriculum-item-action">
								<a href="javascript:;">
									<svg class="icon" width="12" height="12">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#eye"></use>
									</svg>
								</a>
								<a href="javascript:;">
									<svg class="icon" width="12" height="12">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#file-edit"></use>
									</svg>
								</a>
								<a href="javascript:;">
									<svg class="icon" width="12" height="12">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
									</svg>
								</a>
							</div>
						</div>
						<div class="bdlms-curriculum-item">
							<div class="bdlms-curriculum-item-drag">
								<svg class="icon" width="8" height="13">
									<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#plus-icon"></use>
								</svg>
							</div>
							<div class="bdlms-curriculum-dd">
								<button class="bdlms-curriculum-dd-button">
									<svg class="icon" width="16" height="16">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#book-bookmark"></use>
									</svg>
									<svg class="icon" width="18" height="18">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#down-arrow2"></use>
									</svg>
								</button>
								<ul>									
									<li class="active">
										<svg class="icon" width="16" height="16">
											<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#book-bookmark"></use>
										</svg>
										<span>Lesson</span>
									</li>
									<li>
										<svg class="icon" width="16" height="16">
											<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#clock"></use>
										</svg>
										<span>Quiz</span>
									</li>
									<li>
										<svg class="icon" width="16" height="16">
											<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#clip"></use>
										</svg>
										<span>Files</span>
									</li>
								</ul>
							</div>
							<input type="text" class="bdlms-curriculum-item-name" placeholder="Add A New Attachment">
							<div class="bdlms-curriculum-item-action">
								<a href="javascript:;">
									<svg class="icon" width="12" height="12">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#eye"></use>
									</svg>
								</a>
								<a href="javascript:;">
									<svg class="icon" width="12" height="12">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#file-edit"></use>
									</svg>
								</a>
								<a href="javascript:;">
									<svg class="icon" width="12" height="12">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
									</svg>
								</a>
							</div>
						</div>
					</div>
					<div class="bdlms-quiz-qus-item__footer">
						<a href="javascript:;" class="button">
							Select Items
						</a>
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
