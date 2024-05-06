<?php
/**
 * Template: Courses - action bar.
 *
 * @package BlueDolphin\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended
 */

?>
<div class="bdlms-lesson-view__header">
	<div class="bdlms-lesson-view__breadcrumb">
		<ul>
			<li>
				<a href="<?php echo esc_url( \BlueDolphin\Lms\get_page_url( 'courses' ) ); ?>">
					<svg class="icon" width="16" height="16">
						<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#home"></use>
					</svg>
				</a>
			</li>
			<li><?php echo esc_html( get_the_title( $args['current_item'] ) ); ?></li>
		</ul>
	</div>
	<div class="bdlms-lesson-view__pagination">
		<a href="#" class="bdlms-btn bdlms-btn-icon bdlms-btn-flate">
			<svg class="icon" width="16" height="16">
				<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#arrow-left"></use>
			</svg>
			<?php esc_html_e( 'Previous', 'bluedolphin-lms' ); ?>
		</a>
		<a href="#" class="bdlms-btn bdlms-btn-icon bdlms-btn-flate">
			<?php esc_html_e( 'Next', 'bluedolphin-lms' ); ?>
			<svg class="icon" width="16" height="16">
				<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#arrow-right"></use>
			</svg>
		</a>
	</div>
	<div class="bdlms-lesson-toggle">
		<svg class="icon" width="20" height="20">
			<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#menu-burger"></use>
		</svg>
	</div>
</div>