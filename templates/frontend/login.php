<?php
/**
 * Template: Login
 *
 * @package BD\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$error_code = ! empty( $_GET['message'] ) ? (int) $_GET['message'] : 0;
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$email   = ! empty( $_GET['email'] ) && is_email( wp_unslash( $_GET['email'] ) ) ? sanitize_email( wp_unslash( $_GET['email'] ) ) : '';
$message = '';
if ( 1 === $error_code ) {
	$message = __( 'something went wrong, Please try again', 'bluedolphin-lms' );
} elseif ( 2 === $error_code ) {
	$message = __( 'Your account role is different, please contact to administration.', 'bluedolphin-lms' );
} elseif ( 3 === $error_code ) {
	// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
	$message = sprintf( __( 'User %s not registered in system.', 'bluedolphin-lms' ), $email );
}
?>
<div class="bdlms-wrap alignfull">
	<div class="bdlms-login-wrap">
		<div class="bdlms-login">
			<div class="bdlms-login__header">
				<div class="bdlms-login__title"><?php esc_html_e( 'Login to BlueDolphin', 'bluedolphin-lms' ); ?></div>
				<div class="bdlms-login__text"><?php esc_html_e( 'Hey, Welcome back!', 'bluedolphin-lms' ); ?><br> <?php esc_html_e( 'Please sign in to grow yourself', 'bluedolphin-lms' ); ?></div>
			</div>
			<div class="bdlms-login__body">
				<?php if ( is_admin() || ! is_user_logged_in() ) : ?>
					<form action="" method="post">
						<?php wp_nonce_field( \BD\Lms\BDLMS_LOGIN_NONCE, '_bdlms_nonce' ); ?>
						<input type="hidden" name="action" value="bdlms_login">
						<div class="bdlms-form-group">
							<label class="bdlms-form-label"><?php esc_html_e( 'Username', 'bluedolphin-lms' ); ?></label>
							<input type="text" name="username" class="bdlms-form-control" placeholder="<?php esc_attr_e( 'Username', 'bluedolphin-lms' ); ?>" required>
						</div>
						<div class="bdlms-form-group">
							<label class="bdlms-form-label"><?php esc_html_e( 'Password', 'bluedolphin-lms' ); ?></label>
							<div class="bdlms-password-field">
								<input type="password" name="password" class="bdlms-form-control" placeholder="********" id="password-field" required>
								<div class="bdlms-password-toggle" toggle="#password-field">
									<svg width="16" height="16" class="eye-on">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS . '/images/sprite-front.svg#eye' ); ?>"></use>
									</svg>
									<svg width="16" height="16" class="eye-off">
										<use xlink:href="<?php echo esc_url( BDLMS_ASSETS . '/images/sprite-front.svg#eye-crossed' ); ?>"></use>
									</svg>
								</div>
							</div>
						</div>
						<div class="bdlms-keep-login bdlms-form-group">
							<div class="bdlms-check-wrap">
								<input type="checkbox" name="remember" class="bdlms-check" id="remember">
								<label for="remember" class="bdlms-check-label text-sm"><?php esc_html_e( 'Keep me logged In', 'bluedolphin-lms' ); ?></label>
							</div>
							<div class="bdlms-forgot-link">
								<a href="<?php echo esc_url( wp_lostpassword_url( \BD\Lms\get_page_url( 'login' ) ) ); ?>" target="_blank"><?php esc_html_e( 'Forgot Password?', 'bluedolphin-lms' ); ?></a>
							</div>
						</div>
						<div class="bdlms-error-message<?php echo empty( $message ) ? ' hidden' : ''; ?>">
							<span class="bdlms-form-error"><?php echo esc_html( $message ); ?></span>
						</div>
						<?php
						$auth_url = \BD\Lms\Login\GoogleLogin::instance()->get_auth_url();
						?>
							<div class="bdlms-form-footer">
								<button type="submit" class="bdlms-btn bdlms-btn-block"><?php esc_html_e( 'Sign In', 'bluedolphin-lms' ); ?><span class="bdlms-loader"></span></button>
								<?php if ( $auth_url ) : ?>
								<span class="or-txt">OR</span>
									<a class='bdlms-btn google-sign-in-btn' href="<?php echo esc_url( $auth_url ); ?>">
										<svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M3.54594 10.1694L2.989 12.2485L0.953406 12.2916C0.345063 11.1633 0 9.87229 0 8.50041C0 7.17382 0.322625 5.92282 0.8945 4.82129H0.894937L2.70719 5.15354L3.50106 6.95491C3.33491 7.43932 3.24434 7.95932 3.24434 8.50041C3.24441 9.08766 3.35078 9.65032 3.54594 10.1694Z" fill="#FBBB00"/>
											<path d="M15.8601 7.00586C15.952 7.4898 15.9999 7.98958 15.9999 8.50036C15.9999 9.07311 15.9397 9.6318 15.825 10.1707C15.4355 12.0045 14.4179 13.6059 13.0083 14.739L13.0078 14.7386L10.7252 14.6221L10.4022 12.6054C11.3375 12.0569 12.0685 11.1984 12.4536 10.1707H8.17578V7.00586H15.8601Z" fill="#518EF8"/>
											<path d="M13.0079 14.7382L13.0083 14.7386C11.6373 15.8406 9.89577 16.4999 7.99996 16.4999C4.95337 16.4999 2.30459 14.7971 0.953369 12.2911L3.5459 10.1689C4.22149 11.972 5.96084 13.2555 7.99996 13.2555C8.87643 13.2555 9.69756 13.0186 10.4022 12.605L13.0079 14.7382Z" fill="#28B446"/>
											<path d="M13.1064 2.34175L10.5148 4.4635C9.78553 4.00769 8.92353 3.74437 8.00003 3.74437C5.91475 3.74437 4.14288 5.08678 3.50113 6.9545L0.894969 4.82087H0.894531C2.22597 2.25384 4.90816 0.5 8.00003 0.5C9.94112 0.5 11.7209 1.19144 13.1064 2.34175Z" fill="#F14336"/>
										</svg>
										<?php esc_html_e( 'Sign In with Google', 'bluedolphin-lms' ); ?>
									</a>
								<?php endif; ?>
							</div>
					</form>
					<?php
				else :
					wp_safe_redirect( \BD\Lms\get_page_url( 'courses' ) );
					exit;
				endif;
				?>
			</div>
		</div>
	</div>
</div>
