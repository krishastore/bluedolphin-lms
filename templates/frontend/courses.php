<?php
/**
 * Template: Courses
 *
 * @package ST\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended,PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$search_keyword = ! empty( $_GET['_s'] ) ? sanitize_text_field( wp_unslash( $_GET['_s'] ) ) : '';
$category       = ! empty( $_GET['category'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_GET['category'] ) ) ) : array();
$category       = array_map( 'intval', $category );
$levels         = ! empty( $_GET['levels'] ) ? explode( ',', sanitize_text_field( wp_unslash( $_GET['levels'] ) ) ) : array();
$levels         = array_map( 'intval', $levels );
$_orderby       = ! empty( $_GET['order_by'] ) ? sanitize_text_field( wp_unslash( $_GET['order_by'] ) ) : '';

$course_args = array(
	'post_type'      => \ST\Lms\STLMS_COURSE_CPT,
	'post_status'    => 'publish',
	'posts_per_page' => -1,
);
$_paged      = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
if ( get_query_var( 'page' ) ) {
	$_paged = get_query_var( 'page' );
}
if ( isset( $args['pagination'] ) && 'yes' === $args['pagination'] ) {
	$course_args['paged']          = $_paged;
	$course_args['posts_per_page'] = apply_filters( 'stlms_courses_list_per_page', get_option( 'posts_per_page' ) );
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
	$course_args['orderby'] = 'post_date';
}
if ( ! empty( $category ) ) {
	// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
	$course_args['tax_query'][] = array(
		'taxonomy' => \ST\Lms\STLMS_COURSE_CATEGORY_TAX,
		'field'    => 'term_id',
		'terms'    => $category,
		'operator' => 'IN',
	);
}
if ( ! empty( $levels ) ) {
	// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
	$course_args['tax_query'][] = array(
		'taxonomy' => \ST\Lms\STLMS_COURSE_TAXONOMY_TAG,
		'field'    => 'term_id',
		'terms'    => $levels,
		'operator' => 'IN',
	);
}

$course_args = apply_filters( 'stlms_course_list_page_query', $course_args );
$courses     = new \WP_Query( $course_args );

?>
<div class="stlms-wrap alignfull">
	<?php require_once STLMS_TEMPLATEPATH . '/frontend/sub-header.php'; ?>
	<div class="stlms-course-list-wrap">
		<div class="stlms-container">
			<?php if ( isset( $args['filter'] ) && 'yes' === $args['filter'] ) : ?>
			<div class="stlms-course-filter">
				<button class="stlms-filter-toggle stlms-filter-close">
					<svg width="24" height="24">
						<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#cross"></use>
					</svg>
				</button>
				<form method="get" onsubmit="return false;" class="stlms-filter-form">
					<div class="stlms-accordion stlms-pb-20">
						<div class="stlms-accordion-item" data-expanded="true">
							<div class="stlms-accordion-header">
								<div class="stlms-accordion-filter-title"><?php esc_html_e( 'Course category', 'skilltriks' ); ?></div>
							</div>
							<?php
							$terms_list = \ST\Lms\course_taxonomies( \ST\Lms\STLMS_COURSE_CATEGORY_TAX );
							?>
							<div class="stlms-accordion-collapse">
								<div class="stlms-filter-list">
									<div class="stlms-form-group">
										<select class="stlms-form-control category">
											<option value=""><?php esc_html_e( 'Choose', 'skilltriks' ); ?></option>
											<?php foreach ( $terms_list as $key => $term_level ) : ?>
												<option value="<?php echo esc_attr( $term_level['id'] ); ?>" <?php selected( reset( $category ), $term_level['id'] ); ?>><?php echo esc_html( $term_level['name'] ); ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php
					$levels_list = \ST\Lms\course_taxonomies( \ST\Lms\STLMS_COURSE_TAXONOMY_TAG );
					if ( ! empty( $levels_list ) ) :
						?>
					<div class="stlms-accordion stlms-pb-20">
						<div class="stlms-accordion-item" data-expanded="true">
							<div class="stlms-accordion-header">
								<div class="stlms-accordion-filter-title"><?php esc_html_e( 'Course Level', 'skilltriks' ); ?></div>
							</div>
							<div class="stlms-accordion-collapse">
								<div class="stlms-filter-list">
									<ul>
									<?php foreach ( $levels_list as $key => $get_level ) : ?>
										<li>
											<div class="stlms-check-wrap">
												<input type="checkbox" name="levels[]" class="stlms-check" id="st_course_level_<?php echo (int) $key; ?>" value="<?php echo esc_attr( $get_level['id'] ); ?>"<?php echo in_array( $get_level['id'], $levels, true ) ? ' checked' : ''; ?>>
												<label for="st_course_level_<?php echo (int) $key; ?>" class="stlms-check-label">
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
					<?php endif; ?>
					<input type="hidden" name="category" value="<?php echo esc_attr( (string) reset( $category ) ); ?>">
					<input type="hidden" name="order_by" value="<?php echo esc_attr( $_orderby ); ?>">
					<input type="hidden" name="_s" value="<?php echo esc_attr( $search_keyword ); ?>">
				</form>
			</div>
			<?php endif; ?>
			<div class="stlms-course-view" id="stlms_course_view">
				<?php if ( $courses->have_posts() ) : ?>
				<div class="stlms-course-view__header">
					<div class="stlms-filtered-item">
						<?php
						echo esc_html(
							sprintf(
								// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment 
								_n( 'Showing %s course', 'Showing %s courses', (int) $courses->post_count, 'skilltriks' ),
								number_format_i18n( $courses->post_count )
							)
						);
						?>
						<?php
						$category_id = isset( $_GET['category'] ) ? (int) $_GET['category'] : 0;
						if ( $category_id ) {
							$_term = get_term_by( 'term_id', $category_id, \ST\Lms\STLMS_COURSE_CATEGORY_TAX );
							if ( $_term && ! is_wp_error( $_term ) ) {
								echo esc_html( '(' . $_term->name . ')' );
							}
						}
						?>
					</div>
					<div class="stlms-sort-by">
						<form onsubmit="return false;">
							<select>
								<option value=""><?php esc_html_e( 'Sort By', 'skilltriks' ); ?></option>
								<option value="asc"<?php selected( $_orderby, 'asc' ); ?>><?php esc_html_e( 'Alphabetically (A To Z)', 'skilltriks' ); ?></option>
								<option value="desc"<?php selected( $_orderby, 'desc' ); ?>><?php esc_html_e( 'Alphabetically (Z To A)', 'skilltriks' ); ?></option>
								<option value="newest"<?php selected( $_orderby, 'newest' ); ?>><?php esc_html_e( 'Newest', 'skilltriks' ); ?></option>
							</select>
						</form>
						<button class="stlms-filter-toggle">
							<svg width="24" height="24">
								<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#filters"></use>
							</svg>
						</button>
					</div>
				</div>
				<?php endif; ?>
				<div class="stlms-course-view__body">
					<?php if ( $courses->have_posts() ) : ?>
						<div class="stlms-course-list">
							<ul>
								<?php
								while ( $courses->have_posts() ) :
									$courses->the_post();
									$get_terms        = get_the_terms( get_the_ID(), \ST\Lms\STLMS_COURSE_CATEGORY_TAX );
									$terms_name       = join( ', ', wp_list_pluck( $get_terms, 'name' ) );
									$curriculums      = get_post_meta( get_the_ID(), \ST\Lms\META_KEY_COURSE_CURRICULUM, true );
									$total_lessons    = 0;
									$total_quizzes    = 0;
									$course_view_link = get_the_permalink();
									$course_link      = $course_view_link;
									$button_text      = esc_html__( 'Enrol Now', 'skilltriks' );
									$extra_class      = '';
									$is_enrol         = false;
									$total_duration   = 0;
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

										$button_text = apply_filters( 'stlms_course_view_button_text', $button_text );
										$course_link = apply_filters( 'stlms_course_view_button_link', $course_link );
									}

									?>
									<li>
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
														<img fetchpriority="high" decoding="async" src="<?php echo esc_url( STLMS_ASSETS ); ?>/images/course-item-placeholder.png" alt="<?php the_title(); ?>" width="1200" height="1200">
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
																echo esc_html__( 'Lifetime', 'skilltriks' );
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
																echo esc_html( sprintf( __( '%s Lessons', 'skilltriks' ), $total_lessons ) );
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
													<a href="<?php echo ! $is_enrol && is_user_logged_in() ? 'javascript:;' : esc_url( $course_link ); ?>" class="stlms-btn stlms-btn-block<?php echo esc_attr( $extra_class ); ?>" id="<?php echo ! $is_enrol && is_user_logged_in() ? 'enrol-now' : ''; ?>" data-course="<?php echo esc_html( (string) get_the_ID() ); ?>"><?php echo esc_html( $button_text ); ?><i class="stlms-loader"></i></a>
												</div>
											</div>
										</div>
									</li>
								<?php endwhile; ?>
							</ul>
						</div>
					<?php elseif ( ! empty( $search_keyword ) ) : ?>
						<div class="stlms-text-xl stlms-p-16 stlms-bg-gray stlms-text-center stlms-text-primary-dark"><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'skilltriks' ); ?> <a href="<?php echo esc_url( \ST\Lms\get_page_url( 'courses' ) ); ?>"><?php esc_html_e( 'Back to courses', 'skilltriks' ); ?>.</a></div>
					<?php else : ?>
						<div class="stlms-text-xl stlms-p-16 stlms-bg-gray stlms-text-center stlms-text-primary-dark"><?php esc_html_e( 'No courses were found.', 'skilltriks' ); ?></div>
					<?php endif; ?>
				</div>
				<?php if ( isset( $args['pagination'] ) && 'yes' === $args['pagination'] ) : ?>
					<div class="stlms-course-view__footer">
						<div class="stlms-pagination">
							<?php
							$big            = 999999999;
							$paginate_links = paginate_links(
								array(
									'base'      => str_replace( (string) $big, '%#%', get_pagenum_link( $big ) ),
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
