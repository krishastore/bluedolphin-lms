<?php
/**
 * Template: Login.
 *
 * @package BlueDolphin\Lms
 */

?>
<div class="bdlms-wrap">
	<div class="bdlms-login-wrap">
		<div class="bdlms-login">
			<div class="bdlms-login__header">
				<div class="bdlms-login__title"><?php esc_html_e( 'Login to BlueDolphin', 'bluedolphin-lms' ); ?></div>
				<div class="bdlms-login__text"><?php esc_html_e( 'Hey, Welcome back!', 'bluedolphin-lms' ); ?><br> <?php esc_html_e( 'Please sign in to grow yourself', 'bluedolphin-lms' ); ?></div>
			</div>
			<div class="bdlms-login__body">
				<form action="" method="post">
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
							<a href="#"><?php esc_html_e( 'Forgot Password?', 'bluedolphin-lms' ); ?></a>
						</div>
					</div>
					<div class="bdlms-form-footer">
						<button type="submit" class="bdlms-btn bdlms-btn-block"><?php esc_html_e( 'Sign In', 'bluedolphin-lms' ); ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
