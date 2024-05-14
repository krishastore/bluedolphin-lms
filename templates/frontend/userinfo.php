<?php
/**
 * Template: Userinfo shortcode.
 *
 * @package BlueDolphin\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended
 */

if ( ! is_user_logged_in() ) {
	return;
}
$userinfo = wp_get_current_user();
?>
<div class="bdlms-user">
	<div class="bdlms-user-photo"><?php echo get_avatar( $userinfo->ID ); ?></div>
	<div class="bdlms-user-info">
		<span class="bdlms-user-name"><?php echo esc_html( $userinfo->display_name ); ?></span><span class="bdlms-user-email"><?php echo esc_html( $userinfo->user_email ); ?></span>
	</div>
</div>