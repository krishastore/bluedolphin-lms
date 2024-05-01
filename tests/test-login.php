<?php
/**
 * Class LoginTest
 *
 * @package BlueDolphin\Lms\Admin\MetaBoxes
 *
 * phpcs:disable WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput
 */

use const BlueDolphin\Lms\BDLMS_LOGIN_NONCE;

/**
 * Login test case.
 */
class LoginTest extends WP_Ajax_UnitTestCase {

	/**
	 * Test custom login.
	 */
	public function test_custom_login() {
		do_action( 'init' );
		\BlueDolphin\Lms\Helpers\Utility::activation_hook();

		$username              = 'phpunit';
		$password              = 'Test@1234';
		$email                 = 'phpunit@example.com';
		$_POST['_bdlms_nonce'] = wp_create_nonce( BDLMS_LOGIN_NONCE );
		$_POST['password']     = $password;
		$_POST['username']     = $username;
		$_POST['remember']     = 'on';

		$user_id = $this->factory->user->create(
			array(
				'role'       => 'bdlms',
				'user_pass'  => $password,
				'user_login' => $username,
				'user_email' => $email,
			)
		);
		$this->assertIsInt( $user_id );

		try {
			$this->_handleAjax( 'bdlms_login' );
		} catch ( WPAjaxDieContinueException $e ) { // phpcs:ignore
			// We expected this, do nothing.
		}

		// Check that the exception was thrown.
		$this->assertTrue( isset( $e ) );
		$response = json_decode( $this->_last_response );
		$this->assertIsObject( $response );
		$this->assertObjectHasAttribute( 'redirect', $response );
		$this->assertIsInt( $response->status );
		$this->assertNotEmpty( $response->status );
	}
}
