<?php
/**
 * Template: Userinfo shortcode.
 *
 * @package BlueDolphin\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_user_logged_in() ) :
	$userinfo   = wp_get_current_user();
	$logout_url = wp_logout_url( \BlueDolphin\Lms\get_page_url( 'login' ) );
	?>
	<div class="bdlms-user">
		<div class="bdlms-user-photo">
			<div class="bdlms-photo">
				<?php echo get_avatar( $userinfo->ID ); ?>
			</div>
		</div>
		<div class="bdlms-user-info">
			<span class="bdlms-user-name"><?php echo esc_html( $userinfo->display_name ); ?></span>
			<div class="bdlms-user-dd">
				<div class="bdlms-user-dd__toggle">
					<?php esc_html_e( 'My Account', 'bluedolphin-lms' ); ?>
					<svg width="24" height="24">
						<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#arrow-down"></use>
					</svg>
				</div>
				<div class="bdlms-user-dd__menu">
					<a href="<?php echo esc_url( \BlueDolphin\Lms\get_page_url( 'my_learning' ) ); ?>" class="bdlms-user-dd__link"><?php esc_html_e( 'My Learnings', 'bluedolphin-lms' ); ?></a>
					<a href="<?php echo esc_url( $logout_url ); ?>" class="bdlms-user-dd__link"><?php esc_html_e( 'Logout', 'bluedolphin-lms' ); ?></a>
				</div>
			</div>
		</div>
	</div>
<?php else : ?>
	<a href="<?php echo esc_url( \BlueDolphin\Lms\get_page_url( 'login' ) ); ?>" class="bdlms-btn bdlms-btn-block"><?php esc_html_e( 'Login', 'bluedolphin-lms' ); ?></a>
<?php endif; ?>