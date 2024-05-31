<?php
/**
 * Template: Login
 *
 * @package BlueDolphin\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
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
						<?php wp_nonce_field( \BlueDolphin\Lms\BDLMS_LOGIN_NONCE, '_bdlms_nonce' ); ?>
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
								<a href="<?php echo esc_url( wp_lostpassword_url( \BlueDolphin\Lms\get_page_url( 'login' ) ) ); ?>" target="_blank"><?php esc_html_e( 'Forgot Password?', 'bluedolphin-lms' ); ?></a>
							</div>
						</div>
						<div class="bdlms-error-message hidden">
							<span class="bdlms-form-error"></span>
						</div>
						<div class="bdlms-form-footer">
							<button type="submit" class="bdlms-btn bdlms-btn-block"><?php esc_html_e( 'Sign In', 'bluedolphin-lms' ); ?><span class="bdlms-loader"></span></button>
						</div>
					</form>
					<?php
				else :
					wp_safe_redirect( \BlueDolphin\Lms\get_page_url( 'courses' ) );
					exit;
				endif;
				?>
			</div>
		</div>
	</div>
</div>
