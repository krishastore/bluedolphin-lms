<?php
/**
 * Template: Custom Header.
 *
 * @package ST\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="stlms-sub-header-wrap">
	<div class="stlms-sub-header">
		<div class="stlms-browse-wrap">
			<div class="stlms-browse-popup stlms-dd-wrap">
				<button class="stlms-dd-btn" data-dropdown="browseContent">
					<?php esc_html_e( 'Browse', 'skilltriks' ); ?>
					<svg width="16" height="16">
						<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#ChevronDown"></use>
					</svg>
				</button>
				<?php
				$terms_list     = \ST\Lms\course_taxonomies( \ST\Lms\STLMS_COURSE_CATEGORY_TAX );
				$course_page    = \ST\Lms\get_page_url( 'courses' );
				$search_keyword = '';
				?>
				<div class="stlms-dd-content" id="browseContent">
					<?php foreach ( $terms_list as $key => $course_term ) : ?>
						<a href="<?php echo esc_url( add_query_arg( 'category', $course_term['id'], $course_page ) ); ?>"><?php echo esc_html( $course_term['name'] ); ?></a>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<div class="stlms-search-wrap">
			<form action="<?php echo esc_url( \ST\Lms\get_page_url( 'courses' ) ); ?>" method="GET">
				<div class="stlms-search">
					<span class="stlms-search-icon">
						<svg width="20" height="20">
							<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#search"></use>
						</svg>
					</span>
					<input type="text" name="_s" class="stlms-form-control" placeholder="<?php esc_attr_e( 'Search', 'skilltriks' ); ?>" value="<?php echo esc_attr( $search_keyword ); ?>">
				</div>
			</form>
		</div>
		<div class="stlms-profile-wrap">
			<div class="stlms-notification-popup">
				<a href="<?php echo esc_url( \ST\Lms\get_page_url( 'notifications' ) ); ?>">
					<svg width="24" height="24">
						<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#Bell"></use>
					</svg>
				</a>
			</div>
			<?php $userinfo = wp_get_current_user(); ?>
			<div class="stlms-profile-popup stlms-dd-wrap">
				<button class="stlms-dd-btn" data-dropdown="profile">
					<div class="profile-picture">
						<?php if ( ! empty( get_user_meta( $userinfo->ID, 'avatar_url', true ) ) ) { ?>
							<img src="<?php echo esc_url( get_user_meta( $userinfo->ID, 'avatar_url', true ) ); ?>" width="48" height="48" alt="user-profile">
						<?php } else { ?>
							<?php echo get_avatar( $userinfo->ID ); ?>
						<?php } ?>
					</div>
				</button>
				<div class="stlms-dd-content" id="profile">
					<div class="stlms-profile-dd">
						<div class="stlms-profile-info">
							<div class="stlms-profile-picture">
								<?php if ( ! empty( get_user_meta( $userinfo->ID, 'avatar_url', true ) ) ) { ?>
									<img src="<?php echo esc_url( get_user_meta( $userinfo->ID, 'avatar_url', true ) ); ?>" width="48" height="48" alt="user-profile">
								<?php } else { ?>
									<?php echo get_avatar( $userinfo->ID ); ?>
								<?php } ?>
							</div>
							<div class="stlms-profile-name">
								<?php echo esc_html( $userinfo->display_name ); ?>
							</div>
						</div>
						<div class="stlms-profile-link-wrap">
							<?php if ( is_plugin_active( 'skilltriks-pro/skilltriks-pro.php' ) ) : ?>
							<a href="<?php echo esc_url( \ST\Lms\get_page_url( 'landing' ) ); ?>" class="stlms-profile-link">
								<svg width="24" height="24">
									<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#dashboard-icon"></use>
								</svg>
								<?php esc_html_e( 'Dashboard', 'skilltriks' ); ?>
							</a>
							<a href="<?php echo esc_url( \ST\Lms\get_page_url( 'courses' ) ); ?>" class="stlms-profile-link">
								<svg width="24" height="24">
									<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#all-courses-icon"></use>
								</svg>
								<?php esc_html_e( 'All Courses', 'skilltriks' ); ?>
							</a>
							<?php endif; ?>
							<a href="<?php echo esc_url( \ST\Lms\get_page_url( 'my_learning' ) ); ?>" class="stlms-profile-link">
								<svg width="24" height="24">
									<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#learning"></use>
								</svg>
								<?php esc_html_e( 'My Learnings', 'skilltriks' ); ?>
							</a>
							<a href="<?php echo esc_url( \ST\Lms\get_page_url( 'assign_course_to_me' ) ); ?>" class="stlms-profile-link">
								<svg width="24" height="24">
									<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#assigned-course"></use>
								</svg>
								<?php esc_html_e( 'Assigned Course', 'skilltriks' ); ?>
							</a>
							<a href="<?php echo esc_url( \ST\Lms\get_page_url( 'user_profile' ) ); ?>" class="stlms-profile-link">
								<svg width="24" height="24">
									<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#account-settings"></use>
								</svg>
								<?php esc_html_e( 'Account Settings', 'skilltriks' ); ?>
							</a>
							<a href="<?php echo esc_url( wp_logout_url( \ST\Lms\get_page_url( 'login' ) ) ); ?>" class="stlms-profile-link sign-out">
								<svg width="24" height="24">
									<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#signout"></use>
								</svg>
								<?php esc_html_e( 'Sign Out', 'skilltriks' ); ?>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
