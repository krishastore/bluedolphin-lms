<?php
/**
 * Template: My learning
 *
 * @package BlueDolphin\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$search_keyword = ! empty( $_GET['_s'] ) ? sanitize_text_field( wp_unslash( $_GET['_s'] ) ) : '';
$category       = ! empty( $_GET['category'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_GET['category'] ) ) ) : array();
$category       = array_map( 'intval', $category );
$_orderby       = ! empty( $_GET['order_by'] ) ? sanitize_text_field( wp_unslash( $_GET['order_by'] ) ) : 'menu_order';
$progress       = ! empty( $_GET['progress'] ) ? sanitize_text_field( wp_unslash( $_GET['progress'] ) ) : '';

$course_args = array(
	'post_type'      => \BlueDolphin\Lms\BDLMS_COURSE_CPT,
	'post_status'    => 'publish',
	'posts_per_page' => -1,
);
$_paged      = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
if ( get_query_var( 'page' ) ) {
	$_paged = get_query_var( 'page' );
}
if ( isset( $args['pagination'] ) && 'yes' === $args['pagination'] ) {
	$course_args['paged']          = $_paged;
	$course_args['posts_per_page'] = apply_filters( 'bdlms_courses_list_per_page', get_option( 'posts_per_page' ) );
}
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$author = ! empty( $_GET['filter_author'] ) ? (int) $_GET['filter_author'] : 0;
if ( $author ) {
	$course_args['author__in'] = array( $author );
}
if ( ! empty( $search_keyword ) ) {
	$course_args['s'] = $search_keyword;
}
if ( in_array( $_orderby, array( 'asc', 'desc' ), true ) ) {
	$course_args['orderby'] = 'title';
	$course_args['order']   = strtoupper( $_orderby );
} elseif ( 'newest' === $_orderby ) {
	$course_args['order'] = 'DESC';
} else {
	$course_args['orderby'] = 'menu_order';
}

if ( ! empty( $category ) ) {
	// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
	$course_args['tax_query'][] = array(
		'taxonomy' => \BlueDolphin\Lms\BDLMS_COURSE_CATEGORY_TAX,
		'field'    => 'term_id',
		'terms'    => $category,
		'operator' => 'IN',
	);
}

$enrol_courses = get_user_meta( get_current_user_id(), \BlueDolphin\Lms\BDLMS_ENROL_COURSES, true );

?>

<div class="bdlms-wrap alignfull">
	<div class="bdlms-course-list-wrap">
		<div class="bdlms-container">
			<div class="bdlms-course-filter">
				<button class="bdlms-filter-toggle">
					<svg width="24" height="24">
						<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#cross"></use>
					</svg>
				</button>
				<?php do_action( 'bdlms_before_search_bar' ); ?>
				<div class="bdlms-course-search">
					<form onsubmit="return false;">
						<div class="bdlms-search">
							<span class="bdlms-search-icon">
								<svg width="20" height="20">
									<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#search"></use>
								</svg>
							</span>
							<input type="text" class="bdlms-form-control" placeholder="<?php esc_attr_e( 'Search', 'bluedolphin-lms' ); ?>" value="<?php echo esc_attr( $search_keyword ); ?>">
							<button type="submit" class="bdlms-search-submit">
								<svg width="22" height="22">
									<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#angle-circle-right"></use>
								</svg>
							</button>
						</div>
					</form>
				</div>
				<form method="get" onsubmit="return false;" class="bdlms-filter-form">
					<div class="bdlms-accordion">
						<div class="bdlms-accordion-item" data-expanded="true">
							<div class="bdlms-accordion-header">
								<div class="bdlms-accordion-filter-title">
									<?php esc_html_e( 'Filters', 'bluedolphin-lms' ); ?>
								</div>
							</div>
							<?php
							$terms_list = \BlueDolphin\Lms\course_taxonomies( \BlueDolphin\Lms\BDLMS_COURSE_CATEGORY_TAX );
							?>
							<div class="bdlms-accordion-collapse">
								<div class="bdlms-pt-20">
									<div class="bdlms-form-group">
										<label class="bdlms-form-label"><?php esc_html_e( 'By Categories', 'bluedolphin-lms' ); ?></label>
										<select class="bdlms-form-control category">
											<option value=""><?php esc_html_e( 'Choose', 'bluedolphin-lms' ); ?></option>
											<?php foreach ( $terms_list as $key => $term_level ) : ?>
												<option value="<?php echo esc_attr( $term_level['id'] ); ?>" <?php selected( reset( $category ), $term_level['id'] ); ?>><?php echo esc_html( $term_level['name'] ); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
									<?php
									$course_status = \BlueDolphin\Lms\course_statistics();
									$total_course  = 0;
									$max_num_page  = 0;
									$has_course    = false;

									if ( ! empty( $enrol_courses ) ) {
										$course_args['post__in'] = $enrol_courses;
										$has_course              = true;
									}
									if ( ! empty( $progress ) ) {
										if ( ! empty( $course_status[ $progress ] ) ) {
											$course_args['post__in'] = $course_status[ $progress ];
										} else {
											$has_course = false;
										}
									}
									if ( $has_course ) {
										$courses      = new \WP_Query( $course_args );
										$total_course = $courses->found_posts;
										$max_num_page = $courses->max_num_pages;
									}

									$statistics = array(
										'total_courses'    => $course_status['total_course'],
										'course_completed' => count( $course_status['completed'] ),
										'course_in_progress' => count( $course_status['in_progress'] ),
										'course_not_started' => count( $course_status['not_started'] ),
									);
									?>
									<div class="bdlms-form-group">
										<label class="bdlms-form-label"><?php esc_html_e( 'By Progress', 'bluedolphin-lms' ); ?></label>
										<select class="bdlms-form-control progress">
											<option value=""><?php esc_html_e( 'Choose', 'bluedolphin-lms' ); ?></option>
											<?php
											foreach ( $statistics as $key => $value ) :
												if ( 'total_courses' !== $key ) :
													$_key = str_replace( 'course_', '', $key );
													?>
													<option value="<?php echo esc_attr( $_key ); ?>" <?php selected( $progress, str_replace( 'course ', '', $_key ) ); ?>><?php echo esc_html( ucwords( str_replace( '_', ' ', $key ) ) ); ?></option>
													<?php
												endif;
											endforeach;
											?>
										</select>
									</div>
									<button class="bdlms-reset-btn"><?php esc_html_e( 'Reset', 'bluedolphin-lms' ); ?></button>
								</div>
							</div>
							<input type="hidden" name="category" value="<?php echo esc_attr( (string) reset( $category ) ); ?>">
							<input type="hidden" name="_s" value="<?php echo esc_attr( $search_keyword ); ?>">
							<input type="hidden" name="progress" value="<?php echo esc_attr( $progress ); ?>">
							<input type="hidden" name="order_by" value="<?php echo esc_attr( $_orderby ); ?>">
						</div>
					</div>
				</form>
			</div>
			<div class="bdlms-course-view" id="bdlms_course_view">
				<div class="bdlms-course-view__header">
					<div class="bdlms-filtered-item">
						<?php
						echo wp_kses(
							sprintf(
								// Translators: %d total courses.
								__( 'My Learnings <span>(%d Courses)</span>', 'bluedolphin-lms' ),
								$total_course
							),
							array(
								'span' => array(),
							)
						);
						?>
					</div>
					<div class="bdlms-sort-by">
						<form onsubmit="return false;">
							<select>
								<option value=""><?php esc_html_e( 'Sort By', 'bluedolphin-lms' ); ?></option>
								<option value="asc"<?php selected( $_orderby, 'asc' ); ?>><?php esc_html_e( 'Alphabetically (A To Z)', 'bluedolphin-lms' ); ?></option>
								<option value="desc"<?php selected( $_orderby, 'desc' ); ?>><?php esc_html_e( 'Alphabetically (Z To A)', 'bluedolphin-lms' ); ?></option>
								<option value="newest"<?php selected( $_orderby, 'newest' ); ?>><?php esc_html_e( 'Newest', 'bluedolphin-lms' ); ?></option>
							</select>
						</form>
						<button class="bdlms-filter-toggle">
							<svg width="24" height="24">
								<use xlink:href="assets/images/sprite-front.svg#filters"></use>
							</svg>
						</button>
					</div>
				</div>
				<div class="bdlms-course-view__body">
					<div class="bdlms-statistics">
						<ul>
							<?php
							foreach ( $statistics as $key => $value ) :
								$stat_title = ucwords( str_replace( '_', ' ', $key ) );
								?>
								<li>
									<div class="bdlms-statistics__title"><?php echo esc_html( $stat_title ); ?></div>
									<div class="bdlms-statistics__no"><?php echo esc_html( $value ); ?></div>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
					<?php if ( $has_course && $courses->have_posts() ) : ?>
						<div class="bdlms-course-list">
							<ul>
								<?php
								while ( $courses->have_posts() ) :
									$courses->the_post();

									$course_id        = get_the_ID();
									$get_terms        = get_the_terms( $course_id, \BlueDolphin\Lms\BDLMS_COURSE_CATEGORY_TAX );
									$terms_name       = join( ', ', wp_list_pluck( $get_terms, 'name' ) );
									$curriculums      = get_post_meta( $course_id, \BlueDolphin\Lms\META_KEY_COURSE_CURRICULUM, true );
									$total_lessons    = 0;
									$total_quizzes    = 0;
									$course_view_link = get_the_permalink();
									$course_link      = $course_view_link;
									$button_text      = esc_html__( 'Start Learning', 'bluedolphin-lms' );
									$extra_class      = '';
									$course_progress  = '0%';
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
											$current_status = get_user_meta( $user_id, $meta_key, true );
											$current_status = ! empty( $current_status ) ? explode( '_', $current_status ) : array();
											if ( ! empty( $current_status ) ) {
												$course_progress = \BlueDolphin\Lms\calculate_course_progress( get_the_ID(), $curriculums, $current_status ) . '%';
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

										$button_text = apply_filters( 'bdlms_course_view_button_text', $button_text );
										$course_link = apply_filters( 'bdlms_course_view_button_link', $course_link );
										?>
										<li>
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
																		)
																	),
																	get_the_author_meta( 'display_name' )
																),
																array(
																	'a' => array(
																		'href' => true,
																		'class' => true,
																		'target' => true,
																	),
																)
															);
														?>
													</div>
													<h3 class="bdlms-course-item__title"><a href="<?php echo esc_url( $course_link ); ?>"><?php the_title(); ?></a></h3>
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
																	printf( esc_html__( '%s Hours', 'bluedolphin-lms' ), esc_html( (string) $duration_str ) );
																} else {
																	echo esc_html__( 'Lifetime', 'bluedolphin-lms' );
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
													<div class="bdlms-course-item__action">
														<a href="<?php echo esc_url( $course_link ); ?>" class="bdlms-btn bdlms-btn-block<?php echo esc_attr( $extra_class ); ?>" ><?php echo esc_html( $button_text ); ?></a>
														<?php if ( '100%' === $course_progress ) : ?>
															<a href="javascript:;" id="download-certificate" data-course="<?php echo esc_attr( (string) $course_id ); ?>" class="bdlms-btn bdlms-btn-block download-certificate"><?php esc_html_e( 'Download certificate', 'bluedolphin-lms' ); ?></a>
														<?php endif; ?>
													</div>
												</div>
											</div>
										</li>
										<?php
									}
								endwhile;
								?>
							</ul>
						</div>
					<?php elseif ( ! empty( $search_keyword ) ) : ?>
						<div class="bdlms-text-xl bdlms-p-16 bdlms-bg-gray bdlms-text-center bdlms-text-primary-dark"><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'bluedolphin-lms' ); ?> <a href="<?php echo esc_url( \BlueDolphin\Lms\get_page_url( 'courses' ) ); ?>"><?php esc_html_e( 'Back to courses', 'bluedolphin-lms' ); ?>.</a></div>
					<?php else : ?>
						<div class="bdlms-text-xl bdlms-p-16 bdlms-bg-gray bdlms-text-center bdlms-text-primary-dark"><?php esc_html_e( 'No courses were found.', 'bluedolphin-lms' ); ?></div>
					<?php endif; ?>
				</div>
				<?php if ( isset( $args['pagination'] ) && 'yes' === $args['pagination'] ) : ?>
					<div class="bdlms-course-view__footer">
						<div class="bdlms-pagination">
							<?php
							$big            = 999999999;
							$paginate_links = paginate_links(
								array(
									'base'      => str_replace( (string) $big, '%#%', get_pagenum_link( $big ) ),
									'format'    => '?paged=%#%',
									'current'   => max( 1, $_paged ),
									'total'     => $max_num_page,
									'prev_text' => '',
									'next_text' => '',
								)
							);
							if ( $paginate_links ) {
								echo wp_kses_post( $paginate_links );
							}
							?>
						</div>
					</div>
				<?php endif; ?>
				<?php wp_reset_postdata(); ?>
			</div>
		</div>
	</div>
</div>
