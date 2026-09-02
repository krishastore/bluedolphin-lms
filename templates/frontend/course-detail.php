<?php
/**
 * Template: Course Detail Page
 *
 * @package ST\Lms
 *
 * phpcs:disable PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$course_id        = ! empty( $args['course_id'] ) ? $args['course_id'] : 0;
$curriculums_list = ! empty( $args['course_data']['curriculums'] ) ? $args['course_data']['curriculums'] : array();
?>

<div class="stlms-wrap">
	<?php require_once STLMS_TEMPLATEPATH . '/frontend/sub-header.php'; ?>
	<div class="stlms-course-banner" style="background-image: url('<?php echo esc_url( STLMS_ASSETS ) . '/images/course-detail-banner.jpg'; ?>')">
		<div class="stlms-container">
			<ul class="stlms-breadcrumb">
				<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'skilltriks' ); ?></a></li>
				<li><a href="<?php echo esc_url( \ST\Lms\get_page_url( 'courses' ) ); ?>"><?php esc_html_e( 'Courses', 'skilltriks' ); ?></a></li>
				<?php the_title( '<li class="active">', '</li>' ); ?>
			</ul>
			<?php
			$get_terms  = get_the_terms( get_the_ID(), \ST\Lms\STLMS_COURSE_CATEGORY_TAX );
			$terms_name = join( ', ', wp_list_pluck( $get_terms, 'name' ) );
			$terms_id   = wp_list_pluck( $get_terms, 'term_id' );
			$author_id  = (int) get_post_field( 'post_author', $course_id );
			?>
			<?php the_title( '<h1 class="stlms-course-title">', '</h1>' ); ?>
			<div class="stlms-course-text"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></div>
			<div class="stlms-course-by-tag">
				<?php if ( ! empty( $terms_name ) ) : ?>
					<span class="tag"><?php echo esc_html( $terms_name ); ?></span>
				<?php endif; ?>
				<span class="by">
				<?php
				echo wp_kses(
					sprintf(
						// Translators: %1$s to filter url, %2$s author name.
						__( 'by: <a href="%1$s">%2$s</a>', 'skilltriks' ),
						add_query_arg(
							array(
								'filter_author' => $author_id,
							),
							esc_url( \ST\Lms\get_page_url( 'courses' ) )
						),
						get_the_author_meta( 'display_name', $author_id )
					),
					array(
						'a' => array(
							'href' => true,
						),
					)
				);
				?>
				</span>
			</div>
		</div>
	</div>
	<?php
	$content            = get_the_content();
	$course_information = get_post_meta( $course_id, \ST\Lms\META_KEY_COURSE_INFORMATION, true );
	$requirements       = isset( $course_information['requirement'] ) ? $course_information['requirement'] : '';
	$what_you_learn     = isset( $course_information['what_you_learn'] ) ? $course_information['what_you_learn'] : '';
	$skills_gain        = isset( $course_information['skills_you_gain'] ) ? $course_information['skills_you_gain'] : '';
	$course_includes    = isset( $course_information['course_includes'] ) ? $course_information['course_includes'] : '';
	$faq_questions      = isset( $course_information['faq_question'] ) ? $course_information['faq_question'] : '';
	$faq_answers        = isset( $course_information['faq_answer'] ) ? $course_information['faq_answer'] : '';
	$first_curriculum   = reset( $curriculums_list );
	$has_curriculum     = isset( $first_curriculum['items'] ) && count( $first_curriculum['items'] );
	?>
	<div class="stlms-course-detail-nav">
		<div class="stlms-container">
			<ul>
				<?php if ( $content || $requirements || $what_you_learn || $skills_gain || $course_includes ) : ?>
					<li><a href="javascript:;" class="goto-section" data-id="about"><?php echo esc_html_e( 'About Course', 'skilltriks' ); ?></a></li>
				<?php endif; ?>
				<?php if ( $has_curriculum ) : ?>
					<li><a href="javascript:;" class="goto-section" data-id="course-content"><?php echo esc_html_e( 'Course Content', 'skilltriks' ); ?></a></li>
				<?php endif; ?>
				<?php if ( $faq_questions && $faq_answers ) : ?>
					<li><a href="javascript:;" class="goto-section" data-id="faq"><?php echo esc_html_e( 'FAQ', 'skilltriks' ); ?></a></li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
	<div class="stlms-course-detail-wrap">
		<div class="stlms-container">
			<div class="stlms-course-detail-column">
				<div class="stlms-course-right">
					<div class="stlms-course-info-box">
						<div class="stlms-course-info-box-inner">
							<div class="stlms-course-info-img">
								<?php if ( has_post_thumbnail() ) : ?>
									<?php the_post_thumbnail(); ?>
								<?php else : ?>
									<img fetchpriority="high" decoding="async" src="<?php echo esc_url( STLMS_ASSETS ); ?>/images/course-item-placeholder.png" alt="<?php the_title(); ?>">
								<?php endif; ?>
								<?php if ( ! empty( $terms_name ) ) : ?>
									<span class="tag"><?php echo esc_html( $terms_name ); ?></span>
								<?php endif; ?>
							</div>
							<?php
							$assessment      = get_post_meta( $course_id, \ST\Lms\META_KEY_COURSE_ASSESSMENT, true );
							$passing_grade   = isset( $assessment['passing_grade'] ) ? $assessment['passing_grade'] . '%' : '0%';
							$curriculums     = \ST\Lms\merge_curriculum_items( $curriculums_list );
							$curriculums     = array_keys( $curriculums );
							$course_progress = \ST\Lms\calculate_course_progress( $course_id, $curriculums ) . '%';
							$lessons         = \ST\Lms\get_curriculums( $curriculums_list, \ST\Lms\STLMS_LESSON_CPT );
							$total_lessons   = count( $lessons );
							$quizzes         = \ST\Lms\get_curriculums( $curriculums_list, \ST\Lms\STLMS_QUIZ_CPT );
							$last_quiz       = end( $quizzes );
							$total_quizzes   = count( $quizzes );
							$total_duration  = \ST\Lms\count_duration( array_merge( $lessons, $quizzes ) );
							$duration_str    = \ST\Lms\seconds_to_decimal_hours( $total_duration );
							$enrol_courses   = get_user_meta( get_current_user_id(), \ST\Lms\STLMS_ENROL_COURSES, true );
							$is_enrol        = ! empty( $enrol_courses ) && in_array( get_the_ID(), $enrol_courses, true );
							if ( isset( $assessment['evaluation'] ) && 2 === $assessment['evaluation'] ) {
								$passing_grade = isset( $last_quiz['settings']['passing_marks'] ) ? $last_quiz['settings']['passing_marks'] : '0';
							}
							?>
							<div class="stlms-course-info">
								<h3><?php echo esc_html_e( 'Course Includes', 'skilltriks' ); ?></h3>
								<ul class="stlms-course-include">
									<li>
										<svg width="16" height="16">
											<use xlink:href="<?php echo esc_url( STLMS_ASSETS ) . '/images/sprite-front.svg#clock'; ?>">
											</use>
										</svg>
										<?php
										if ( ! empty( $duration_str ) ) {
											echo wp_kses(
												sprintf(
													// Translators: %s total course duration.
													__( 'Hours <span>%s</span>', 'skilltriks' ),
													$duration_str
												),
												array(
													'span' => array(),
												)
											);
										} else {
											echo wp_kses(
												__( 'Hours <span>Lifetime</span>', 'skilltriks' ),
												array(
													'span' => array(),
												)
											);
										}
										?>
									</li>
									<li>
										<svg width="16" height="16">
											<use xlink:href="<?php echo esc_url( STLMS_ASSETS ) . '/images/sprite-front.svg#lessons'; ?>">
											</use>
										</svg>
										<?php
										echo wp_kses(
											sprintf(
												// Translators: %d total number of lesson.
												__( 'Lesson <span>%d</span>', 'skilltriks' ),
												$total_lessons
											),
											array(
												'span' => array(),
											)
										);
										?>
									</li>
									<li>
										<svg width="16" height="16">
											<use xlink:href="<?php echo esc_url( STLMS_ASSETS ) . '/images/sprite-front.svg#quiz'; ?>">
											</use>
										</svg>
										<?php
										echo wp_kses(
											sprintf(
												// Translators: %d total number of quiz.
												__( 'Quiz<span>%d</span>', 'skilltriks' ),
												$total_quizzes
											),
											array(
												'span' => array(),
											)
										);
										?>
									</li>
									<li>
										<svg width="16" height="16">
											<use xlink:href="<?php echo esc_url( STLMS_ASSETS ) . '/images/sprite-front.svg#badget-check'; ?>">
											</use>
										</svg>
										<?php
										$passing_text = isset( $assessment['evaluation'] ) && 2 === $assessment['evaluation'] ? 'Marks' : 'Grade';
										echo wp_kses(
											sprintf(
												// Translators: %s passing grade.
												__( 'Passing %1$s<span class="stlms-tag secondary">%2$s</span>', 'skilltriks' ),
												esc_html( $passing_text ),
												esc_html( $passing_grade )
											),
											array(
												'span' => array(
													'class' => true,
												),
											)
										);
										?>
									</li>
									<?php if ( $is_enrol ) : ?>
										<div class="stlms-progress">
											<div class="stlms-progress__label">
												<?php
													// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
													echo esc_html( sprintf( __( '%s Complete', 'skilltriks' ), $course_progress ) );
												?>
											</div>
											<div class="stlms-progress__bar">
												<div class="stlms-progress__bar-inner" style="width: <?php echo esc_attr( $course_progress ); ?>"></div>
											</div>
										</div>
									<?php endif; ?>
								</ul>
								<?php
								$has_certificate  = 0;
								$course_completed = false;
								$first_curriculum = reset( $curriculums_list );
								$items            = isset( $first_curriculum['items'] ) ? $first_curriculum['items'] : array();
								$first_item       = reset( $items );
								$section_id       = 1;
								$item_id          = isset( $first_item['item_id'] ) ? $first_item['item_id'] : 0;
								$course_link      = get_the_permalink();
								$button_text      = esc_html__( 'Enrol Now', 'skilltriks' );
								$extra_class      = '';
								$meta_key         = sprintf( \ST\Lms\STLMS_COURSE_STATUS, $course_id );
								$button_text      = $is_enrol ? esc_html__( 'Start Learning', 'skilltriks' ) : $button_text;
								$current_status   = get_user_meta( get_current_user_id(), $meta_key, true );
								if ( ! empty( $current_status ) ) {
									$current_status  = ! is_string( $current_status ) ? end( $current_status ) : $current_status;
									$current_status  = explode( '_', $current_status );
									$section_id      = (int) reset( $current_status );
									$item_id         = (int) end( $current_status );
									$button_text     = esc_html__( 'Continue Learning', 'skilltriks' );
									$extra_class     = ' stlms-btn-light';
									$last_curriculum = end( $curriculums_list );
									$items           = isset( $last_curriculum['items'] ) ? $last_curriculum['items'] : array();
									$last_item       = end( $items );
									$last_item_id    = isset( $last_item['item_id'] ) ? $last_item['item_id'] : 0;
									$last_section_id = count( $curriculums_list );
									if ( $last_section_id === $section_id && $last_item_id === $item_id ) {
										$restart_course = \ST\Lms\restart_course( $course_id );
										if ( $restart_course ) {
											$section_id         = 1;
											$item_id            = isset( $first_item['item_id'] ) ? $first_item['item_id'] : 0;
											$button_text        = esc_html__( 'Restart Course', 'skilltriks' );
											$extra_class        = ' stlms-btn-dark';
											$course_completed   = true;
											$course_certificate = get_post_meta( $course_id, \ST\Lms\META_KEY_COURSE_SIGNATURE, true );
											$has_certificate    = isset( $course_certificate['certificate'] ) ? $course_certificate['certificate'] : 0;
										}
									}
								}
								$curriculum_type = get_post_type( $item_id );
								$curriculum_type = str_replace( 'stlms_', '', $curriculum_type );
								$course_link     = sprintf( '%s/%d/%s/%d/', untrailingslashit( $course_link ), $section_id, $curriculum_type, $item_id );
								?>
								<div class="cta">
									<a href="<?php echo ! $is_enrol && is_user_logged_in() ? 'javascript:;' : esc_url( $course_link ); ?>" class="stlms-btn stlms-btn-block <?php echo esc_attr( $extra_class ); ?>" id="<?php echo ! $is_enrol && is_user_logged_in() ? 'enrol-now' : ''; ?>" data-course="<?php echo esc_attr( $course_id ); ?>"><?php echo esc_html( $button_text ); ?><i class="stlms-loader"></i></a>
									<?php if ( $has_certificate && '100%' === $course_progress ) : ?>
										<a href="javascript:;" id="download-certificate" data-course="<?php echo esc_attr( $course_id ); ?>" class="stlms-btn stlms-btn-block download-certificate"><?php esc_html_e( 'Download certificate', 'skilltriks' ); ?></a>
									<?php endif; ?>
									<?php if ( current_user_can( 'assign_course' ) || current_user_can( 'manage_options' ) ) : //phpcs:ignore WordPress.WP.Capabilities.Unknown ?>
									<a href="javascript:void(0);" data-fancybox data-src="#assign-course" class="stlms-btn stlms-btn-outline stlms-btn-block"><?php esc_html_e( 'Assign Course', 'skilltriks' ); ?></a>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="stlms-course-left">
					<?php if ( $content ) : ?>
						<div class="stlms-course-requirement-box" id="about">
							<h3><?php echo esc_html_e( 'About Course', 'skilltriks' ); ?></h3>
							<div class="stlms-quiz-content">
								<?php echo wp_kses_post( wpautop( $content ) ); ?>
							</div>
						</div>
					<?php endif; ?>
					<?php if ( $requirements ) : ?>
						<div class="stlms-course-requirement-box">
							<h3><?php echo esc_html_e( 'Course Requirement', 'skilltriks' ); ?></h3>
							<ul class="stlms-course-requirement-check">
								<?php foreach ( $requirements as $requirement ) : ?>
									<li><?php echo esc_html( $requirement ); ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
					<?php if ( $what_you_learn ) : ?>
						<div class="stlms-course-requirement-box learn-box">
							<h3><?php echo esc_html_e( 'What We Learn', 'skilltriks' ); ?></h3>
							<ul class="stlms-course-requirement-check">
								<?php foreach ( $what_you_learn as $learn ) : ?>
									<li><?php echo esc_html( $learn ); ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
					<?php if ( $skills_gain ) : ?>
						<div class="stlms-course-requirement-box skill-box">
							<h3><?php echo esc_html_e( 'Skills you Gain', 'skilltriks' ); ?></h3>
							<ul class="stlms-course-requirement-check">
								<?php foreach ( $skills_gain as $skill ) : ?>
									<li><?php echo esc_html( $skill ); ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
					<?php if ( $course_includes ) : ?>
						<div class="stlms-course-requirement-box include-box">
							<h3><?php echo esc_html_e( 'Course Includes', 'skilltriks' ); ?></h3>
							<ul class="stlms-course-requirement-check">
								<?php foreach ( $course_includes as $course_include ) : ?>
									<li><?php echo esc_html( $course_include ); ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $curriculums_list ) && $has_curriculum ) : ?>
						<div class="stlms-course-requirement-box" id="course-content">
							<h3><?php echo esc_html_e( 'Course Content', 'skilltriks' ); ?></h3>
							<div class="stlms-accordion-course-content">
								<div class="stlms-accordion">
									<?php
									$current_section_id = ! empty( $current_status ) ? (int) reset( $current_status ) : 0;
									$current_item_id    = ! empty( $current_status ) ? (int) end( $current_status ) : 0;
									$inactive           = false;

									foreach ( $curriculums_list as $item_key => $curriculums ) :
										$current_curriculum = false;
										$items              = ! empty( $curriculums['items'] ) ? $curriculums['items'] : array();
										$total_duration     = \ST\Lms\count_duration( $items );
										$duration_str       = \ST\Lms\seconds_to_hours_str( $total_duration );
										$section_desc       = ! empty( $curriculums['section_desc'] ) ? $curriculums['section_desc'] : '';
										if ( ++$item_key === $current_section_id ) {
											$current_curriculum = true;
										}
										if ( empty( $current_section_id ) && 1 === $item_key ) {
											$current_curriculum = true;
										}
										?>
										<div class="stlms-accordion-item" <?php echo $current_curriculum ? esc_attr( 'data-expanded=true' ) : ''; ?>>
											<div class="stlms-accordion-header">
												<div class="stlms-lesson-title">
													<div class="stlms-lesson-name">
														<div class="name"><?php echo (int) $item_key; ?>. <?php echo isset( $curriculums['section_name'] ) ? esc_html( $curriculums['section_name'] ) : ''; ?></div>
														<?php if ( ! empty( $duration_str ) ) : ?>
															<div class="info">
																<span><?php echo esc_html( $duration_str ); ?></span>
															</div>
														<?php endif; ?>
													</div>
												</div>
											</div>
											<div class="stlms-accordion-collapse">
												<?php if ( $section_desc ) : ?>
												<div class="stlms-accordion-note">
													<?php echo esc_html( $section_desc ); ?>
												</div>
												<?php endif; ?>
												<div class="stlms-lesson-list">
													<ul>
													<?php
													foreach ( $items as $key => $item ) :
														++$key;
														$media_type = 'quiz-2';
														$item_id    = isset( $item['item_id'] ) ? $item['item_id'] : 0;
														if ( \ST\Lms\STLMS_LESSON_CPT === get_post_type( $item_id ) ) {
															$media      = get_post_meta( $item_id, \ST\Lms\META_KEY_LESSON_MEDIA, true );
															$media_type = ! empty( $media['media_type'] ) ? $media['media_type'] : '';
															$media_type = 'text' === $media_type ? 'file-text' : $media_type;
															$settings   = get_post_meta( $item_id, \ST\Lms\META_KEY_LESSON_SETTINGS, true );
														} else {
															$settings = get_post_meta( $item_id, \ST\Lms\META_KEY_QUIZ_SETTINGS, true );
														}
														$duration      = isset( $settings['duration'] ) ? (int) $settings['duration'] : '';
														$duration_type = isset( $settings['duration_type'] ) ? $settings['duration_type'] : '';

														if ( empty( $current_item_id ) ) {
															if ( $key > 1 ) {
																$current_curriculum = false;
															}
															$inactive = true;
														}
														if ( $current_section_id === $item_key && $current_item_id === $item_id ) {
															$inactive           = true;
															$current_curriculum = true;
														} else {
															$current_curriculum = false;
														}
														?>
														<li>
															<label class=<?php echo $current_curriculum && ! $course_completed ? esc_attr( 'in-progress' ) : ''; ?>>
															<?php if ( $current_curriculum || ( $current_section_id === $item_key && ( $current_item_id === $item_id ) ) ) : ?>
																<input type="checkbox" class="stlms-check" checked <?php echo $course_completed ? ' hidden' : ''; ?>>
															<?php else : ?>
																<input type="checkbox" class="stlms-check"<?php echo $inactive ? ' readonly' : ' checked hidden'; ?>>
															<?php endif; ?>
																<span class="stlms-lesson-class">
																	<span class="class-type">
																		<svg class="icon" width="16" height="16">
																			<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#<?php echo esc_html( $media_type ); ?>">
																			</use>
																		</svg>
																	</span>
																	<span class="class-name"><span><?php echo esc_html( sprintf( '%s.%s.', $item_key, $key ) ); ?></span> <?php echo esc_html( get_the_title( $item_id ) ); ?></span>
																	<span class="class-time-info">
																		<span class="class-time">
																		<?php
																		if ( ! empty( $duration ) ) {
																			$duration_type .= $duration > 1 ? 's' : '';
																			echo esc_html( sprintf( '%d %s', $duration, ucfirst( $duration_type ) ) );
																		} else {
																			echo esc_html_e( 'No duration', 'skilltriks' );
																		}
																		?>
																		</span>
																		<?php if ( ( $current_section_id === $item_key && $current_item_id === $item_id ) && ! $course_completed ) : ?>
																			<a href="<?php echo esc_url( $course_link ); ?>" class="stlms-btn"><?php echo esc_html_e( 'Continue', 'skilltriks' ); ?></a>
																		<?php elseif ( empty( $current_section_id ) && 1 === $item_key && 1 === $key && $is_enrol ) : ?>
																			<a href="<?php echo esc_url( $course_link ); ?>" class="stlms-btn"><?php echo esc_html_e( 'Continue', 'skilltriks' ); ?></a>
																		<?php elseif ( $inactive && ! $course_completed ) : ?>
																			<svg class="lock-icon" width="16" height="16">
																				<use
																					xlink:href="<?php echo esc_url( STLMS_ASSETS ) . '/images/sprite-front.svg#lock'; ?>">
																				</use>
																			</svg>
																		<?php endif; ?>
																	</span>
																</span>
															</label>
														</li>
													<?php endforeach; ?>
													</ul>
												</div>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
					<?php endif; ?>
					<?php if ( $faq_questions && $faq_answers ) : ?>
						<div class="stlms-course-requirement-box" id="faq">
							<h3><?php echo esc_html_e( 'FAQ', 'skilltriks' ); ?></h3>
							<div class="stlms-accordion-faq">
								<div class="stlms-accordion">
									<?php
									foreach ( $faq_questions as $key => $faq_question ) :
										if ( ! empty( $faq_answers[ $key ] ) ) :
											?>
											<div class="stlms-accordion-item" <?php echo 0 === $key ? 'data-expanded="true"' : ''; ?>>
												<div class="stlms-accordion-header">
													<?php echo esc_html( $faq_question ); ?>
												</div>
												<div class="stlms-accordion-collapse">
													<div class="stlms-quiz-content">
														<p><?php echo esc_html( $faq_answers[ $key ] ); ?></p>
													</div>
												</div>
											</div>
											<?php
										endif;
									endforeach;
									?>
								</div>
							</div>
						</div>
					<?php endif; ?>

					<?php
					$parent_terms_id = array();
					foreach ( $terms_id as $term_id ) {
						$parent_id = wp_get_term_taxonomy_parent_id( $term_id, \ST\Lms\STLMS_COURSE_CATEGORY_TAX );
						if ( $parent_id ) {
							$parent_terms_id[] = $parent_id;
						}
					}
					$terms_id       = array_merge( $parent_terms_id, $terms_id );
					$tax_query_args = array(
						'taxonomy' => \ST\Lms\STLMS_COURSE_CATEGORY_TAX,
						'field'    => 'term_id',
						'terms'    => $terms_id,
					);

					$courses_arg = array(
						'post_type'    => \ST\Lms\STLMS_COURSE_CPT,
						'post_status'  => 'publish',
						// phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in
						'post__not_in' => array( $course_id ),
						// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
						'tax_query'    => array(
							$tax_query_args,
						),
					);
					$courses = new WP_Query( $courses_arg );
					if ( $courses->have_posts() ) :
						?>
						<div class="stlms-similar-course">
							<div class="stlms-similar-course-title">
								<h3><?php echo esc_html_e( 'Similar Courses', 'skilltriks' ); ?></h3>
								<?php if ( $courses->post_count > 3 ) : ?>
									<div class="stlms-slider-arrows">
										<div class="stlms-slider-arrow stlms-sc-slider-prev">
											<svg class="icon" width="24" height="24">
												<use xlink:href="<?php echo esc_url( STLMS_ASSETS ) . '/images/sprite-front.svg#arrow-left'; ?>">
												</use>
											</svg>
										</div>
										<div class="stlms-slider-arrow stlms-sc-slider-next">
											<svg class="icon" width="24" height="24">
												<use xlink:href="<?php echo esc_url( STLMS_ASSETS ) . '/images/sprite-front.svg#arrow-right'; ?>">
												</use>
											</svg>
										</div>
									</div>
								<?php endif; ?>
							</div>
							<div class="swiper stlms-similar-course-slider">
								<div class="swiper-wrapper">
									<?php
									while ( $courses->have_posts() ) :
										$courses->the_post();
										$get_terms        = get_the_terms( get_the_ID(), \ST\Lms\STLMS_COURSE_CATEGORY_TAX );
										$terms_name       = join( ', ', wp_list_pluck( $get_terms, 'name' ) );
										$curriculums      = get_post_meta( get_the_ID(), \ST\Lms\META_KEY_COURSE_CURRICULUM, true );
										$total_lessons    = 0;
										$total_quizzes    = 0;
										$course_view_link = get_the_permalink();
										$button_text      = esc_html__( 'Enrol Now', 'skilltriks' );
										$extra_class      = '';
										if ( ! empty( $curriculums ) ) {
											$lessons          = \ST\Lms\get_curriculums( $curriculums, \ST\Lms\STLMS_LESSON_CPT );
											$total_lessons    = count( $lessons );
											$quizzes          = \ST\Lms\get_curriculums( $curriculums, \ST\Lms\STLMS_QUIZ_CPT );
											$total_quizzes    = count( $quizzes );
											$total_duration   = \ST\Lms\count_duration( array_merge( $lessons, $quizzes ) );
											$curriculums      = \ST\Lms\merge_curriculum_items( $curriculums );
											$curriculums      = array_keys( $curriculums );
											$first_curriculum = reset( $curriculums );
											$first_curriculum = explode( '_', $first_curriculum );
											$first_curriculum = array_map( 'intval', $first_curriculum );
											$section_id       = reset( $first_curriculum );
											$item_id          = end( $first_curriculum );
											if ( is_user_logged_in() ) {
												$meta_key       = sprintf( \ST\Lms\STLMS_COURSE_STATUS, get_the_ID() );
												$user_id        = get_current_user_id();
												$enrol_courses  = get_user_meta( $user_id, \ST\Lms\STLMS_ENROL_COURSES, true );
												$is_enrol       = ! empty( $enrol_courses ) && in_array( get_the_ID(), $enrol_courses, true );
												$button_text    = $is_enrol ? esc_html__( 'Start Learning', 'skilltriks' ) : $button_text;
												$current_status = get_user_meta( $user_id, $meta_key, true );
												if ( ! empty( $current_status ) ) {
													$current_status  = ! is_string( $current_status ) ? end( $current_status ) : $current_status;
													$current_status  = explode( '_', $current_status );
													$section_id      = (int) reset( $current_status );
													$item_id         = (int) end( $current_status );
													$button_text     = esc_html__( 'Continue Learning', 'skilltriks' );
													$extra_class     = ' stlms-btn-light';
													$last_curriculum = end( $curriculums );
													$last_curriculum = explode( '_', $last_curriculum );
													$last_curriculum = array_map( 'intval', $last_curriculum );
													if ( reset( $last_curriculum ) === $section_id && end( $last_curriculum ) === $item_id ) {
														$restart_course = \ST\Lms\restart_course( get_the_ID() );
														if ( $restart_course ) {
															$first_curriculum = reset( $curriculums );
															$first_curriculum = explode( '_', $first_curriculum );
															$first_curriculum = array_map( 'intval', $first_curriculum );
															$section_id       = reset( $first_curriculum );
															$item_id          = end( $first_curriculum );
															$button_text      = esc_html__( 'Restart Course', 'skilltriks' );
															$extra_class      = ' stlms-btn-dark';
														}
													}
												}
											}
											$curriculum_type = get_post_type( $item_id );
											$curriculum_type = str_replace( 'stlms_', '', $curriculum_type );
											$course_link     = sprintf( '%s/%d/%s/%d/', untrailingslashit( $course_view_link ), $section_id, $curriculum_type, $item_id );
											$button_text     = apply_filters( 'stlms_course_view_button_text', $button_text );
											$course_link     = apply_filters( 'stlms_course_view_button_link', $course_link );
										}
										?>
										<div class="swiper-slide stlms-similar-course-slide">
											<div class="stlms-course-item">
												<div class="stlms-course-item__img">
													<?php if ( ! empty( $terms_name ) ) : ?>
														<div class="stlms-course-item__tag">
															<span><?php echo esc_html( $terms_name ); ?></span>
														</div>
													<?php endif; ?>
													<a href="<?php echo esc_url( $course_view_link ); ?>">
														<?php if ( has_post_thumbnail() ) : ?>
															<?php the_post_thumbnail(); ?>
														<?php else : ?>
															<img fetchpriority="high" decoding="async" src="<?php echo esc_url( STLMS_ASSETS ); ?>/images/course-item-placeholder.png" alt="<?php the_title(); ?>">
														<?php endif; ?>
													</a>
												</div>
												<div class="stlms-course-item__info">
													<div class="stlms-course-item__by">
														<?php
															echo wp_kses(
																sprintf(
																	// Translators: %1$s to filter url, %2$s author name.
																	__( 'by <a href="%1$s">%2$s</a>', 'skilltriks' ),
																	add_query_arg(
																		array(
																			'filter_author' => get_the_author_meta( 'ID' ),
																		),
																		esc_url( \ST\Lms\get_page_url( 'courses' ) )
																	),
																	get_the_author_meta( 'display_name' )
																),
																array(
																	'a' => array(
																		'href' => true,
																	),
																)
															);
														?>
													</div>
													<h3 class="stlms-course-item__title"><a href="<?php echo esc_url( $course_view_link ); ?>"><?php the_title(); ?></a></h3>
													<div class="stlms-course-item__meta">
													<ul>
														<li>
															<svg width="16" height="16">
																<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#clock">
																</use>
															</svg>
															<?php
															$duration_str = \ST\Lms\seconds_to_decimal_hours( $total_duration );
															if ( ! empty( $duration_str ) ) {
																// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
																echo esc_html( sprintf( __( '%s Hours', 'skilltriks' ), $duration_str ) );
															} else {
																echo esc_html_e( 'Lifetime', 'skilltriks' );
															}
															?>
														</li>
														<li>
															<svg width="16" height="16">
																<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#lessons">
																</use>
															</svg>
															<?php
															if ( $total_lessons > 1 ) {
																// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
																echo esc_html( sprintf( __( '%s Lessons', 'skilltriks' ), $total_lessons ) );
															} else {
																// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
																echo esc_html( sprintf( __( '%s Lesson', 'skilltriks' ), $total_lessons ) );
															}
															?>
														</li>
														<li>
															<svg width="16" height="16">
																<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#quiz">
																</use>
															</svg>
															<?php
															if ( $total_quizzes > 1 ) {
																// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
																echo esc_html( sprintf( __( '%s Quizzes', 'skilltriks' ), $total_quizzes ) );
															} else {
																// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
																echo esc_html( sprintf( __( '%s Quiz', 'skilltriks' ), $total_quizzes ) );
															}
															?>
														</li>
													</ul>
												</div>
												<div class="stlms-course-item__action">
													<a href="<?php echo ! $is_enrol && is_user_logged_in() ? 'javascript:;' : esc_url( $course_link ); ?>" class="stlms-btn stlms-btn-block<?php echo esc_attr( $extra_class ); ?>" id="<?php echo ! $is_enrol && is_user_logged_in() ? 'enrol-now' : ''; ?>" data-course="<?php echo esc_html( $course_id ); ?>"><?php echo esc_html( $button_text ); ?><i class="stlms-loader"></i></a>
												</div>
												</div>
											</div>
										</div>
									<?php endwhile; ?>
								</div>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- assign popup -->
<?php
$stlms_users    = get_users(
	array(
		'fields'       => array( 'ID', 'display_name' ),
		'role__not_in' => array( 'Administrator' ),
		'exclude'      => get_current_user_id(),
	)
);
$assigned_users = get_post_meta( $course_id, ST\LMS\META_KEY_COURSE_ASSIGNED, true ) ? get_post_meta( $course_id, ST\LMS\META_KEY_COURSE_ASSIGNED, true ) : array();
?>
<div id="assign-course" class="stlms-dialog" data-course="<?php echo esc_attr( $course_id ); ?>" style="display: none;">
	<form class="stlms-assign-course__box">
		<div class="stlms-dialog__header">
			<div class="stlms-dialog__title">
				<?php esc_html_e( 'Assign This Course', 'skilltriks' ); ?>
			</div>
			<button class="stlms-dialog__close" data-fancybox-close>
				<svg width="30" height="30">
					<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#cross"></use>
				</svg>
			</button>
		</div>
		<div class="stlms-dialog__content-box">
			<div class="stlms-dialog__content">
				<div class="stlms-dialog__content-title">
					<p>
						<span>
							<?php esc_html_e( 'Choose people whom you wish to assign this course', 'skilltriks' ); ?>
						</span>
					</p>
				</div>
			</div>
			<div class="stlms-dialog__content">
				<div class="stlms-form-group">
					<label class="stlms-select-search" for="employee-list">
						<?php esc_html_e( 'Choose Employee(s)', 'skilltriks' ); ?>
						<select multiple data-placeholder="John Doe" class="stlms-select2-multi js-states form-control" id="employee-list">
							<?php foreach ( $stlms_users as $users ) : ?>
								<option value="<?php echo esc_attr( base64_encode( $users->ID ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode ?>" <?php echo in_array( (int) $users->ID, $assigned_users, true ) ? 'disabled' : ''; ?>><?php echo esc_html( $users->display_name ); ?></option>
							<?php endforeach; ?>
						</select>
					</label>
				</div>
			</div>
			<div class="stlms-dialog__content">
				<div class="stlms-switch-wrap">
					<label>
						<input type="checkbox" class="stlms-check"><?php esc_html_e( 'Common completion date for all?', 'skilltriks' ); ?>
					</label>
				</div>
			</div>
			<div class="stlms-dialog__content" id="common-date">
				<div class="stlms-form-group">
					<label for="completion-date"><?php esc_html_e( 'Common completion date for all', 'skilltriks' ); ?></label>
					<input type="date" id="completion-date" min="<?php echo esc_attr( wp_date( 'Y-m-d' ) ); ?>" />
				</div>
			</div>
			<div class="stlms-dialog__content" id="unique-date">
				<div class="stlms-form-col">
					<div class="stlms-form-group">
						<label for="completion-date"><?php esc_html_e( 'Common completion date for John Doe', 'skilltriks' ); ?></label>
						<input type="date" id="completion-date" min="<?php echo esc_attr( wp_date( 'Y-m-d' ) ); ?>" />
					</div>
				</div>
			</div>
		</div>
		<div class="stlms-dialog__footer">
			<div class="stlms-dialog__cta center">
				<button class="stlms-btn" data-fancybox-close id="showSnackbar"><?php esc_html_e( 'Assign Course', 'skilltriks' ); ?></button>
			</div>
		</div>
	</form>
</div>

<div id="snackbar-success" class="stlms-snackbar">
	<svg width="30" height="30">
		<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#tick"></use>
	</svg>
	<?php esc_html_e( 'Course Assigned Successfully!', 'skilltriks' ); ?>
	<button class="hideSnackbar">
		<svg width="20" height="20">
			<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#cross"></use>
		</svg>
	</button>
</div>
<div id="snackbar-error" class="stlms-snackbar error-snackbar">
	<svg width="30" height="30">
		<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#cross-error"></use>
	</svg>
	<?php esc_html_e( 'Oops, something went wrong, please try again later.', 'skilltriks' ); ?>
	<button class="hideSnackbar">
		<svg width="20" height="20">
			<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#cross"></use>
		</svg>
	</button>
</div>
