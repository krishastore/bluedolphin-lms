<?php
/**
 * Template: Courses - action bar.
 *
 * @package BlueDolphin\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended
 */

$section_id       = get_query_var( 'section' ) ? (int) get_query_var( 'section' ) : 1;
$curriculums      = $args['curriculums'];
$current_item     = $args['current_item'];
$curriculums_keys = array_keys( $curriculums );
$current_index    = \BlueDolphin\Lms\find_current_curriculum_index( $current_item, $curriculums, $section_id );

$next_key = array_search( $current_index, $curriculums_keys, true );
if ( false !== $next_key ) {
	++$next_key;
}

$prev_key = array_search( $current_index, $curriculums_keys, true );
if ( false !== $prev_key ) {
	--$prev_key;
}

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
		<?php if ( $prev_key >= 0 && isset( $curriculums_keys[ $prev_key ] ) ) : ?>
			<a href="<?php echo esc_url( \BlueDolphin\Lms\get_curriculum_link( $curriculums_keys[ $prev_key ], $prev_key ) ); ?>" class="bdlms-btn bdlms-btn-icon bdlms-btn-flate">
				<svg class="icon" width="16" height="16">
					<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#arrow-left"></use>
				</svg>
				<?php esc_html_e( 'Previous', 'bluedolphin-lms' ); ?>
			</a>
		<?php endif; ?>
		<?php if ( $next_key >= 1 && isset( $curriculums_keys[ $next_key ] ) ) : ?>
			<a href="<?php echo esc_url( \BlueDolphin\Lms\get_curriculum_link( $curriculums_keys[ $next_key ], $next_key ) ); ?>" class="bdlms-btn bdlms-btn-icon bdlms-btn-flate">
				<?php esc_html_e( 'Next', 'bluedolphin-lms' ); ?>
				<svg class="icon" width="16" height="16">
					<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#arrow-right"></use>
				</svg>
			</a>
		<?php endif; ?>
	</div>
	<?php if ( ! empty( $args['current_item'] ) ) : ?>
		<div class="bdlms-lesson-toggle">
			<svg class="icon" width="20" height="20">
				<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#menu-burger"></use>
			</svg>
		</div>
	<?php endif; ?>
</div>