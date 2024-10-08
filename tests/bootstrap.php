<?php
/**
 * PHPUnit bootstrap file.
 *
 * @package Bluedolphin_Lms
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

// Forward custom PHPUnit Polyfills configuration to PHPUnit bootstrap file.
$_phpunit_polyfills_path = getenv( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH' );
if ( false !== $_phpunit_polyfills_path ) {
	define( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH', $_phpunit_polyfills_path );
}

if ( ! file_exists( "{$_tests_dir}/includes/functions.php" ) ) {
	echo "Could not find {$_tests_dir}/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	exit( 1 );
}
/**
 * The path to the main file of the plugin to test.
 */
define( 'WP_USE_THEMES', false );
define( 'WP_TESTS_FORCE_KNOWN_BUGS', true );
define( 'TI_UNIT_TESTING', true );

// Give access to tests_add_filter() function.
require_once "{$_tests_dir}/includes/functions.php";

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
	require dirname( __DIR__ ) . '/bluedolphin-lms.php';
	add_action( 'init', 'bdlms_activation' );
}

tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
require "{$_tests_dir}/includes/bootstrap.php";

activate_plugin( 'bluedolphin-lms/bluedolphin-lms.php' );
global $current_user;
$current_user = new WP_User( 1 ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
$current_user->set_role( 'administrator' );
wp_update_user(
	array(
		'ID'         => 1,
		'first_name' => 'Admin',
		'last_name'  => 'User',
	)
);
