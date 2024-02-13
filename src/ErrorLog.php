<?php
/**
 * The file that stores the error log.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BlueDolphin\Lms
 */

namespace BlueDolphin\Lms;

/**
 * Error log hendler.
 */
class ErrorLog {

	/**
	 * Store error log.
	 *
	 * @param string $msg Error message.
	 * @param string $type Error type.
	 * @param string $file File type.
	 * @param string $line Line number.
	 */
	public static function add( $msg = '', $type = 'error', $file = __FILE__, $line = __LINE__ ) {
		if ( defined( 'BDLMS_LOCAL_DEBUG' ) && BDLMS_LOCAL_DEBUG ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( sprintf( '%s (%s:%d): %s', $type, $file, $line, $msg ) );
		}
	}
}
