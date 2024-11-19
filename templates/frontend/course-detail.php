<?php
/**
 * Template: Course Detail Page
 *
 * @package BlueDolphin\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$course_id        = ! empty( $args['course_id'] ) ? $args['course_id'] : 0;
$curriculums_list = ! empty( $args['course_data']['curriculums'] ) ? $args['course_data']['curriculums'] : array();

global $current_user;
$current_user_id    = $current_user->ID;
$current_user_name  = $current_user->display_name;
$current_user_email = $current_user->user_email;
?>

<div class="bdlms-wrap">
	<?php if ( is_user_logged_in() ) : ?>
		<div class="bdlms-container">
			<div class="bdlms-pt-48 bdlms-pb-48">
				<div class="bdlms-user">
					<div class="bdlms-user-photo">
						<?php echo get_avatar( $current_user_email ); ?>
					</div>
					<div class="bdlms-user-info">
						<span class="bdlms-user-name"><?php echo esc_html( $current_user_name ); ?></span>
						<span class="bdlms-user-email"><?php echo esc_html( $current_user_email ); ?></span>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<div class="bdlms-course-banner" style="background-image: url('<?php echo esc_url( BDLMS_ASSETS ) . '/images/course-detail-banner.jpg'; ?>')">
		<div class="bdlms-container">
			<ul class="bdlms-breadcrumb">
				<li><a href="<?php echo esc_url( \BlueDolphin\Lms\get_page_url( 'courses' ) ); ?>"><?php echo esc_html_e( 'Course List Page', 'bluedolphin-lms' ); ?></a></li>
				<li class="active"><?php echo esc_html_e( 'Course Detail Page', 'bluedolphin-lms' ); ?></li>
			</ul>
			<?php
			$get_terms  = get_the_terms( get_the_ID(), \BlueDolphin\Lms\BDLMS_COURSE_CATEGORY_TAX );
			$terms_name = join( ', ', wp_list_pluck( $get_terms, 'name' ) );
			$terms_id   = wp_list_pluck( $get_terms, 'term_id' );
			$author_id  = (int) get_post_field( 'post_author', $course_id );
			?>
			<?php the_title( '<h1 class="bdlms-course-title">', '</h1>' ); ?>
			<div class="bdlms-course-text"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></div>
			<div class="bdlms-course-by-tag">
				<?php if ( ! empty( $terms_name ) ) : ?>
					<span class="tag"><?php echo esc_html( $terms_name ); ?></span>
				<?php endif; ?>
				<span class="by">
				<?php
				echo wp_kses(
					sprintf(
						// Translators: %1$s to filter url, %2$s author name.
						__( 'by: <a href="%1$s">%2$s</a>', 'bluedolphin-lms' ),
						add_query_arg(
							array(
								'filter_author' => $author_id,
							),
							esc_url( \BlueDolphin\Lms\get_page_url( 'courses' ) )
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
	$course_information = get_post_meta( $course_id, \BlueDolphin\Lms\META_KEY_COURSE_INFORMATION, true );
	$requirements       = isset( $course_information['requirement'] ) ? $course_information['requirement'] : '';
	$what_you_learn     = isset( $course_information['what_you_learn'] ) ? $course_information['what_you_learn'] : '';
	$skills_gain        = isset( $course_information['skills_you_gain'] ) ? $course_information['skills_you_gain'] : '';
	$course_includes    = isset( $course_information['course_includes'] ) ? $course_information['course_includes'] : '';
	$faq_questions      = isset( $course_information['faq_question'] ) ? $course_information['faq_question'] : '';
	$faq_answers        = isset( $course_information['faq_answer'] ) ? $course_information['faq_answer'] : '';
	$first_curriculum   = reset( $curriculums_list );
	$has_curriculum     = isset( $first_curriculum['items'] ) && count( $first_curriculum['items'] );
	?>
	<div class="bdlms-course-detail-nav">
		<div class="bdlms-container">
			<ul>
				<?php if ( $content || $requirements || $what_you_learn || $skills_gain || $course_includes ) : ?>
					<li><a href="javascript:;" class="goto-section" data-id="about"><?php echo esc_html_e( 'About Course', 'bluedolphin-lms' ); ?></a></li>
				<?php endif; ?>
				<?php if ( $has_curriculum ) : ?>
					<li><a href="javascript:;" class="goto-section" data-id="course-content"><?php echo esc_html_e( 'Course Content', 'bluedolphin-lms' ); ?></a></li>
				<?php endif; ?>
				<?php if ( $faq_questions && $faq_answers ) : ?>
					<li><a href="javascript:;" class="goto-section" data-id="faq"><?php echo esc_html_e( 'FAQ', 'bluedolphin-lms' ); ?></a></li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
	<div class="bdlms-course-detail-wrap">
		<div class="bdlms-container">
			<div class="bdlms-course-detail-column">
				<div class="bdlms-course-right">
					<div class="bdlms-course-info-box">
						<div class="bdlms-course-info-box-inner">
							<div class="bdlms-course-info-img">
								<?php if ( has_post_thumbnail() ) : ?>
									<?php the_post_thumbnail(); ?>
								<?php else : ?>
									<img fetchpriority="high" decoding="async" src="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/course-item-placeholder.png" alt="<?php the_title(); ?>">
								<?php endif; ?>
								<?php if ( ! empty( $terms_name ) ) : ?>
									<span class="tag"><?php echo esc_html( $terms_name ); ?></span>
								<?php endif; ?>
							</div>
							<?php
							$assessment      = get_post_meta( $course_id, \BlueDolphin\Lms\META_KEY_COURSE_ASSESSMENT, true );
							$passing_grade   = isset( $assessment['passing_grade'] ) ? $assessment['passing_grade'] . '%' : '0%';
							$curriculums     = \BlueDolphin\Lms\merge_curriculum_items( $curriculums_list );
							$curriculums     = array_keys( $curriculums );
							$course_progress = \BlueDolphin\Lms\calculate_course_progress( $course_id, $curriculums ) . '%';
							$lessons         = \BlueDolphin\Lms\get_curriculums( $curriculums_list, \BlueDolphin\Lms\BDLMS_LESSON_CPT );
							$total_lessons   = count( $lessons );
							$quizzes         = \BlueDolphin\Lms\get_curriculums( $curriculums_list, \BlueDolphin\Lms\BDLMS_QUIZ_CPT );
							$total_quizzes   = count( $quizzes );
							$total_duration  = \BlueDolphin\Lms\count_duration( array_merge( $lessons, $quizzes ) );
							$duration_str    = \BlueDolphin\Lms\seconds_to_decimal_hours( $total_duration );
							$enrol_courses   = get_user_meta( $current_user_id, \BlueDolphin\Lms\BDLMS_ENROL_COURSES, true );
							$is_enrol        = ! empty( $enrol_courses ) && in_array( get_the_ID(), $enrol_courses, true );
							?>
							<div class="bdlms-course-info">
								<h3><?php echo esc_html_e( 'Course Includes', 'bluedolphin-lms' ); ?></h3>
								<ul class="bdlms-course-include">
									<li>
										<svg width="16" height="16">
											<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ) . '/images/sprite-front.svg#clock'; ?>">
											</use>
										</svg>
										<?php
										if ( ! empty( $duration_str ) ) {
											echo wp_kses(
												sprintf(
													// Translators: %s total course duration.
													__( 'Hours <span>%s</span>', 'bluedolphin-lms' ),
													$duration_str
												),
												array(
													'span' => array(),
												)
											);
										} else {
											echo wp_kses(
												__( 'Hours <span>Lifetime</span>', 'bluedolphin-lms' ),
												array(
													'span' => array(),
												)
											);
										}
										?>
									</li>
									<li>
										<svg width="16" height="16">
											<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ) . '/images/sprite-front.svg#lessons'; ?>">
											</use>
										</svg>
										<?php
										echo wp_kses(
											sprintf(
												// Translators: %d total number of lesson.
												__( 'Lesson <span>%d</span>', 'bluedolphin-lms' ),
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
											<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ) . '/images/sprite-front.svg#quiz'; ?>">
											</use>
										</svg>
										<?php
										echo wp_kses(
											sprintf(
												// Translators: %d total number of quiz.
												__( 'Quiz<span>%d</span>', 'bluedolphin-lms' ),
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
											<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ) . '/images/sprite-front.svg#badget-check'; ?>">
											</use>
										</svg>
										<?php
										echo wp_kses(
											sprintf(
												// Translators: %s passing grade.
												__( 'Passing Grade<span>%s</span>', 'bluedolphin-lms' ),
												esc_html( $passing_grade )
											),
											array(
												'span' => array(),
											)
										);
										?>
									</li>
									<?php if ( $is_enrol ) : ?>
										<div class="bdlms-progress">
											<div class="bdlms-progress__label">
												<?php
													// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
													printf( esc_html__( '%s Complete', 'bluedolphin-lms' ), esc_html( $course_progress ) )
												?>
											</div>
											<div class="bdlms-progress__bar">
												<div class="bdlms-progress__bar-inner" style="width: <?php echo esc_attr( $course_progress ); ?>"></div>
											</div>
										</div>
									<?php endif; ?>
								</ul>
								<?php
								$course_completed = false;
								$first_curriculum = reset( $curriculums_list );
								$items            = isset( $first_curriculum['items'] ) ? $first_curriculum['items'] : array();
								$first_item       = reset( $items );
								$section_id       = 1;
								$item_id          = isset( $first_item['item_id'] ) ? $first_item['item_id'] : 0;
								$course_link      = get_the_permalink();
								$button_text      = esc_html__( 'Enrol Now', 'bluedolphin-lms' );
								$extra_class      = '';
								$meta_key         = sprintf( \BlueDolphin\Lms\BDLMS_COURSE_STATUS, $course_id );
								$button_text      = $is_enrol ? esc_html__( 'Start Learning', 'bluedolphin-lms' ) : $button_text;
								$current_status   = get_user_meta( $current_user_id, $meta_key, true );
								$current_status   = ! empty( $current_status ) ? explode( '_', $current_status ) : array();
								if ( ! empty( $current_status ) ) {
									$section_id      = (int) reset( $current_status );
									$item_id         = (int) end( $current_status );
									$button_text     = esc_html__( 'Continue Learning', 'bluedolphin-lms' );
									$extra_class     = ' bdlms-btn-light';
									$last_curriculum = end( $curriculums_list );
									$items           = isset( $last_curriculum['items'] ) ? $last_curriculum['items'] : array();
									$last_item       = end( $items );
									$last_item_id    = isset( $last_item['item_id'] ) ? $last_item['item_id'] : 0;
									$last_section_id = count( $curriculums_list );
									if ( $last_section_id === $section_id && $last_item_id === $item_id ) {
										$restart_course = \BlueDolphin\Lms\restart_course( $course_id );
										if ( $restart_course ) {
											$section_id       = 1;
											$item_id          = isset( $first_item['item_id'] ) ? $first_item['item_id'] : 0;
											$button_text      = esc_html__( 'Restart Course', 'bluedolphin-lms' );
											$extra_class      = ' bdlms-btn-dark';
											$course_completed = true;
										}
									}
								}
								$curriculum_type = get_post_type( $item_id );
								$curriculum_type = str_replace( 'bdlms_', '', $curriculum_type );
								$course_link     = sprintf( '%s/%d/%s/%d/', untrailingslashit( $course_link ), $section_id, $curriculum_type, $item_id );
								?>
								<div class="cta">
									<a href="<?php echo ! $is_enrol && is_user_logged_in() ? 'javascript:;' : esc_url( $course_link ); ?>" class="bdlms-btn bdlms-btn-block <?php echo esc_attr( $extra_class ); ?>" id="<?php echo ! $is_enrol && is_user_logged_in() ? 'enrol-now' : ''; ?>" data-course="<?php echo esc_attr( $course_id ); ?>"><?php echo esc_html( $button_text ); ?><i class="bdlms-loader"></i></a>
									<?php if ( '100%' === $course_progress ) : ?>
										<a href="javascript:;" id="download-certificate" data-course="<?php echo esc_attr( $course_id ); ?>" class="bdlms-btn bdlms-btn-block download-certificate"><?php esc_html_e( 'Download certificate', 'bluedolphin-lms' ); ?></a>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="bdlms-course-left">
					<?php if ( $content ) : ?>
						<div class="bdlms-course-requirement-box" id="about">
							<h3><?php echo esc_html_e( 'About Course', 'bluedolphin-lms' ); ?></h3>
							<div class="bdlms-quiz-content">
								<?php echo wp_kses_post( wpautop( $content ) ); ?>
							</div>
						</div>
					<?php endif; ?>
					<?php if ( $requirements ) : ?>
						<div class="bdlms-course-requirement-box">
							<h3><?php echo esc_html_e( 'Course Requirement', 'bluedolphin-lms' ); ?></h3>
							<ul class="bdlms-course-requirement-check">
								<?php foreach ( $requirements as $requirement ) : ?>
									<li><?php echo esc_html( $requirement ); ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
					<?php if ( $what_you_learn ) : ?>
						<div class="bdlms-course-requirement-box learn-box">
							<h3><?php echo esc_html_e( 'What We Learn', 'bluedolphin-lms' ); ?></h3>
							<ul class="bdlms-course-requirement-check">
								<?php foreach ( $what_you_learn as $learn ) : ?>
									<li><?php echo esc_html( $learn ); ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
					<?php if ( $skills_gain ) : ?>
						<div class="bdlms-course-requirement-box skill-box">
							<h3><?php echo esc_html_e( 'Skills you Gain', 'bluedolphin-lms' ); ?></h3>
							<ul class="bdlms-course-requirement-check">
								<?php foreach ( $skills_gain as $skill ) : ?>
									<li><?php echo esc_html( $skill ); ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
					<?php if ( $course_includes ) : ?>
						<div class="bdlms-course-requirement-box include-box">
							<h3><?php echo esc_html_e( 'Course Includes', 'bluedolphin-lms' ); ?></h3>
							<ul class="bdlms-course-requirement-check">
								<?php foreach ( $course_includes as $course_include ) : ?>
									<li><?php echo esc_html( $course_include ); ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $curriculums_list ) && $has_curriculum ) : ?>
						<div class="bdlms-course-requirement-box" id="course-content">
							<h3><?php echo esc_html_e( 'Course Content', 'bluedolphin-lms' ); ?></h3>
							<div class="bdlms-accordion-course-content">
								<div class="bdlms-accordion">
									<?php
									$current_section_id = ! empty( $current_status ) ? (int) reset( $current_status ) : 0;
									$current_item_id    = ! empty( $current_status ) ? (int) end( $current_status ) : 0;
									$inactive           = false;

									foreach ( $curriculums_list as $item_key => $curriculums ) :
										$current_curriculum = false;
										$items              = ! empty( $curriculums['items'] ) ? $curriculums['items'] : array();
										$total_duration     = \BlueDolphin\Lms\count_duration( $items );
										$duration_str       = \BlueDolphin\Lms\seconds_to_hours_str( $total_duration );
										$section_desc       = ! empty( $curriculums['section_desc'] ) ? $curriculums['section_desc'] : '';
										if ( ++$item_key === $current_section_id ) {
											$current_curriculum = true;
										}
										if ( empty( $current_section_id ) && 1 === $item_key ) {
											$current_curriculum = true;
										}
										?>
										<div class="bdlms-accordion-item" <?php echo $current_curriculum ? esc_attr( 'data-expanded=true' ) : ''; ?>>
											<div class="bdlms-accordion-header">
												<div class="bdlms-lesson-title">
													<div class="bdlms-lesson-name">
														<div class="name"><?php echo (int) $item_key; ?>. <?php echo isset( $curriculums['section_name'] ) ? esc_html( $curriculums['section_name'] ) : ''; ?></div>
														<?php if ( ! empty( $duration_str ) ) : ?>
															<div class="info">
																<span><?php echo esc_html( $duration_str ); ?></span>
															</div>
														<?php endif; ?>
													</div>
												</div>
											</div>
											<div class="bdlms-accordion-collapse">
												<?php if ( $section_desc ) : ?>
												<div class="bdlms-accordion-note">
													<?php echo esc_html( $section_desc ); ?>
												</div>
												<?php endif; ?>
												<div class="bdlms-lesson-list">
													<ul>
													<?php
													foreach ( $items as $key => $item ) :
														++$key;
														$media_type = 'quiz-2';
														$item_id    = isset( $item['item_id'] ) ? $item['item_id'] : 0;
														if ( \BlueDolphin\Lms\BDLMS_LESSON_CPT === get_post_type( $item_id ) ) {
															$media      = get_post_meta( $item_id, \BlueDolphin\Lms\META_KEY_LESSON_MEDIA, true );
															$media_type = ! empty( $media['media_type'] ) ? $media['media_type'] : '';
															$media_type = 'text' === $media_type ? 'file-text' : $media_type;
															$settings   = get_post_meta( $item_id, \BlueDolphin\Lms\META_KEY_LESSON_SETTINGS, true );
														} else {
															$settings = get_post_meta( $item_id, \BlueDolphin\Lms\META_KEY_QUIZ_SETTINGS, true );
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
																<input type="checkbox" class="bdlms-check" checked <?php echo $course_completed ? ' hidden' : ''; ?>>
															<?php else : ?>
																<input type="checkbox" class="bdlms-check"<?php echo $inactive ? ' readonly' : ' checked hidden'; ?>>
															<?php endif; ?>
																<span class="bdlms-lesson-class">
																	<span class="class-type">
																		<svg class="icon" width="16" height="16">
																			<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#<?php echo esc_html( $media_type ); ?>">
																			</use>
																		</svg>
																	</span>
																	<span class="class-name"><span><?php printf( '%d.%d.', (int) $item_key, (int) $key ); ?></span> <?php echo esc_html( get_the_title( $item_id ) ); ?></span>
																	<span class="class-time-info">
																		<span class="class-time">
																		<?php
																		if ( ! empty( $duration ) ) {
																			$duration_type .= $duration > 1 ? 's' : '';
																			printf( '%d %s', (int) $duration, esc_html( ucfirst( $duration_type ) ) );
																		} else {
																			echo esc_html_e( 'No duration', 'bluedolphin-lms' );
																		}
																		?>
																		</span>
																		<?php if ( ( $current_section_id === $item_key && $current_item_id === $item_id ) && ! $course_completed ) : ?>
																			<a href="<?php echo esc_url( $course_link ); ?>" class="bdlms-btn"><?php echo esc_html_e( 'Continue', 'bluedolphin-lms' ); ?></a>
																		<?php elseif ( empty( $current_section_id ) && 1 === $item_key && 1 === $key ) : ?>
																			<a href="<?php echo esc_url( $course_link ); ?>" class="bdlms-btn"><?php echo esc_html_e( 'Continue', 'bluedolphin-lms' ); ?></a>
																		<?php elseif ( $inactive && ! $course_completed ) : ?>
																			<svg class="lock-icon" width="16" height="16">
																				<use
																					xlink:href="<?php echo esc_url( BDLMS_ASSETS ) . '/images/sprite-front.svg#lock'; ?>">
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
						<div class="bdlms-course-requirement-box" id="faq">
							<h3><?php echo esc_html_e( 'FAQ', 'bluedolphin-lms' ); ?></h3>
							<div class="bdlms-accordion-faq">
								<div class="bdlms-accordion">
									<?php
									foreach ( $faq_questions as $key => $faq_question ) :
										if ( ! empty( $faq_answers[ $key ] ) ) :
											?>
											<div class="bdlms-accordion-item" <?php echo 0 === $key ? 'data-expanded="true"' : ''; ?>>
												<div class="bdlms-accordion-header">
													<?php echo esc_html( $faq_question ); ?>
												</div>
												<div class="bdlms-accordion-collapse">
													<div class="bdlms-quiz-content">
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
						$parent_id = wp_get_term_taxonomy_parent_id( $term_id, \BlueDolphin\Lms\BDLMS_COURSE_CATEGORY_TAX );
						if ( $parent_id ) {
							$parent_terms_id[] = $parent_id;
						}
					}
					$terms_id       = array_merge( $parent_terms_id, $terms_id );
					$tax_query_args = array(
						'taxonomy' => \BlueDolphin\Lms\BDLMS_COURSE_CATEGORY_TAX,
						'field'    => 'term_id',
						'terms'    => $terms_id,
					);

					$courses_arg = array(
						'post_type'    => \BlueDolphin\Lms\BDLMS_COURSE_CPT,
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
						<div class="bdlms-similar-course">
							<div class="bdlms-similar-course-title">
								<h3><?php echo esc_html_e( 'Similar Courses', 'bluedolphin-lms' ); ?></h3>
								<?php if ( $courses->post_count > 3 ) : ?>
									<div class="bdlms-slider-arrows">
										<div class="bdlms-slider-arrow bdlms-sc-slider-prev">
											<svg class="icon" width="24" height="24">
												<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ) . '/images/sprite-front.svg#arrow-left'; ?>">
												</use>
											</svg>
										</div>
										<div class="bdlms-slider-arrow bdlms-sc-slider-next">
											<svg class="icon" width="24" height="24">
												<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ) . '/images/sprite-front.svg#arrow-right'; ?>">
												</use>
											</svg>
										</div>
									</div>
								<?php endif; ?>
							</div>
							<div class="swiper bdlms-similar-course-slider">
								<div class="swiper-wrapper">
									<?php
									while ( $courses->have_posts() ) :
										$courses->the_post();
										$get_terms        = get_the_terms( get_the_ID(), \BlueDolphin\Lms\BDLMS_COURSE_CATEGORY_TAX );
										$terms_name       = join( ', ', wp_list_pluck( $get_terms, 'name' ) );
										$curriculums      = get_post_meta( get_the_ID(), \BlueDolphin\Lms\META_KEY_COURSE_CURRICULUM, true );
										$total_lessons    = 0;
										$total_quizzes    = 0;
										$course_view_link = get_the_permalink();
										$button_text      = esc_html__( 'Enrol Now', 'bluedolphin-lms' );
										$extra_class      = '';
										if ( ! empty( $curriculums ) ) {
											$lessons          = \BlueDolphin\Lms\get_curriculums( $curriculums, \BlueDolphin\Lms\BDLMS_LESSON_CPT );
											$total_lessons    = count( $lessons );
											$quizzes          = \BlueDolphin\Lms\get_curriculums( $curriculums, \BlueDolphin\Lms\BDLMS_QUIZ_CPT );
											$total_quizzes    = count( $quizzes );
											$total_duration   = \BlueDolphin\Lms\count_duration( array_merge( $lessons, $quizzes ) );
											$curriculums      = \BlueDolphin\Lms\merge_curriculum_items( $curriculums );
											$curriculums      = array_keys( $curriculums );
											$first_curriculum = reset( $curriculums );
											$first_curriculum = explode( '_', $first_curriculum );
											$first_curriculum = array_map( 'intval', $first_curriculum );
											$section_id       = reset( $first_curriculum );
											$item_id          = end( $first_curriculum );
											if ( is_user_logged_in() ) {
												$meta_key       = sprintf( \BlueDolphin\Lms\BDLMS_COURSE_STATUS, get_the_ID() );
												$user_id        = get_current_user_id();
												$enrol_courses  = get_user_meta( $user_id, \BlueDolphin\Lms\BDLMS_ENROL_COURSES, true );
												$is_enrol       = ! empty( $enrol_courses ) && in_array( get_the_ID(), $enrol_courses, true );
												$button_text    = $is_enrol ? esc_html__( 'Start Learning', 'bluedolphin-lms' ) : $button_text;
												$current_status = get_user_meta( $user_id, $meta_key, true );
												$current_status = ! empty( $current_status ) ? explode( '_', $current_status ) : array();
												if ( ! empty( $current_status ) ) {
													$section_id      = (int) reset( $current_status );
													$item_id         = (int) end( $current_status );
													$button_text     = esc_html__( 'Continue Learning', 'bluedolphin-lms' );
													$extra_class     = ' bdlms-btn-light';
													$last_curriculum = end( $curriculums );
													$last_curriculum = explode( '_', $last_curriculum );
													$last_curriculum = array_map( 'intval', $last_curriculum );
													if ( reset( $last_curriculum ) === $section_id && end( $last_curriculum ) === $item_id ) {
														$restart_course = \BlueDolphin\Lms\restart_course( get_the_ID() );
														if ( $restart_course ) {
															$first_curriculum = reset( $curriculums );
															$first_curriculum = explode( '_', $first_curriculum );
															$first_curriculum = array_map( 'intval', $first_curriculum );
															$section_id       = reset( $first_curriculum );
															$item_id          = end( $first_curriculum );
															$button_text      = esc_html__( 'Restart Course', 'bluedolphin-lms' );
															$extra_class      = ' bdlms-btn-dark';
														}
													}
												}
											}
											$curriculum_type = get_post_type( $item_id );
											$curriculum_type = str_replace( 'bdlms_', '', $curriculum_type );
											$course_link     = sprintf( '%s/%d/%s/%d/', untrailingslashit( $course_view_link ), $section_id, $curriculum_type, $item_id );
											$button_text     = apply_filters( 'bdlms_course_view_button_text', $button_text );
											$course_link     = apply_filters( 'bdlms_course_view_button_link', $course_link );
										}
										?>
										<div class="swiper-slide bdlms-similar-course-slide">
											<div class="bdlms-course-item">
												<div class="bdlms-course-item__img">
													<?php if ( ! empty( $terms_name ) ) : ?>
														<div class="bdlms-course-item__tag">
															<span><?php echo esc_html( $terms_name ); ?></span>
														</div>
													<?php endif; ?>
													<a href="<?php echo esc_url( $course_view_link ); ?>">
														<?php if ( has_post_thumbnail() ) : ?>
															<?php the_post_thumbnail(); ?>
														<?php else : ?>
															<img fetchpriority="high" decoding="async" src="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/course-item-placeholder.png" alt="<?php the_title(); ?>">
														<?php endif; ?>
													</a>
												</div>
												<div class="bdlms-course-item__info">
													<div class="bdlms-course-item__by">
														<?php
															echo wp_kses(
																sprintf(
																	// Translators: %1$s to filter url, %2$s author name.
																	__( 'by <a href="%1$s">%2$s</a>', 'bluedolphin-lms' ),
																	add_query_arg(
																		array(
																			'filter_author' => get_the_author_meta( 'ID' ),
																		),
																		esc_url( \BlueDolphin\Lms\get_page_url( 'courses' ) )
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
													<h3 class="bdlms-course-item__title"><a href="<?php echo esc_url( $course_view_link ); ?>"><?php the_title(); ?></a></h3>
													<div class="bdlms-course-item__meta">
													<ul>
														<li>
															<svg width="16" height="16">
																<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#clock">
																</use>
															</svg>
															<?php
															$duration_str = \BlueDolphin\Lms\seconds_to_decimal_hours( $total_duration );
															if ( ! empty( $duration_str ) ) {
																// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
																printf( esc_html__( '%s Hours', 'bluedolphin-lms' ), (float) $duration_str );
															} else {
																echo esc_html_e( 'Lifetime', 'bluedolphin-lms' );
															}
															?>
														</li>
														<li>
															<svg width="16" height="16">
																<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#lessons">
																</use>
															</svg>
															<?php
															if ( $total_lessons > 1 ) {
																// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
																printf( esc_html__( '%d Lessons', 'bluedolphin-lms' ), (int) $total_lessons );
															} else {
																// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
																printf( esc_html__( '%d Lesson', 'bluedolphin-lms' ), (int) $total_lessons );
															}
															?>
														</li>
														<li>
															<svg width="16" height="16">
																<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#quiz">
																</use>
															</svg>
															<?php
															if ( $total_quizzes > 1 ) {
																// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
																printf( esc_html__( '%d Quizzes', 'bluedolphin-lms' ), (int) $total_quizzes );
															} else {
																// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
																printf( esc_html__( '%d Quiz', 'bluedolphin-lms' ), (int) $total_quizzes );
															}
															?>
														</li>
													</ul>
												</div>
												<div class="bdlms-course-item__action">
													<a href="<?php echo ! $is_enrol && is_user_logged_in() ? 'javascript:;' : esc_url( $course_link ); ?>" class="bdlms-btn bdlms-btn-block<?php echo esc_attr( $extra_class ); ?>" id="<?php echo ! $is_enrol && is_user_logged_in() ? 'enrol-now' : ''; ?>" data-course="<?php echo esc_html( $course_id ); ?>"><?php echo esc_html( $button_text ); ?><i class="bdlms-loader"></i></a>
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
