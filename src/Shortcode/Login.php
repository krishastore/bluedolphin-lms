<?php
/**
 * The file that defines the login shortcode functionality.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BD\Lms\Shortcode
 */

namespace BD\Lms\Shortcode;

use BD\Lms\ErrorLog as EL;
use BD\Lms\Login\GoogleLogin as GL;

/**
 * Shortcode register manage class.
 */
class Login extends \BD\Lms\Shortcode\Register implements \BD\Lms\Interfaces\Login {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->set_shortcode_tag( 'login' );
		add_action( 'wp_ajax_bdlms_login', array( $this, 'login_process' ) );
		add_action( 'wp_ajax_nopriv_bdlms_login', array( $this, 'login_process' ) );
		add_action( 'wp_logout', array( $this, 'redirect_after_logout' ) );
		add_action( 'template_redirect', array( GL::instance(), 'google_sso_verify' ) );
		add_filter( 'show_admin_bar', array( $this, 'show_admin_bar' ) );
		add_filter( 'logout_url', array( $this, 'logout_url' ) );
		$this->init();
	}

	/**
	 * Register shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 */
	public function register_shortcode( $atts ) {
		wp_enqueue_script( $this->handler );
		wp_enqueue_style( $this->handler );
		ob_start();
		load_template( \BD\Lms\locate_template( 'login.php' ), false, array() );
		$content = ob_get_clean();
		return $content;
	}

	/**
	 * Login process.
	 */
	public function login_process() {
		check_ajax_referer( \BD\Lms\BDLMS_LOGIN_NONCE, '_bdlms_nonce' );
		$username = isset( $_POST['username'] ) ? sanitize_text_field( wp_unslash( $_POST['username'] ) ) : '';
		$password = isset( $_POST['password'] ) ? sanitize_text_field( wp_unslash( $_POST['password'] ) ) : '';

		$credential                  = array();
		$credential['user_login']    = $username;
		$credential['user_password'] = $password;
		if ( isset( $_POST['remember'] ) && 'on' === $_POST['remember'] ) {
			$credential['remember'] = true;
		}
		$user_verify = wp_signon( $credential, false );
		if ( is_wp_error( $user_verify ) ) {
			$response = array(
				'status'  => 0,
				'message' => $user_verify->get_error_message(),
			);
			EL::add( 'User singon error: ' . $user_verify->get_error_message(), 'error', __FILE__, __LINE__ );
			wp_send_json( $response );
		}
		if ( ! in_array( 'bdlms', $user_verify->roles, true ) ) {
			wp_logout();
			$response = array(
				'status'  => 0,
				'message' => __( 'Your account role is different, please contact to administration', 'bluedolphin-lms' ),
			);
			EL::add( $response['message'], 'error', __FILE__, __LINE__ );
			wp_send_json( $response );
		}
		wp_set_current_user( $user_verify->ID, $user_verify->user_login );
		wp_set_auth_cookie( $user_verify->ID );
		$response = array(
			'status'   => 1,
			'redirect' => \BD\Lms\get_page_url( 'courses' ),
		);
		EL::add( sprintf( 'User Logged, User ID: %d', $user_verify->ID ), 'info', __FILE__, __LINE__ );
		wp_send_json( $response );
	}

	/**
	 * Show admin bar.
	 *
	 * @param bool $show Show admin bar.
	 * @return bool
	 */
	public function show_admin_bar( $show ) {
		if ( \BD\Lms\is_lms_user() ) {
			return $show;
		}
		return $show;
	}

	/**
	 * Filters the logout URL.
	 *
	 * @since 1.0.0
	 *
	 * @param string $logout_url The HTML-encoded logout URL.
	 * @return string
	 */
	public function logout_url( $logout_url ) {
		if ( \BD\Lms\is_lms_user() ) {
			$logout_url = add_query_arg( 'is_bdlms_user', 1, $logout_url );
		}
		return $logout_url;
	}

	/**
	 * Filters the logout URL.
	 */
	public function redirect_after_logout() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! empty( $_GET['is_bdlms_user'] ) ) {
			wp_safe_redirect( \BD\Lms\get_page_url( 'login' ) );
			exit;
		}
	}
}
