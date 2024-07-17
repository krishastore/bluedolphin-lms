<?php
/**
 * Template: Courses
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
$levels         = ! empty( $_GET['levels'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_GET['levels'] ) ) ) : array();
$levels         = array_map( 'intval', $levels );
$_orderby       = ! empty( $_GET['order_by'] ) ? sanitize_text_field( wp_unslash( $_GET['order_by'] ) ) : 'menu_order';

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
if ( ! empty( $levels ) ) {
	// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
	$course_args['tax_query'][] = array(
		'taxonomy' => \BlueDolphin\Lms\BDLMS_COURSE_TAXONOMY_TAG,
		'field'    => 'term_id',
		'terms'    => $levels,
		'operator' => 'IN',
	);
}

$course_args = apply_filters( 'bdlms_course_list_page_query', $course_args );
$courses     = new \WP_Query( $course_args );

?>
<div class="bdlms-wrap alignfull">
	<div class="bdlms-course-list-wrap">
		<div class="bdlms-container">
			<?php if ( $courses->have_posts() && ( isset( $args['filter'] ) && 'yes' === $args['filter'] ) ) : ?>
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
					<div class="bdlms-accordion bdlms-pb-20">
						<div class="bdlms-accordion-item" data-expanded="true">
							<div class="bdlms-accordion-header">
								<div class="bdlms-accordion-filter-title"><?php esc_html_e( 'Course category', 'bluedolphin-lms' ); ?></div>
							</div>
							<?php
							$get_terms  = get_terms(
								array(
									'taxonomy'   => \BlueDolphin\Lms\BDLMS_COURSE_CATEGORY_TAX,
									'hide_empty' => true,
								)
							);
							$terms_list = array();
							if ( ! empty( $get_terms ) ) {
								$terms_list = array_map(
									function ( $term ) {
										return array(
											'id'    => $term->term_id,
											'name'  => $term->name,
											'count' => $term->count,
										);
									},
									$get_terms
								);
							}
							$total_count = $courses->found_posts;
							?>
							<div class="bdlms-accordion-collapse">
								<div class="bdlms-filter-list">
									<ul>
										<li>
											<div class="bdlms-check-wrap">
												<input type="checkbox" class="bdlms-check" id="bdlms_category_all">
												<label for="bdlms_category_all" class="bdlms-check-label"><?php esc_html_e( 'All', 'bluedolphin-lms' ); ?><span><?php echo esc_html( $total_count ); ?></span></label>
											</div>
										</li>
										<?php foreach ( $terms_list as $key => $course_term ) : ?>
											<li>
												<div class="bdlms-check-wrap">
													<input type="checkbox" name="category[]" class="bdlms-check" id="bd_course_term_<?php echo (int) $key; ?>" value="<?php echo esc_attr( $course_term['id'] ); ?>"<?php echo in_array( $course_term['id'], $category, true ) ? ' checked' : ''; ?>>
													<label for="bd_course_term_<?php echo (int) $key; ?>" class="bdlms-check-label">
														<?php echo esc_html( $course_term['name'] ); ?>
														<span><?php echo esc_html( $course_term['count'] ); ?></span>
													</label>
												</div>
											</li>
										<?php endforeach; ?>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<div class="bdlms-accordion bdlms-pb-20">
						<div class="bdlms-accordion-item" data-expanded="true">
							<div class="bdlms-accordion-header">
								<div class="bdlms-accordion-filter-title"><?php esc_html_e( 'Course Level', 'bluedolphin-lms' ); ?></div>
							</div>
							<?php
							$get_levels  = get_terms(
								array(
									'taxonomy'   => \BlueDolphin\Lms\BDLMS_COURSE_TAXONOMY_TAG,
									'hide_empty' => true,
								)
							);
							$levels_list = array();
							if ( ! empty( $get_levels ) ) {
								$levels_list = array_map(
									function ( $term ) {
										return array(
											'id'    => $term->term_id,
											'name'  => $term->name,
											'count' => $term->count,
										);
									},
									$get_levels
								);
							}
							$total_count = count( $levels_list );
							?>
							<div class="bdlms-accordion-collapse">
								<div class="bdlms-filter-list">
									<ul>
										<li>
											<div class="bdlms-check-wrap">
												<input type="checkbox" class="bdlms-check" id="bdlms_level_all">
												<label for="bdlms_level_all" class="bdlms-check-label"><?php esc_html_e( 'All', 'bluedolphin-lms' ); ?><span><?php echo esc_html( $total_count ); ?></span></label>
											</div>
										</li>
										<?php foreach ( $levels_list as $key => $get_level ) : ?>
											<li>
												<div class="bdlms-check-wrap">
													<input type="checkbox" name="levels[]" class="bdlms-check" id="bd_course_level_<?php echo (int) $key; ?>" value="<?php echo esc_attr( $get_level['id'] ); ?>"<?php echo in_array( $get_level['id'], $levels, true ) ? ' checked' : ''; ?>>
													<label for="bd_course_level_<?php echo (int) $key; ?>" class="bdlms-check-label">
														<?php echo esc_html( $get_level['name'] ); ?>
														<span><?php echo esc_html( $get_level['count'] ); ?></span>
													</label>
												</div>
											</li>
										<?php endforeach; ?>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<input type="hidden" name="order_by" value="<?php echo esc_attr( $_orderby ); ?>">
					<input type="hidden" name="_s" value="<?php echo esc_attr( $search_keyword ); ?>">
				</form>
			</div>
			<?php endif; ?>
			<div class="bdlms-course-view" id="bdlms_course_view">
				<?php if ( $courses->have_posts() ) : ?>
				<div class="bdlms-course-view__header">
					<div class="bdlms-filtered-item">
						<?php
						echo esc_html(
							sprintf(
								// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment 
								_n( 'Showing %d course', 'Showing %d courses', $courses->post_count, 'bluedolphin-lms' ),
								$courses->post_count
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
								<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#filters"></use>
							</svg>
						</button>
					</div>
				</div>
				<?php endif; ?>
				<div class="bdlms-course-view__body">
					<?php if ( $courses->have_posts() ) : ?>
						<div class="bdlms-course-list">
							<ul>
								<?php
								while ( $courses->have_posts() ) :
									$courses->the_post();
									$get_terms          = get_the_terms( get_the_ID(), \BlueDolphin\Lms\BDLMS_COURSE_CATEGORY_TAX );
									$terms_name         = join( ', ', wp_list_pluck( $get_terms, 'name' ) );
									$curriculums        = get_post_meta( get_the_ID(), \BlueDolphin\Lms\META_KEY_COURSE_CURRICULUM, true );
									$total_lessons      = 0;
									$total_quizzes      = 0;
									$course_detail_link = get_the_permalink();
									$button_text        = esc_html__( 'Start Learning', 'bluedolphin-lms' );
									$extra_class        = '';
									if ( ! empty( $curriculums ) ) {
										$lessons        = \BlueDolphin\Lms\get_curriculums( $curriculums, \BlueDolphin\Lms\BDLMS_LESSON_CPT );
										$total_lessons  = count( $lessons );
										$quizzes        = \BlueDolphin\Lms\get_curriculums( $curriculums, \BlueDolphin\Lms\BDLMS_QUIZ_CPT );
										$total_quizzes  = count( $quizzes );
										$total_duration = \BlueDolphin\Lms\count_duration( array_merge( $lessons, $quizzes ) );
										$curriculums    = \BlueDolphin\Lms\merge_curriculum_items( $curriculums );
										$curriculums    = array_keys( $curriculums );
										if ( is_user_logged_in() ) {
											$meta_key       = sprintf( \BlueDolphin\Lms\BDLMS_COURSE_STATUS, get_the_ID() );
											$user_id        = get_current_user_id();
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
											} else {
												$first_curriculum = reset( $curriculums );
												$first_curriculum = explode( '_', $first_curriculum );
												$first_curriculum = array_map( 'intval', $first_curriculum );
												$section_id       = reset( $first_curriculum );
												$item_id          = end( $first_curriculum );
											}
											$curriculum_type = get_post_type( $item_id );
											$curriculum_type = str_replace( 'bdlms_', '', $curriculum_type );
											$course_link     = sprintf( '%s/%d/%s/%d/', untrailingslashit( $course_detail_link ), $section_id, $curriculum_type, $item_id );
										}
										$button_text = apply_filters( 'bdlms_course_view_button_text', $button_text );
										$course_link = apply_filters( 'bdlms_course_view_button_link', $course_link );
									}

									?>
									<li>
										<div class="bdlms-course-item">
											<div class="bdlms-course-item__img">
												<?php if ( ! empty( $terms_name ) ) : ?>
													<div class="bdlms-course-item__tag">
														<span><?php echo esc_html( $terms_name ); ?></span>
													</div>
												<?php endif; ?>
												<a href="<?php echo esc_url( $course_detail_link ); ?>">
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
																printf( esc_html__( '%s Hours', 'bluedolphin-lms' ), esc_html( $duration_str ) );
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
												<div class="bdlms-course-item__action">
													<a href="<?php echo esc_url( $course_link ); ?>" class="bdlms-btn bdlms-btn-block<?php echo esc_attr( $extra_class ); ?>"><?php echo esc_html( $button_text ); ?></a>
												</div>
											</div>
										</div>
									</li>
								<?php endwhile; ?>
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
									'base'      => str_replace( $big, '%#%', get_pagenum_link( $big ) ),
									'format'    => '?paged=%#%',
									'current'   => max( 1, $_paged ),
									'total'     => $courses->max_num_pages,
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
