<?php
/**
 * Plugin Name:     BlueDolphin LMS
 * Plugin URI:      https://getbluedolphin.com
 * Description:     A Comprehensive Solution For Training Management. Contact Us For More Details On Training Management System.
 * Author:          KrishaWeb
 * Author URI:      https://getbluedolphin.com
 * Text Domain:     bluedolphin-lms
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * @package         BlueDolphin\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vendor_file = __DIR__ . '/vendor/autoload.php';
if ( is_readable( $vendor_file ) ) {
	require_once $vendor_file;
}

define( 'BDLMS_BASEFILE', __FILE__ );
define( 'BDLMS_VERSION', '1.0.0' );
define( 'BDLMS_ABSURL', plugins_url( '/', BDLMS_BASEFILE ) );
define( 'BDLMS_BASENAME', plugin_basename( BDLMS_BASEFILE ) );
define( 'BDLMS_ABSPATH', dirname( BDLMS_BASEFILE ) );
define( 'BDLMS_DIRNAME', basename( BDLMS_ABSPATH ) );

/**
 * Plugin textdomain.
 */
function bdlms_textdomain() {
	load_plugin_textdomain( 'bluedolphin-lms', false, basename( __DIR__ ) . '/languages' );
}
add_action( 'plugins_loaded', 'bdlms_textdomain' );

/**
 * Plugin activation.
 */
function bdlms_activation() {
	\BlueDolphin\Lms\Helpers\Utility::activation_hook();
}
register_activation_hook( BDLMS_BASEFILE, 'bdlms_activation' );

/**
 * Plugin deactivation.
 */
function bdlms_deactivation() {
	\BlueDolphin\Lms\Helpers\Utility::deactivation_hook();
}
register_deactivation_hook( BDLMS_BASEFILE, 'bdlms_deactivation' );

/**
 * Initialization class.
 */
function bdlms_init() {
	$bdlms = bdlms_run();
	if ( is_callable( array( $bdlms, 'init' ) ) ) {
		$bdlms->init();
	}
}
add_action( 'plugins_loaded', 'bdlms_init' );

/**
 * Init.
 */
function bdlms_run() {
	if ( ! class_exists( '\BlueDolphin\Lms\BlueDolphin' ) ) {
		return null;
	}
	return BlueDolphin\Lms\BlueDolphin::instance();
}
