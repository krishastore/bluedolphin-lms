<?php
/**
 * Template: Notifications
 *
 * @package ST\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended,PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( isset( $args['pagination'] ) && 'yes' === $args['pagination'] ) {
	$_paged         = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
	$items_per_page = apply_filters( 'stlms_notification_log_per_page', get_option( 'posts_per_page' ) );
}

$notifications        = \ST\Lms\fetch_notification_data( $_paged, (int) $items_per_page );
$notification_message = \ST\Lms\notification_message();
$total_items          = $notifications['items'];
$has_unread           = ! empty( $notifications['data'] ) ? ! empty( array_filter( $notifications['data'], fn( $n ) => '0' === $n['is_read'] ) ) : 0;
?>

<div class="stlms-wrap alignfull">
	<?php require_once STLMS_TEMPLATEPATH . '/frontend/sub-header.php'; ?>
	<div class="stlms-course-list-wrap">
		<div class="stlms-container">
			<div class="stlms-course-view">
				<div class="stlms-course-view__header">
					<div class="stlms-filtered-item">
						<?php esc_html_e( 'Notifications', 'skilltriks' ); ?>	
					</div>
					<?php if ( ! empty( $notifications['data'] ) && $has_unread ) : ?>
					<div class="stlms-sort-by">
						<a href="javascript:void(0);" class="stlms-btn stlms-btn-light stlms-btn-block" id="mark-all-read">
							<?php esc_html_e( 'Mark All As Read', 'skilltriks' ); ?>	
						</a>
					</div>
					<?php endif; ?>
				</div>
				<div class="stlms-course-view__body">
					<div class="stlms-notification-wrap">
						<ul>
							<?php
							if ( ! empty( $notifications['data'] ) ) {
								foreach ( $notifications['data'] as $notification ) :

									$from_user       = get_userdata( $notification['from_user_id'] );
									$from_name       = $from_user ? $from_user->display_name : 'Someone';
									$course_name     = get_the_title( $notification['course_id'] );
									$course_link     = get_permalink( $notification['course_id'] );
									$date_format     = get_option( 'date_format' );
									$due_date        = '0000-00-00' !== $notification['due_date'] ? wp_date( $date_format, strtotime( $notification['due_date'] ) ) : 'that has not been set';
									$time_diff       = human_time_diff( strtotime( $notification['created_at'] ), (int) current_datetime()->format( 'U' ) );
									$action_type     = (int) $notification['action_type'];
									$message         = isset( $notification_message[ $action_type - 1 ] ) ? $notification_message[ $action_type - 1 ] : '';
									$content_changes = ! empty( $notification['content_changes'] ) ? json_decode( $notification['content_changes'] ) : array();
									$course_name     = empty( $course_name ) && ! empty( $notification['content_changes'] ) ? $content_changes : $course_name;
									?>
								<li>
									<div class="stlms-notification-card <?php echo $notification['is_read'] ? esc_attr( 'read-notification' ) : ''; ?>">
										<div class="stlms-notification-image">
											<?php if ( ! in_array( $action_type, array( 4, 5, 6, 7 ), true ) ) : ?>
												<?php if ( ! empty( get_user_meta( $notification['from_user_id'], 'avatar_url', true ) ) ) { ?>
													<img src="<?php echo esc_url( get_user_meta( $notification['from_user_id'], 'avatar_url', true ) ); ?>" alt="user-icon" width="48" height="48">
												<?php } else { ?>
													<img src="<?php echo esc_url( get_avatar_url( $from_user ) ); ?>" alt="user-icon" width="48" height="48">
												<?php } ?>
											<?php else : ?>
												<img src="<?php echo esc_url( STLMS_ASSETS ); ?>/images/ST.png" alt="skilltriks" width="48" height="48">
											<?php endif; ?>
										</div>
										<div class="stlms-notification-content">
											<div class="stlms-notification-heading">
												<div class="stlms-notification-title">
													<?php
													if ( 8 !== $action_type ) {
														echo wp_kses_post(
															wp_sprintf(
																$message,
																esc_html( $from_name ),
																esc_url( $course_link ),
																esc_html( $course_name ),
																esc_html( $due_date )
															)
														);
													} else {
														echo wp_kses_post( wp_sprintf( '<strong>%1$s</strong> updated the content of the course <a href="%2$s">%3$s</a>.', esc_html( $from_name ), esc_url( $course_link ), esc_html( $course_name ) ) );
													}
													?>
												</div>
												<div class="stlms-notification-time">
													<?php echo esc_html( $time_diff ) . ' ago'; ?>
												</div>
											</div>
											<?php if ( 8 === $action_type ) : ?>
											<div class="stlms-notification-desc" bis_skin_checked="1">
												<ul>
													<?php
													foreach ( $content_changes as $key => $_ids ) :

														list( $_type, $_action ) = explode( '_', $key, 2 );
														$prepositions            = ( 'added' === $_action ) ? 'to' : 'from';

														foreach ( $_ids as $_id ) :
															$content_name = get_the_title( $_id );
															?>
														<li>
															<?php
																echo wp_kses_post(
																	wp_sprintf(
																		$message,
																		esc_html( ucfirst( $_type ) ),
																		esc_html( $content_name ),
																		esc_html( $_action ),
																		esc_html( $prepositions )
																	)
																);
															?>
														</li>
														<?php endforeach; ?>
													<?php endforeach; ?>
												</ul>
											</div>
											<?php endif; ?>
										</div>
										<?php if ( ! $notification['is_read'] ) : ?>
										<div class="stlms-notification-icon" data-id="<?php echo esc_attr( $notification['id'] ); ?>">
											<button>
												<svg width="30" height="30">
													<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#unread-icon"></use>
												</svg>
											</button>
										</div>
										<?php endif; ?>
									</div>
								</li>
									<?php
							endforeach;
							} else {
								?>
								<li>
									<div class="stlms-notification-card read-notification">
										<?php esc_html_e( 'No Notifications to read!', 'skilltriks' ); ?>
									</div>
								</li>
								<?php
							}
							?>
						</ul>
					</div>
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
									'total'     => ceil( $total_items / $items_per_page ),
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
			</div>
		</div>
	</div>
</div>
