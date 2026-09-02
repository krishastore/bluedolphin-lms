<?php
/**
 * Template: Assign Course To Me.
 *
 * @package ST\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended,PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! ( current_user_can( 'assign_course' ) || current_user_can( 'manage_options' ) ) ) { //phpcs:ignore WordPress.WP.Capabilities.Unknown 
	exit;
}

$course_assigned_by_me = get_user_meta( get_current_user_id(), \ST\Lms\STLMS_COURSE_ASSIGN_BY_ME, true ) ? get_user_meta( get_current_user_id(), \ST\Lms\STLMS_COURSE_ASSIGN_BY_ME, true ) : array();
$due_soon              = get_option( 'stlms_settings' );
$due_soon              = ! empty( $due_soon['due_soon'] ) ? $due_soon['due_soon'] : '';
$stlms_users           = array();

foreach ( $course_assigned_by_me as $key => $completion_date ) :
	list( $course_id, $_user_id ) = explode( '_', $key, 2 );
	if ( get_userdata( $_user_id ) ) {
		$stlms_users[] = get_userdata( $_user_id )->display_name;
	}
endforeach;

$stlms_users = array_unique( $stlms_users );
?>
<div class="stlms-wrap alignfull">
	<?php require_once STLMS_TEMPLATEPATH . '/frontend/sub-header.php'; ?>
	<div class="stlms-course-list-wrap">
		<div class="stlms-container">
			<div class="stlms-course-filter">
				<button class="stlms-filter-toggle stlms-filter-close">
					<svg width="24" height="24">
						<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#cross"></use>
					</svg>
				</button>
				<div class="stlms-accordion">
					<div class="stlms-accordion-item" data-expanded="true">
						<div class="stlms-accordion-header">
							<div class="stlms-accordion-filter-title">
								<?php esc_html_e( 'Filters', 'skilltriks' ); ?>
							</div>
						</div>
						<div class="stlms-accordion-collapse">
							<div class="stlms-pt-20">
								<div class="stlms-form-group">
									<label class="stlms-select-search" for="id_label_nosearch">
										<?php esc_html_e( 'By Progress', 'skilltriks' ); ?>
										<select data-placeholder="Choose" class="stlms-select2 js-states stlms-form-control" data-minimum-results-for-search="Infinity" id="id_label_nosearch">
											<option value=""><?php esc_html_e( 'Choose', 'skilltriks' ); ?></option>
											<option value="not-started"><?php esc_html_e( 'Not Started', 'skilltriks' ); ?></option>
											<option value="in-progress"><?php esc_html_e( 'In Progress', 'skilltriks' ); ?></option>
											<option value="completed"><?php esc_html_e( 'Completed', 'skilltriks' ); ?></option>	
										</select>
									</label>
								</div>
								<div class="stlms-form-group">
									<label class="stlms-select-search" for="id_label_user"><?php esc_html_e( 'By Assignee', 'skilltriks' ); ?>
										<select data-placeholder="Choose" class="stlms-select2 js-states stlms-form-control" id="id_label_user">
											<option value=""><?php esc_html_e( 'Choose', 'skilltriks' ); ?></option>
											<?php foreach ( $stlms_users as $users ) : ?>
											<option value="<?php echo esc_html( $users ); ?>"><?php echo esc_html( $users ); ?></option>
											<?php endforeach; ?>
										</select>
									</label>
								</div>
								<button class="stlms-reset-btn"><?php esc_html_e( 'Reset', 'skilltriks' ); ?></button>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="stlms-course-view">
				<?php if ( current_user_can( 'assign_course' ) || current_user_can( 'manage_options' ) ) : //phpcs:ignore WordPress.WP.Capabilities.Unknown ?>
				<div class="stlms-course-view__header">
					<div class="stlms-filtered-item">
						<?php esc_html_e( 'Assign Course', 'skilltriks' ); ?>
					</div>
					<div class="stlms-sort-by">
						<a href="<?php echo esc_url( \ST\Lms\get_page_url( 'assign_new_course' ) ); ?>" class="stlms-btn">
							<?php esc_html_e( 'Assign New Course', 'skilltriks' ); ?>
						</a>
						<button class="stlms-filter-toggle">
							<svg width="24" height="24">
								<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#filters"></use>
							</svg>
						</button>
					</div>
				</div>
				<?php endif; ?>
				<div class="stlms-course-view__body">
					<?php if ( current_user_can( 'assign_course' ) || current_user_can( 'manage_options' ) ) : //phpcs:ignore WordPress.WP.Capabilities.Unknown ?>
					<div class="stlms-assigned-course__header">
						<a href="<?php echo esc_url( \ST\Lms\get_page_url( 'assign_course_by_me' ) ); ?>" class="stlms-assigned-course__btn active">
							<span>
								<?php esc_html_e( 'Assigned By Me', 'skilltriks' ); ?>
							</span>
						</a>
						<a href="<?php echo esc_url( \ST\Lms\get_page_url( 'assign_course_to_me' ) ); ?>" class="stlms-assigned-course__btn ">
							<span>
								<?php esc_html_e( 'Assigned To Me', 'skilltriks' ); ?>
							</span>
						</a>
					</div>
					<?php endif; ?>
					<div class="stlms-datatable">
						<table id="myTable" class="stripe row-border" style="width:100%">
							<thead>
								<tr>
									<th><?php esc_html_e( 'Course Assigned', 'skilltriks' ); ?></th>
									<th><?php esc_html_e( 'Assigned To', 'skilltriks' ); ?></th>
									<th><?php esc_html_e( 'Completion Date', 'skilltriks' ); ?></th>
									<th><?php esc_html_e( 'Progress Status', 'skilltriks' ); ?></th>
									<th><?php esc_html_e( 'Actions', 'skilltriks' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
								foreach ( $course_assigned_by_me as $key => $completion_date ) :

									list( $course_id, $_user_id ) = explode( '_', $key, 2 );

									$user_info            = get_userdata( $_user_id );
									$completion_date      = strtotime( $completion_date );
									$date_format          = get_option( 'date_format' );
									$due_soon_day         = '-' . $due_soon . ' day';
									$due_date             = strtotime( $due_soon_day, $completion_date );
									$formatted_date       = wp_date( $date_format, $completion_date );
									$current_status       = get_user_meta( $_user_id, sprintf( \ST\Lms\STLMS_COURSE_STATUS, $course_id ), true );
									$curriculums          = get_post_meta( $course_id, \ST\Lms\META_KEY_COURSE_CURRICULUM, true );
									$curriculums          = \ST\Lms\merge_curriculum_items( $curriculums );
									$curriculums          = array_keys( $curriculums );
									$course_progress      = ! empty( $current_status ) ? \ST\Lms\calculate_course_progress( $course_id, $curriculums, $current_status ) . '%' : '0%';
									$course_completed_key = sprintf( \ST\Lms\STLMS_COURSE_COMPLETED_ON, $course_id );
									$completed_on         = get_user_meta( $_user_id, $course_completed_key, true );

									if ( $completed_on ) {
										$course_status   = 'Completed';
										$course_progress = '100%';
									} elseif ( '0%' === $course_progress ) {
										$course_status = 'Not Started';
									} else {
										$course_status = 'In Progress';
									}

									if ( 'publish' === get_post_status( $course_id ) ) :
										?>
								<tr data-key="<?php echo esc_attr( $key ); ?>">
									<td>
										<a href="<?php echo esc_url( get_permalink( $course_id ) ); ?>" class="stlms-datatable__course-link">
											<?php echo esc_html( get_the_title( $course_id ) ); ?>
										</a>
									</td>
									<td><?php echo esc_html( $user_info->display_name ); ?></td>
									<td data-date="<?php echo esc_attr( $completion_date ); ?>">
										<div class="due-date">
											<?php echo ! empty( $formatted_date ) ? esc_html( $formatted_date ) : '-'; ?>
											<?php
											if ( ! empty( $completion_date ) && '100%' !== $course_progress ) :
													$today_timestamp     = (int) current_datetime()->format( 'U' );
													$formatted_timestamp = strtotime( $formatted_date );
												?>
												<?php if ( $today_timestamp >= $due_date && $today_timestamp <= $formatted_timestamp ) : ?>	
													<span class="due-soon-tag">
														<?php esc_html_e( 'Due Soon', 'skilltriks' ); ?>
													</span>
												<?php endif; ?>
												<?php if ( $today_timestamp > $formatted_timestamp ) : ?>	
													<span class="due-tag">
														<?php esc_html_e( 'Due', 'skilltriks' ); ?>
													</span>
													<?php
												endif;
											endif;
											?>
										</div>
									</td>
									<td>
										<div class="stlms-progress">
											<?php echo esc_html( $course_status . '(' . $course_progress . ')' ); ?>
											<div class="stlms-progress__bar">
												<div class="stlms-progress__bar-inner" style="width: <?php echo esc_html( $course_progress ); ?>"></div>
											</div>
										</div>
									</td>
									<td>
										<div class="stlms-assigned-course__action">
											<?php if ( '100%' !== $course_progress ) : ?>
											<button class="stlms-assigned-course__button edit"
												data-fancybox data-src="#edit-course">
												<svg width="19" height="17">
													<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#edit-assigned-course"></use>
												</svg>
											</button>
											<?php endif; ?>
											<button class="stlms-assigned-course__button delete" data-fancybox data-src="#delete-course">
												<svg width="14" height="17">
													<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#delete-assigned-course"></use>
												</svg>
											</button>
										</div>
									</td>
								</tr>
										<?php
									endif;
								endforeach;
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- delete popup -->
<div id="delete-course" class="stlms-dialog" style="display: none;">
	<form class="stlms-assign-course__box">
		<div class="stlms-dialog__header">
			<div class="stlms-dialog__title">
				<?php esc_html_e( 'Confirm Deletion', 'skilltriks' ); ?>
			</div>
			<button class="stlms-dialog__close" data-fancybox-close>
				<svg width="30" height="30">
					<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#cross"></use>
				</svg>
			</button>
		</div>
		<div class="stlms-dialog__content-box">
			<div class="stlms-dialog__content">
				<p><?php esc_html_e( 'Are you sure you want to delete this item? This action cannot be undone.', 'skilltriks' ); ?></p>
			</div>
		</div>
		<div class="stlms-dialog__footer">
			<div class="stlms-dialog__cta">
				<button class="stlms-btn stlms-btn-outline" data-fancybox-close><?php esc_html_e( 'Cancel', 'skilltriks' ); ?></button>
				<button class="stlms-btn delete" data-fancybox-close><?php esc_html_e( 'Delete', 'skilltriks' ); ?></button>
			</div>
		</div>
	</form>
</div>

<!-- edit popup -->
<?php
$course_args = array(
	'post_type'      => \ST\Lms\STLMS_COURSE_CPT,
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	'fields'         => 'ids',
);
$courses     = get_posts( $course_args );
?>
<div id="edit-course" class="stlms-dialog" style="display: none;">
	<form class="stlms-assign-course__box">
		<div class="stlms-dialog__header">
			<div class="stlms-dialog__title">
				<?php esc_html_e( 'Edit Assigned Course Date', 'skilltriks' ); ?>
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
					<?php
					echo wp_kses_post(
						sprintf(
							/* translators: 1: course span tag, 2: user span tag */
							__( 'Update the completion date for the course %1$s assigned to %2$s.', 'skilltriks' ),
							'<span class="course-name"></span>',
							'<span class="user-name"></span>'
						)
					);
					?>
					</p>
				</div>
			</div>
			<div class="stlms-dialog__content">
				<div class="stlms-form-group">
					<label for="completion-date"></label>
					<input type="date" id="completion-date">
				</div>
			</div>
		</div>
		<div class="stlms-dialog__footer">
			<div class="stlms-dialog__cta">
				<button class="stlms-btn stlms-btn-outline" data-fancybox-close><?php esc_html_e( 'Cancel', 'skilltriks' ); ?></button>
				<button class="stlms-btn update" data-fancybox-close><?php esc_html_e( 'Update', 'skilltriks' ); ?></button>
			</div>
		</div>
	</form>
</div>
