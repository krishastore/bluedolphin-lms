<?php
/**
 * Template: Courses - action bar.
 *
 * @package ST\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$section_id       = get_query_var( 'section' ) ? (int) get_query_var( 'section' ) : 1;
$curriculums      = $args['curriculums'];
$current_item     = $args['current_item'];
$curriculum_type  = $args['curriculum_type'];
$curriculums_keys = array_keys( $curriculums );
$current_index    = \ST\Lms\find_current_curriculum_index( $current_item, $curriculums, $section_id );
$is_quiz          = \ST\Lms\STLMS_QUIZ_CPT === get_post_type( $current_item );
$media            = get_post_meta( $current_item, \ST\Lms\META_KEY_LESSON_MEDIA, true );

$next_key = array_search( $current_index, $curriculums_keys, true );
if ( false !== $next_key ) {
	++$next_key;
}

$prev_key = array_search( $current_index, $curriculums_keys, true );
if ( false !== $prev_key ) {
	--$prev_key;
}
$course_result   = apply_filters( 'stlms_course_result_endpoint', 'course-result' );
$result_page_url = sprintf( '%s/%s/%d/', untrailingslashit( home_url() ), $course_result, get_the_ID() );

?>
<div class="stlms-lesson-view__header">
	<div class="stlms-lesson-view__breadcrumb">
		<ul>
			<li>
				<a href="<?php echo esc_url( \ST\Lms\get_page_url( 'courses' ) ); ?>">
					<svg class="icon" width="16" height="16">
						<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#home"></use>
					</svg>
				</a>
			</li>
			<li><?php echo esc_html( get_the_title( $args['current_item'] ) ); ?></li>
		</ul>
	</div>
	<div class="stlms-lesson-view__pagination">
		<?php if ( $prev_key >= 0 && isset( $curriculums_keys[ $prev_key ] ) ) : ?>
			<a href="<?php echo esc_url( \ST\Lms\get_curriculum_link( $curriculums_keys[ $prev_key ] ) ); ?>" class="stlms-btn stlms-btn-icon stlms-btn-flate stlms-prev-btn">
				<svg class="icon" width="16" height="16">
					<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#arrow-left"></use>
				</svg>
				<?php esc_html_e( 'Previous', 'skilltriks' ); ?>
			</a>
		<?php endif; ?>
		<?php if ( $next_key >= 1 && isset( $curriculums_keys[ $next_key ] ) ) : ?>
			<a href="<?php echo esc_url( \ST\Lms\get_curriculum_link( $curriculums_keys[ $next_key ] ) ); ?>" class="stlms-btn stlms-btn-icon stlms-btn-flate stlms-next-btn <?php echo $is_quiz ? 'hidden' : ''; ?>">
				<?php esc_html_e( 'Next', 'skilltriks' ); ?>
				<svg class="icon" width="16" height="16">
					<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#arrow-right"></use>
				</svg>
			</a>
		<?php else : ?>
			<a href="<?php echo esc_url( $result_page_url ); ?>" class="stlms-btn stlms-btn-icon stlms-btn-flate stlms-next-btn<?php echo 'video' === $curriculum_type || $is_quiz ? ' hidden' : ''; ?>">
				<?php esc_html_e( 'Next', 'skilltriks' ); ?>
				<svg class="icon" width="16" height="16">
					<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#arrow-right"></use>
				</svg>
			</a>
		<?php endif; ?>
		<?php if ( class_exists( \LFI\Stlms\LicenseIntegration::class ) ) : ?>
			<?php if ( \LFI\Stlms\LicenseIntegration::instance()->is_pro() && ! $is_quiz && 'video' === $curriculum_type && ! empty( $media['video_id'] ) && ! empty( $media['ai_chatbot'] ) && 1 === $media['ai_chatbot'] ) : ?>
				<button class="stlms-ai-chat">
					<svg class="icon" width="20" height="20">
						<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#ai-chat"></use>
					</svg>
					<div>
						<?php esc_html_e( 'AI Chat', 'skilltriks' ); ?>
					</div>
				</button>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<?php if ( ! empty( $args['current_item'] ) ) : ?>
		<div class="stlms-lesson-toggle">
			<svg class="icon" width="20" height="20">
				<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#menu-burger"></use>
			</svg>
		</div>
	<?php endif; ?>
</div>
<div class="stlms-ai-chat__wrap">
	<div class="stlms-ai-chat__overlay"></div>
	<div class="stlms-ai-chat__box">
		<div class="stlms-ai-chat__header">
			<div class="stlms-ai-chat__title">
				<div class="stlms-ai-chat__title--left">
					<div class="stlms-ai-chat__icon">
						<svg class="icon" width="16" height="16">
							<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#ai-comment"></use>
						</svg>
					</div>
					<div class="stlms-ai-chat__text">
						<div>
							<?php esc_html_e( 'SkillTriks AI', 'skilltriks' ); ?>
						</div>
						<svg class="icon" width="20" height="20">
							<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#ai-star"></use>
						</svg>
					</div>
				</div>
				<div class="stlms-ai-chat__title--right">
					<button class="stlms-ai-chat-close">
						<svg class="icon" width="16" height="16">
							<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#cross"></use>
						</svg>
					</button>
				</div>
			</div>
		</div>
		<div class="stlms-ai-chat__body" id="stlms-ai-chat-body">
			<ul class="stlms-ai-chat__messages">
				<li class="stlms-ai-chat__message--bot">
					<div class="stlms-ai-chat__message--content">
						<?php esc_html_e( 'Hello! I am SkillTriks AI, your personal learning assistant. How can I help you today?', 'skilltriks' ); ?>
					</div>
				</li>
			</ul>
		</div>
		<div class="stlms-ai-chat__footer">
			<form class="stlms-ai-chat-form">
				<textarea class="stlms-ai-chat-input" placeholder="<?php esc_attr_e( 'Max 150 characters. letters, numbers, and question punctuation only', 'skilltriks' ); ?>"></textarea>
				<button type="submit" class="stlms-ai-chat-send-btn">
					<svg class="icon" width="16" height="16">
						<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#send-msg"></use>
					</svg>
				</button>
			</form>
			<div class="stlms-ai-chat__info">
				<?php esc_html_e( 'AI responses based on course content', 'skilltriks' ); ?>
			</div>
			<div class="stlms-ai-chat__disclaimer">
				<?php esc_html_e( 'This chat is for temporary use only. Conversations are not stored.', 'skilltriks' ); ?>
			</div>
		</div>
	</div>
</div>
