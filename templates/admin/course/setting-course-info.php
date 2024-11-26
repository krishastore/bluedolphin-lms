<?php
/**
 * Template: Course setting - Course Information.
 *
 * @package BlueDolphin\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="bdlms-tab-content<?php echo esc_attr( $active_class ); ?>" data-tab="course-info">
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