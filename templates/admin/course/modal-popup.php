<?php
/**
 * Template: Popup html template.
 *
 * @package BlueDolphin\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="select_items" class="hidden" style="max-width:463px">
	<div class="bdlms-qus-bank-modal">
		<div class="bdlms-tab-container">
			<div class="bdlms-tabs-nav">
				<button class="bdlms-tab active" data-tab="assign-quiz-list" data-filter_type="<?php echo esc_attr( \BlueDolphin\Lms\BDLMS_LESSON_CPT ); ?>"><?php esc_html_e( 'Lesson', 'bluedolphin-lms' ); ?></button>
				<button class="bdlms-tab" data-tab="assign-quiz-list" data-filter_type="<?php echo esc_attr( \BlueDolphin\Lms\BDLMS_QUIZ_CPT ); ?>"><?php esc_html_e( 'Quiz', 'bluedolphin-lms' ); ?></button>
			</div>

			<div class="bdlms-tab-content active" data-tab="assign-quiz-list">
				<input type="text" placeholder="<?php esc_attr_e( 'Type here to search for items', 'bluedolphin-lms' ); ?>" class="bdlms-qus-bank-search">
				<div class="bdlms-qus-list" id="curriculums_list">
				<?php
				if ( ! empty( $fetch_request ) ) :
					$args  = array(
						'posts_per_page' => -1,
						'post_type'      => $type,
						'post_status'    => 'publish',
					);
					$items = get_posts( $args );
					?>
					<?php if ( ! empty( $items ) ) : ?>
					<ul class="bdlms-qus-list-scroll">
						<?php
						foreach ( $items as $key => $item ) :
							$disabled_item = in_array( (int) $item->ID, $existing_items, true );
							?>
						<li class="<?php echo $disabled_item ? 'disabled-choose-item' : ''; ?>">
							<div class="bdlms-setting-checkbox">
								<input type="checkbox" class="bdlms-choose-item" id="bdlms-qus-<?php echo (int) $key; ?>" value="<?php echo (int) $item->ID; ?>"<?php checked( true, $disabled_item, true ); ?>>
								<label for="bdlms-qus-<?php echo (int) $key; ?>"><?php echo esc_html( $item->post_title ); ?></label>
							</div>
						</li>
						<?php endforeach; ?>
					</ul>
					<?php else : ?>
						<p><?php esc_html_e( 'No items found.', 'bluedolphin-lms' ); ?></p>
					<?php endif; ?>
				<?php else : ?>
					<span class="spinner is-active"></span>
			<?php endif; ?>
				</div>
			</div>
		</div>
		<div class="bdlms-qus-bank-add">
			<button class="button button-primary bdlms-add-item" disabled><?php esc_html_e( 'Add', 'bluedolphin-lms' ); ?></button>
			<span
				class="bdlms-qus-selected"><?php echo esc_html( sprintf( __( '%d Selected', 'bluedolphin-lms' ), 0 ) ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment ?></span>
			<span class="spinner"></span>
		</div>
	</div>
</div>
