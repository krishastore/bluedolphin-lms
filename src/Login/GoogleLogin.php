<?php
/**
 * The file is handle the google sso login.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BD\Lms
 *
 * phpcs:disable WordPress.NamingConventions.ValidHookName.UseUnderscores
 */

namespace BD\Lms\Login;

use BD\Lms\Helpers\SettingOptions as Options;

/**
 * Register post types.
 */
class GoogleLogin {

	/**
	 * The main instance var.
	 *
	 * @var GoogleLogin|null The one GoogleLogin instance.
	 * @since 1.0.0
	 */
	private static $instance = null;

	/**
	 * Init the main singleton instance class.
	 *
	 * @return GoogleLogin Return the instance class
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new GoogleLogin();
		}
		return self::$instance;
	}

	/**
	 * Get google client object.
	 *
	 * @return object|null
	 */
	public function get_google_client() {
		$client_id     = Options::instance()->get_option( 'client_id' );
		$client_secret = Options::instance()->get_option( 'client_secret' );
		$redirect_uri  = Options::instance()->get_option( 'redirect_uri' );

		if ( empty( $client_id ) || empty( $client_secret ) || empty( $redirect_uri ) ) {
			return null;
		}

		$client = new \Google_Client();
		$client->setClientId( $client_id );
		$client->setClientSecret( $client_secret );
		$client->setRedirectUri( $redirect_uri );
		$client->addScope( 'email' );
		$client->addScope( 'profile' );
		return $client;
	}

	/**
	 * Get google auth URL.
	 *
	 * @return string|false
	 */
	public function get_auth_url() {
		if ( ! method_exists( $this->get_google_client(), 'createAuthUrl' ) ) {
			return false;
		}
		return $this->get_google_client()->createAuthUrl();
	}

	/**
	 * Verify google sso login.
	 */
	public function google_sso_verify() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$code = isset( $_GET['code'] ) ? sanitize_text_field( wp_unslash( $_GET['code'] ) ) : false;
		if ( $code ) {
			$client = $this->get_google_client();
			$token  = $client->fetchAccessTokenWithAuthCode( $code );

			if ( empty( $token['access_token'] ) ) {
				wp_safe_redirect(
					add_query_arg(
						array(
							'email'   => '',
							'message' => 1,
						),
						\BD\Lms\get_page_url( 'login' )
					)
				);
				exit;
			}

			$client->setAccessToken( $token['access_token'] );
			// get profile info.
			$google_oauth        = new \Google_Service_Oauth2( $client );
			$google_account_info = $google_oauth->userinfo->get();
			$email               = $google_account_info->email;

			if ( is_email( $email ) ) {
				$userinfo = get_user_by( 'email', $email );
				if ( $userinfo ) {
					if ( ! in_array( 'bdlms', $userinfo->roles, true ) ) {
						wp_safe_redirect(
							add_query_arg(
								array(
									'email'   => $email,
									'message' => 2,
								),
								\BD\Lms\get_page_url( 'login' )
							)
						);
						exit;
					}
					wp_set_current_user( $userinfo->ID, $userinfo->user_login );
					wp_set_auth_cookie( $userinfo->ID );
					wp_safe_redirect( \BD\Lms\get_page_url( 'courses' ) );
					exit;
				}
				wp_safe_redirect(
					add_query_arg(
						array(
							'email'   => $email,
							'message' => 3,
						),
						\BD\Lms\get_page_url( 'login' )
					)
				);
			}
		}
	}
}
