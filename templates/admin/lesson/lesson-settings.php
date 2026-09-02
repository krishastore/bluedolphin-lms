<?php
/**
 * Template: Lesson Settings Metabox.
 *
 * @package ST\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$duration      = $settings['duration'];
$duration_type = $settings['duration_type'];
?>

<div class="stlms-lesson-duration">
	<label><?php esc_html_e( 'Duration', 'skilltriks' ); ?></label>
	<div class="stlms-duration-input">
		<input type="number" value="<?php echo (int) $duration; ?>" step="1" min="0" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[settings][duration]">
		<select name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[settings][duration_type]">
			<option value="minute"<?php selected( 'minute', $duration_type ); ?>><?php esc_html_e( 'Minute(s)', 'skilltriks' ); ?></option>
			<option value="hour"<?php selected( 'hour', $duration_type ); ?>><?php esc_html_e( 'Hour(s)', 'skilltriks' ); ?></option>
			<option value="day"<?php selected( 'day', $duration_type ); ?>><?php esc_html_e( 'Day(s)', 'skilltriks' ); ?></option>
			<option value="week"<?php selected( 'week', $duration_type ); ?>><?php esc_html_e( 'Week(s)', 'skilltriks' ); ?></option>
		</select>
	</div>
</div>
