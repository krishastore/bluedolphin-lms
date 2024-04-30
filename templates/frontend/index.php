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
				<div class="bdlms-login__title">
					Login to
					BlueDolphin</div>
				<div class="bdlms-login__text">Hey, Welcome back!<br> Please sign in to grow yourself
				</div>
			</div>
			<div class="bdlms-login__body">
				<form action="">
					<div class="bdlms-form-group">
						<label class="bdlms-form-label">Username</label>
						<input type="text" class="bdlms-form-control" placeholder="John@company.com">
						<span class="bdlms-form-error">This field is required.</span>
					</div>
					<div class="bdlms-form-group">
						<label class="bdlms-form-label">Password</label>
						<div class="bdlms-password-field">
							<input type="password" class="bdlms-form-control" placeholder="********"
								id="password-field">
							<div class="bdlms-password-toggle" toggle="#password-field">
								<svg width="16" height="16" class="eye-on">
									<use xlink:href="assets/images/sprite.svg#eye"></use>
								</svg>
								<svg width="16" height="16" class="eye-off">
									<use xlink:href="assets/images/sprite.svg#eye-crossed"></use>
								</svg>
							</div>
						</div>
					</div>
					<div class="bdlms-keep-login bdlms-form-group">
						<div class="bdlms-check-wrap">
							<input type="checkbox" class="bdlms-check" id="keep">
							<label for="keep" class="bdlms-check-label text-sm">Keep me logged In</label>
						</div>
						<div class="bdlms-forgot-link">
							<a href="#">Forgot Password?</a>
						</div>
					</div>
					<div class="bdlms-form-footer">
						<button type="submit" class="bdlms-btn bdlms-btn-block">Sign In</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
