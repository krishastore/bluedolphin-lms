<?php
/**
 * Display capability list template.
 *
 * @package skilltriks\Lms
 */

$user_role       = isset( $_GET['role'] ) ? sanitize_text_field( wp_unslash( $_GET['role'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$user_role       = preg_replace( '/-/', '_', $user_role );
$role_caps       = ! empty( get_role( $user_role ) ) ? get_role( $user_role )->capabilities : array();
$role_name       = preg_replace( '/_/', ' ', $user_role );
$capability_list = \ST\Lms\user_capability_list();
?>

<h1><?php esc_html_e( 'Edit User Capability', 'skilltriks' ); ?></h1>
<?php
printf(
	'<h3>Add capability to this new role:- <span>%s</span></h3>',
	esc_html( ucwords( $role_name ) )
);
?>
<div class="wrap">
	<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="user-caps-form" method="post">
		<input type="hidden" name="action" value="user_caps" />
		<input type="hidden" name="role" value="<?php echo esc_html( $user_role ); ?>" />
		<?php wp_nonce_field( 'user_caps', 'user-caps-nonce' ); ?>
		<table class="wp-list-table widefat fixed striped table-view-list capability">
			<thead>
				<tr>
					<th scope="col" id="type" class="manage-column column-type column-primary "><?php esc_html_e( 'Access Right', 'skilltriks' ); ?></th>
					<th scope="col" id="course" class="manage-column column-course"><?php esc_html_e( 'Course', 'skilltriks' ); ?></th>
					<th scope="col" id="lesson" class="manage-column column-lesson"><?php esc_html_e( 'Lesson', 'skilltriks' ); ?></th>
					<th scope="col" id="question" class="manage-column column-question"><?php esc_html_e( 'Question', 'skilltriks' ); ?></th>
					<th scope="col" id="quiz" class="manage-column column-quiz"><?php esc_html_e( 'Quiz', 'skilltriks' ); ?></th>
				</tr>
			</thead>
			<tbody id="the-list" data-wp-lists="list:capability">
				<?php foreach ( $capability_list as $cap => $content ) : ?>
				<tr>
					<td class="title column has-row-actions column-primary" data-colname="Access Right"><?php echo esc_html( $content['label'] ); ?><div class="tooltip dashicons dashicons-info"><span class="tooltip-text"><?php echo esc_html( $content['tooltip'] ); ?></span></div>
						<button type="button" class="toggle-row">
							<span class="screen-reader-text">Show more details</span>
						</button>
					</td>
					<td class="type column-type" data-colname="Course"><input name="users_can[]" type="checkbox" id="users_can_<?php echo esc_html( $cap . '_courses' ); ?>" value='<?php echo esc_html( $cap . '_courses' ); ?>' <?php echo ! empty( $role_caps ) && array_key_exists( $cap . '_courses', $role_caps ) ? esc_attr( 'checked' ) : ''; ?>></td>
					<td class="type column-type" data-colname="Lesson"><input name="users_can[]" type="checkbox" id="users_can_<?php echo esc_html( $cap . '_lessons' ); ?>" value='<?php echo esc_html( $cap . '_lessons' ); ?>' <?php echo ! empty( $role_caps ) && array_key_exists( $cap . '_lessons', $role_caps ) ? esc_attr( 'checked' ) : ''; ?>></td>
					<td class="type column-type" data-colname="Question"><input name="users_can[]" type="checkbox" id="users_can_<?php echo esc_html( $cap . '_questions' ); ?>" value='<?php echo esc_html( $cap . '_questions' ); ?>' <?php echo ! empty( $role_caps ) && array_key_exists( $cap . '_questions', $role_caps ) ? esc_attr( 'checked' ) : ''; ?>></td>
					<td class="type column-type" data-colname="Quiz"><input name="users_can[]" type="checkbox" id="users_can_<?php echo esc_html( $cap . '_quizzes' ); ?>" value='<?php echo esc_html( $cap . '_quizzes' ); ?>' <?php echo ! empty( $role_caps ) && array_key_exists( $cap . '_quizzes', $role_caps ) ? esc_attr( 'checked' ) : ''; ?>></td>
				</tr>	
				<?php endforeach; ?>
			</tbody>
		</table>
		<h3><?php esc_html_e( 'Frontend Capability', 'skilltriks' ); ?></h3>
		<table class="wp-list-table widefat fixed striped table-view-list capability">
			<thead>
				<tr>
					<th scope="col" id="type" class="manage-column column-type column-primary "><?php esc_html_e( 'Access Right', 'skilltriks' ); ?></th>
					<th scope="col" id="assign-course" class="manage-column column-assign-course"></th>
				</tr>
			</thead>
			<tbody id="the-list" data-wp-lists="list:capability">
				<tr>
					<td class="title column has-row-actions column-primary" data-colname="Access Right"><?php esc_html_e( 'Assign Course', 'skilltriks' ); ?><div class="tooltip dashicons dashicons-info"><span class="tooltip-text"><?php esc_html_e( 'Allows the user to assign course to others', 'skilltriks' ); ?></span></div>
						<button type="button" class="toggle-row">
							<span class="screen-reader-text">Show more details</span>
						</button>
					</td>
					<td class="type column-type" data-colname="Assign-course"><input name="users_can[]" type="checkbox" id="users_can_assign_course" value='assign_course' <?php echo ! empty( $role_caps ) && array_key_exists( 'assign_course', $role_caps ) ? esc_attr( 'checked' ) : ''; ?>></td>
				</tr>
			</tbody>
		</table>
		<input type="submit" style="margin-top: 1em;" class="button button-primary" name="submit" value="<?php esc_html_e( 'Submit', 'skilltriks' ); ?>" />
	</form>
</div>
