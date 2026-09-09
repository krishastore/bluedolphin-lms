<?php
/**
 * Declare the abstract Notification class.
 *
 * @link       https://www.skilltriks.com/
 * @since      1.0.0
 *
 * @package    ST\Lms
 */

namespace ST\Lms\Helpers;

use ST\Lms\ErrorLog as EL;
use const ST\Lms\STLMS_NOTIFICATION_TABLE;
use const ST\Lms\STLMS_COURSE_COMPLETED_ON;

/**
 * Main Notification class.
 */
abstract class Notification {

	/**
	 * Email Subject.
	 *
	 * @var string $subject
	 */
	public $subject;

	/**
	 * Email message.
	 *
	 * @var string $message
	 */
	public $message;

	/**
	 * Send Email or not.
	 *
	 * @var bool $should_send_email
	 */
	public $should_send_email;

	/**
	 * Get subject line for the email notification.
	 *
	 * @param string|null $course_name Course name related to the notification.
	 * @param bool        $is_assigner Optional course assigner.
	 *
	 * @return string
	 */
	abstract public function email_subject( $course_name, $is_assigner = false );

	/**
	 * Get body/content of the email notification.
	 *
	 * @param string      $from_user_name User who initiated the action.
	 * @param string      $to_user_name Recipient user.
	 * @param int         $course_id course ID.
	 * @param string|null $due_date Optional course due date.
	 * @param bool        $is_assigner Optional course assigner.
	 *
	 * @return string
	 */
	abstract public function email_message( $from_user_name, $to_user_name, $course_id, $due_date, $is_assigner = false );

	/**
	 * Return true if this notification should also send an email.
	 *
	 * @return bool
	 */
	abstract public function should_send_email_notification();

	/**
	 * Render an email template with data.
	 *
	 * @param string $template_name name of the template.
	 * @param array  $args          Data to pass to the template.
	 *
	 * @return string Rendered HTML message.
	 */
	protected function render_email_template( $template_name, $args = array() ) {
		ob_start();

		$template_full_path = STLMS_TEMPLATEPATH . '/email/' . $template_name . '.php';

		if ( ! file_exists( $template_full_path ) ) {
			return '';
		}
		$args = $args; // Resolve ( The method parameter $args is never used ) phpcs warning.
		require_once $template_full_path;

		return ob_get_clean();
	}

	/**
	 * Send email notification logic.
	 *
	 * Handles the process of preparing and sending an email notification
	 * related to course assignments, due dates, completions, and other actions.
	 * This method is designed to be flexible for both the assigner and assignee.
	 *
	 * @param int         $from_user_id  ID of the user who initiated the action (typically the assigner).
	 * @param int         $to_user_id    ID of the recipient user (assignee or assigner based on context).
	 * @param int         $course_id     ID of the course related to the notification.
	 * @param string|null $due_date      Optional. Due date of the course in 'Y-m-d' format. Default null.
	 * @param bool        $is_assigner   Optional. Whether the recipient is the assigner. Default false.
	 * @param int         $action_type   Type of action triggering the notification:
	 *                                   1 = Assigned,
	 *                                   2 = Updated,
	 *                                   3 = Deleted,
	 *                                   4 = Due Today,
	 *                                   5 = Due Soon,
	 *                                   6 = Overdue,
	 *                                   7 = Completed.
	 *
	 * @return void
	 */
	public function send_email_notification( $from_user_id, $to_user_id, $course_id, $due_date = null, $is_assigner = false, $action_type = 0 ) {
		global $wpdb;

		if ( ! $this->should_send_email_notification() ) {
			return;
		}

		$from_user           = get_userdata( $from_user_id );
		$from_user_name      = $from_user->display_name;
		$to_user             = get_userdata( $to_user_id );
		$to_user_name        = $to_user->display_name;
		$course              = get_post( $course_id );
		$course_name         = ! empty( $course ) ? $course->post_title : '';
		$subject             = $this->email_subject( $course_name, $is_assigner );
		$message             = $this->email_message( $from_user_name, $to_user_name, $course_id, $due_date, $is_assigner );
		$notifications_table = $wpdb->prefix . STLMS_NOTIFICATION_TABLE;
		$result              = 1;
		$completed_on        = 0;
		$_date               = empty( $due_date ) ? '0000-00-00' : $due_date;

		if ( in_array( $action_type, array( 4, 5, 6 ), true ) ) {
			$course_completed_key = sprintf( \ST\Lms\STLMS_COURSE_COMPLETED_ON, $course_id );
			$completed_on         = get_user_meta( $to_user_id, $course_completed_key, true );
			EL::add(
				sprintf(
					'Course progress for user ID %d, completed status %s.',
					$to_user_id,
					$completed_on
				),
				'info',
				__FILE__,
				__LINE__
			);
		}

		$sent_notification = $wpdb->get_var( $wpdb->prepare( // phpcs:ignore.
			// phpcs:ignore.
			"SELECT `notification_sent` FROM $notifications_table WHERE action_type = %d AND to_user_id = %d AND from_user_id = %d AND course_id = %d AND due_date = %s",
			$action_type,
			(int) $to_user_id,
			(int) $from_user_id,
			(int) $course_id,
			$_date
		) ); // phpcs:ignore.

		if ( ! $sent_notification && ! $completed_on ) {
			if ( $action_type ) {
				$result = $wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$notifications_table,
					array(
						'from_user_id'      => $from_user_id,
						'to_user_id'        => $to_user_id,
						'course_id'         => $course_id,
						'due_date'          => $due_date,
						'action_type'       => $action_type,
						'is_read'           => 0,
						'notification_sent' => 1,
					),
					array( '%d', '%d', '%d', '%s', '%d', '%d', '%d' )
				);
				delete_transient( 'stlms_notification_data_' . $to_user_id );
			}

			if ( ! $result || is_wp_error( $result ) ) {
				EL::add(
					sprintf(
						'DB insert failed for user ID %d, course ID %d. Error: %s',
						$to_user_id,
						$course_id,
						$wpdb->last_error
					),
					'error',
					__FILE__,
					__LINE__
				);
			} else {
				wp_mail(
					$to_user->user_email,
					wp_strip_all_tags( $subject ),
					$message,
					array( 'Content-Type: text/html; charset=UTF-8' )
				);
			}
		}
	}
}
