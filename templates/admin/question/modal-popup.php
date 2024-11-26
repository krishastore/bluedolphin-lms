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

<div id="assign_quiz" class="hidden" style="max-width:463px">
	<div class="bdlms-qus-bank-modal">
		<div class="bdlms-tab-container">
			<div class="bdlms-tabs-nav">
				<button class="bdlms-tab active" data-tab="assign-quiz-list" data-filter_type="all"><?php esc_html_e( 'All', 'bluedolphin-lms' ); ?></button>
				<button class="bdlms-tab" data-tab="assign-quiz-list" data-filter_type="most_used"><?php esc_html_e( 'Most Used', 'bluedolphin-lms' ); ?></button>
			</div>

			<div class="bdlms-tab-content active" data-tab="assign-quiz-list">
				<input type="text"
					placeholder="<?php esc_attr_e( 'Type here to search for the quiz', 'bluedolphin-lms' ); ?>"
					class="bdlms-qus-bank-search">
				<div class="bdlms-qus-list" id="bdlms_quiz_list">
				<?php
				if ( ! empty( $fetch_request ) ) :
					$args = array(
						'posts_per_page' => -1,
						'post_type'      => \BlueDolphin\Lms\BDLMS_QUIZ_CPT,
						'post_status'    => 'publish',
					);
					if ( isset( $type ) && 'most_used' === $type ) {
						$popular_ids = wp_popular_terms_checklist( \BlueDolphin\Lms\BDLMS_QUIZ_TAXONOMY_LEVEL_1, 0, 10, false );
						// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
						$args['tax_query'][] = array(
							'taxonomy' => \BlueDolphin\Lms\BDLMS_QUIZ_TAXONOMY_LEVEL_1,
							'field'    => 'term_id',
							'terms'    => $popular_ids,
							'operator' => 'IN',
						);
					}
					$quizzes  = get_posts( $args );
					$quiz_ids = get_posts(
						array(
							'post_type'    => \BlueDolphin\Lms\BDLMS_QUIZ_CPT,
							// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
							'meta_key'     => \BlueDolphin\Lms\META_KEY_QUIZ_QUESTION_IDS,
							// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
							'meta_value'   => array( $question_id ),
							'meta_compare' => 'REGEXP',
							'fields'       => 'ids',
						)
					);
					$quiz_ids = ! empty( $quiz_ids ) ? $quiz_ids : array();
					?>
					<?php if ( ! empty( $quizzes ) ) : ?>
					<ul class="bdlms-qus-list-scroll">
						<?php
						foreach ( $quizzes as $key => $quiz ) :
							?>
						<li>
							<div class="bdlms-setting-checkbox">
								<input type="checkbox" class="bdlms-choose-quiz" id="bdlms-qus-<?php echo (int) $key; ?>" value="<?php echo (int) $quiz->ID; ?>" <?php echo esc_attr( in_array( $quiz->ID, $quiz_ids, true ) ? 'checked' : '' ); ?>>
								<label for="bdlms-qus-<?php echo (int) $key; ?>"><?php echo esc_html( $quiz->post_title ); ?></label>
							</div>
						</li>
						<?php endforeach; ?>
					</ul>
					<?php else : ?>
						<p><?php esc_html_e( 'No quiz found.', 'bluedolphin-lms' ); ?></p>
					<?php endif; ?>
				<?php else : ?>
					<span class="spinner is-active"></span>
			<?php endif; ?>
				</div>
			</div>
		</div>
		<div class="bdlms-qus-bank-add">
			<button class="button button-primary bdlms-add-quiz"
				disabled><?php esc_html_e( 'Save', 'bluedolphin-lms' ); ?></button>
			<span
				class="bdlms-qus-selected"><?php echo esc_html( sprintf( __( '%d Selected', 'bluedolphin-lms' ), 0 ) ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment ?></span>
			<span class="spinner"></span>
		</div>
	</div>
</div>
