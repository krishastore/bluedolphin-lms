<?php
/**
 * Template: Course setting - Course Information.
 *
 * @package ST\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="stlms-tab-content<?php echo esc_attr( $active_class ); ?>" data-tab="course-info">
	<div class="stlms-cs-row">
		<div class="stlms-cs-col-left"><?php esc_html_e( 'Course Requirement', 'skilltriks' ); ?></div>
		<div class="stlms-cs-col-right">
			<div class="stlms-cs-drag-list">
				<ul class="cs-drag-list cs-drag-list-group">
					<?php
					$requirement       = $information['requirement'];
					$requirement_count = count( $requirement );
					foreach ( $requirement as $requirement ) :
						?>
						<li>
							<div class="stlms-cs-drag-field">
								<div class="stlms-options-drag">
									<svg class="icon" width="8" height="13">
										<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
									</svg>
								</div>
								<input type="text" value="<?php echo esc_attr( $requirement ); ?>" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[information][requirement][]" placeholder="<?php echo esc_attr_e( 'Add details..', 'skilltriks' ); ?>" class="stlms-cs-input">
								<div class="stlms-cs-action<?php echo $requirement_count <= 1 ? ' hidden' : ''; ?>">
									<a href="javascript:;">
										<svg class="icon" width="12" height="12">
											<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
										</svg>											
									</a>
								</div>	
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
				<button class="button" data-add_more="true"><?php esc_html_e( 'Add More', 'skilltriks' ); ?></button>
			</div>
		</div>
	</div>
	<div class="stlms-cs-row">
		<div class="stlms-cs-col-left"><?php esc_html_e( 'What You\'ll Learn', 'skilltriks' ); ?></div>
		<div class="stlms-cs-col-right">
			<div class="stlms-cs-drag-list">
				<ul class="cs-drag-list cs-drag-list-group">
					<?php
					$what_you_learn       = $information['what_you_learn'];
					$what_you_learn_count = count( $what_you_learn );
					foreach ( $what_you_learn as $value ) :
						?>
					<li>
						<div class="stlms-cs-drag-field">
							<div class="stlms-options-drag">
								<svg class="icon" width="8" height="13">
									<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
								</svg>
							</div>
							<input type="text" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[information][what_you_learn][]" placeholder="<?php echo esc_attr_e( 'Add details..', 'skilltriks' ); ?>" class="stlms-cs-input">
							<div class="stlms-cs-action<?php echo $what_you_learn_count <= 1 ? ' hidden' : ''; ?>">
								<a href="javascript:;">
									<svg class="icon" width="12" height="12">
										<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
									</svg>											
								</a>
							</div>	
						</div>
					</li>
					<?php endforeach; ?>
				</ul>
				<button class="button" data-add_more="true"><?php esc_html_e( 'Add More', 'skilltriks' ); ?></button>
			</div>
		</div>
	</div>
	<div class="stlms-cs-row">
		<div class="stlms-cs-col-left"><?php esc_html_e( 'Skills You\'ll Gain', 'skilltriks' ); ?></div>
		<div class="stlms-cs-col-right">
			<div class="stlms-cs-drag-list">
				<ul class="cs-drag-list cs-drag-list-group">
				<?php
				$skills_you_gain       = $information['skills_you_gain'];
				$skills_you_gain_count = count( $skills_you_gain );
				foreach ( $skills_you_gain as $value ) :
					?>
					<li>
						<div class="stlms-cs-drag-field">
							<div class="stlms-options-drag">
								<svg class="icon" width="8" height="13">
									<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
								</svg>
							</div>
							<input type="text" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[information][skills_you_gain][]" placeholder="<?php echo esc_attr_e( 'Add details..', 'skilltriks' ); ?>" class="stlms-cs-input">
							<div class="stlms-cs-action<?php echo $skills_you_gain_count <= 1 ? ' hidden' : ''; ?>">
								<a href="javascript:;">
									<svg class="icon" width="12" height="12">
										<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
									</svg>											
								</a>
							</div>	
						</div>
					</li>
					<?php endforeach; ?>
				</ul>
				<button class="button" data-add_more="true"><?php esc_html_e( 'Add More', 'skilltriks' ); ?></button>
			</div>
		</div>
	</div>
	<div class="stlms-cs-row">
		<div class="stlms-cs-col-left"><?php esc_html_e( 'This Course Includes', 'skilltriks' ); ?></div>
		<div class="stlms-cs-col-right">
			<div class="stlms-cs-drag-list">
				<ul class="cs-drag-list cs-drag-list-group">
				<?php
				$course_includes       = $information['course_includes'];
				$course_includes_count = count( $course_includes );
				foreach ( $course_includes as $value ) :
					?>
					<li>
						<div class="stlms-cs-drag-field">
							<div class="stlms-options-drag">
								<svg class="icon" width="8" height="13">
									<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
								</svg>
							</div>
							<input type="text" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[information][course_includes][]" placeholder="<?php echo esc_attr_e( 'Add details..', 'skilltriks' ); ?>" class="stlms-cs-input">
							<div class="stlms-cs-action<?php echo $course_includes_count <= 1 ? ' hidden' : ''; ?>">
								<a href="javascript:;">
									<svg class="icon" width="12" height="12">
										<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
									</svg>											
								</a>
							</div>	
						</div>
					</li>
					<?php endforeach; ?>
				</ul>
				<button class="button" data-add_more="true"><?php esc_html_e( 'Add More', 'skilltriks' ); ?></button>
			</div>
		</div>
	</div>
	<div class="stlms-cs-row">
		<div class="stlms-cs-col-left"><?php esc_html_e( 'FAQs', 'skilltriks' ); ?></div>
		<div class="stlms-cs-col-right">
			<div class="stlms-cs-drag-list">
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
								<div class="stlms-cs-drag-field">
									<input type="text" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[information][faq_question][]" placeholder="<?php echo esc_attr_e( 'Question', 'skilltriks' ); ?>" class="stlms-cs-input">
									<div class="stlms-cs-action<?php echo $faq_question_count <= 1 ? ' hidden' : ''; ?>">
										<a href="javascript:;">
											<svg class="icon" width="12" height="12">
												<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
											</svg>											
										</a>
									</div>	
								</div>
							</li>
							<li>
								<div class="stlms-cs-drag-field">
									<textarea name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[information][faq_answer][]" placeholder="<?php esc_attr_e( 'Answer', 'skilltriks' ); ?>" class="stlms-cs-input"><?php echo isset( $faq_answer[ $key ] ) ? esc_textarea( $faq_answer[ $key ] ) : ''; ?></textarea>
								</div>
							</li>
						</ul>	
					</li>
					<?php endforeach; ?>
				</ul>						
				<button class="button" data-add_more="true"><?php esc_html_e( 'Add More', 'skilltriks' ); ?></button>
			</div>
		</div>
	</div>
</div>
