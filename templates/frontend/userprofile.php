<?php
/**
 * Template: Userprofile shortcode.
 *
 * @package ST\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_user_logged_in() ) :
	$user_info      = get_userdata( get_current_user_id() );
	$avatar_url     = get_user_meta( get_current_user_id(), 'avatar_url', true );
	$topics         = get_user_meta( get_current_user_id(), '_stlms_user_topics', true ) ? get_user_meta( get_current_user_id(), '_stlms_user_topics', true ) : array();
	$default_avatar = esc_url( STLMS_ASSETS ) . '/images/profile-pic.png';
	?>
	<div class="stlms-wrap alignfull">
		<?php require_once STLMS_TEMPLATEPATH . '/frontend/sub-header.php'; ?>
		<div class="stlms-course-list-wrap">
			<div class="stlms-container">
				<div class="stlms-course-view">
					<div class="stlms-course-view__header">
						<div class="stlms-filtered-item">
							<?php esc_html_e( 'Account Settings', 'skilltriks' ); ?>
						</div>
					</div>
					<div class="stlms-course-view__body">
						<div class="stlms-profile-wrapper">
							<form>
								<div class="stlms-form-box">
									<div class="stlms-profile-row">
										<div class="stlms-profile-col">
											<div class="stlms-profile-box">
												<div class="stlms-profile-box__title">
													<?php esc_html_e( 'Account Details', 'skilltriks' ); ?>
												</div>
												<div class="stlms-profile-form">
													<div class="stlms-profile-form__row">
														<div class="stlms-profile-form__col">
															<div class="stlms-form-group">
																<label class="stlms-form-label" for="first-name"><?php esc_html_e( 'First Name', 'skilltriks' ); ?></label>
																<input class="stlms-form-control" type="text" id="first-name" value="<?php echo esc_html( $user_info->first_name ); ?>" placeholder="First Name">
															</div>
														</div>
														<div class="stlms-profile-form__col">
															<div class="stlms-form-group">
																<label class="stlms-form-label" for="last-name"><?php esc_html_e( 'Last Name', 'skilltriks' ); ?></label>
																<input class="stlms-form-control" type="text" id="last-name" value="<?php echo esc_html( $user_info->last_name ); ?>" placeholder="Last Name">
															</div>
														</div>
													</div>
													<div class="stlms-profile-form__row">
														<div class="stlms-profile-form__col">
															<div class="stlms-form-group">
																<label class="stlms-form-label" for="username"><?php esc_html_e( 'Username', 'skilltriks' ); ?></label>
																<input class="stlms-form-control not-allowed" type="text" id="username" value="<?php echo esc_html( $user_info->user_login ); ?>" readonly>
															</div>
														</div>
														<div class="stlms-profile-form__col">
															<div class="stlms-form-group">
																<label class="stlms-form-label" for="email"><?php esc_html_e( 'Email', 'skilltriks' ); ?></label>
																<input class="stlms-form-control not-allowed" type="text" id="email" value="<?php echo esc_html( $user_info->user_email ); ?>" readonly>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<?php if ( ! current_user_can( 'manage_options' ) ) : ?>
										<div class="stlms-profile-col">
											<div class="stlms-profile-box">
												<div class="stlms-profile-box__title">
													<?php esc_html_e( 'Change Password', 'skilltriks' ); ?>
												</div>
												<div class="stlms-profile-form">
													<div class="stlms-pswd-wrap">
														<div class="stlms-form-group">
															<button type="button" class="button stlms-btn wp-generate-pw hide-if-no-js" aria-expanded="false">
																<?php esc_html_e( 'Set New Password', 'skilltriks' ); ?>
															</button>
															<div class="stlms-password-field wp-pwd hide-if-js" style="display: none;">
																<div class="password-input-wrapper">
																	<input type="text" name="pass1" id="pass1" class="stlms-form-control" value=""
																		autocomplete="new-password" spellcheck="false" aria-describedby="pass-strength-result"
																		data-pw="<?php echo wp_kses_post( wp_generate_password() ); ?>" disabled="" />
																	<div id="pass-strength-result" class="stlms-pass-strength"></div>
																</div>
																<div class="stlms-pswd-btn-wrap">
																	<button type="button" class="button stlms-btn wp-hide-pw hide-if-no-js" data-toggle="0" aria-label="Hide password">
																		<svg width="16" height="16" class="eye-on" style="display:none;">
																			<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#eye"></use>
																		</svg>
																		<svg width="16" height="16" class="eye-off">
																			<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#eye-crossed"></use>
																		</svg>
																		<span class="text"><?php esc_html_e( 'Hide', 'skilltriks' ); ?></span>
																	</button>
																	<button type="button" class="button stlms-btn stlms-btn-light wp-cancel-pw hide-if-no-js"
																		aria-label="Cancel password change">
																		<span class="text"><?php esc_html_e( 'Cancel', 'skilltriks' ); ?></span>
																	</button>
																</div>
															</div>
														</div>	
														<div class="pw-weak" style="display: none;">
															<div class="stlms-form-group">
																<label>
																	<input type="checkbox" name="pw_weak" class="pw-checkbox stlms-check" />
																	<span><?php esc_html_e( 'Confirm use of weak password', 'skilltriks' ); ?></span>
																</label>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<?php endif; ?>
									</div>
									<div class="stlms-profile-row is-two-column">
										<?php if ( current_user_can( 'upload_files' ) ) : ?>
										<div class="stlms-profile-col">
											<div class="stlms-profile-box">
												<div class="stlms-profile-box__title">
													<?php esc_html_e( 'Profile Picture', 'skilltriks' ); ?>
												</div>
												<div class="stlms-profile-picture">
													<div class="stlms-profile-image">
														<img id="preview" 
															src="<?php echo $avatar_url ? esc_url( $avatar_url ) : esc_url( $default_avatar ); ?>" 
															alt="<?php esc_attr_e( 'Profile Photo', 'skilltriks' ); ?>" width="220" height="220">
													</div>
													<input type="file" id="fileInput" accept="image/png, image/jpeg, image/jpg" style="display:none">
													<div class="stlms-profile-action">
														<button type="button" class="stlms-btn" id="uploadBtn"><?php esc_html_e( 'Upload Photo', 'skilltriks' ); ?></button>
														<button type="button" class="stlms-btn stlms-btn-light" style="display:<?php echo $avatar_url ? 'block' : 'none'; ?>" id="deleteBtn"><?php esc_html_e( 'Delete Photo', 'skilltriks' ); ?></button>
														<div class="stlms-profile-action__text">
															<?php esc_html_e( 'Only JPG, JPEG, and PNG files are supported. Maximum file size: 2 MB. For best results, upload a square image of at least 250×250 pixels.', 'skilltriks' ); ?>
														</div>
													</div>
												</div>
											</div>
										</div>
										<?php endif; ?>
										<div class="stlms-profile-col">
											<div class="stlms-profile-box">
												<div class="stlms-profile-box__title">
													<?php esc_html_e( 'Preferred Topics', 'skilltriks' ); ?>
												</div>
												<div class="stlms-profile-form">
													<div class="stlms-profile-form__group">
														<label for="select-topics" class="stlms-select-search">
															<?php esc_html_e( 'Select Topics', 'skilltriks' ); ?>
															<select id="select-topics"
																multiple data-placeholder="Please choose" class="stlms-select2-multi js-states form-control" id="id_label_single" style="width: 100%;">
																<?php
																$terms_list  = \ST\Lms\course_taxonomies( \ST\Lms\STLMS_COURSE_CATEGORY_TAX );
																$course_page = \ST\Lms\get_page_url( 'courses' );
																?>
																<?php foreach ( $terms_list as $key => $course_term ) : ?>
																<option value="<?php echo absint( $course_term['id'] ); ?>" <?php echo in_array( $course_term['id'], $topics, true ) ? 'selected' : ''; ?>><?php echo esc_html( $course_term['name'] ); ?></option>
																<?php endforeach; ?>
															</select>
														</label>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="stlms-profile-submit">
									<input type="submit" value="Save Profile" class="stlms-btn save-profile">
								</div>

								<div id="snackbar-success" class="stlms-snackbar">
									<svg width="30" height="30">
										<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#tick"></use>
									</svg>
									<?php esc_html_e( 'Profile updated successfully!', 'skilltriks' ); ?>
									<button class="hideSnackbar">
										<svg width="20" height="20">
											<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#cross"></use>
										</svg>
									</button>
								</div>

								<div id="snackbar-error" class="stlms-snackbar error-snackbar">
									<svg width="30" height="30">
										<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#cross-error"></use>
									</svg>
									<span class="snackbar-message"><?php esc_html_e( 'Oops, something went wrong, please try again later.', 'skilltriks' ); ?></span>
									<button class="hideSnackbar">
										<svg width="20" height="20">
											<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#cross"></use>
										</svg>
									</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
endif;
