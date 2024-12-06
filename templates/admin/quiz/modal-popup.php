<?php
/**
 * Template: Popup html template.
 *
 * @package BD\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div id="add_new_question" class="hidden bdlms-add-qus-modal" style="max-width:463px">
	<div class="bdlms-btn-group">
		<button class="button button-primary create-your-own"><?php esc_html_e( 'Create Your Own', 'bluedolphin-lms' ); ?></button>
		<button class="button open-questions-bank"><?php esc_html_e( 'Add From Existing', 'bluedolphin-lms' ); ?></button>
		<span class="spinner"></span>
	</div>
	<p>
		<strong><?php esc_html_e( 'Tips:', 'bluedolphin-lms' ); ?></strong>
	</p>
	<p><?php esc_html_e( 'Add from existing helps you to add question from your question bank which are stored.', 'bluedolphin-lms' ); ?></p>
</div>

<div id="questions_bank" class="hidden" style="max-width:463px">
	<div class="bdlms-qus-bank-modal">
		<input type="text" placeholder="<?php esc_attr_e( 'Type here to search for the question', 'bluedolphin-lms' ); ?>" class="bdlms-qus-bank-search">
		<div class="bdlms-qus-list" id="bdlms_qus_list">
			<?php
			if ( ! empty( $fetch_request ) ) :
				$args          = array(
					'posts_per_page' => -1,
					'post_type'      => \BD\Lms\BDLMS_QUESTION_CPT,
					'post_status'    => 'publish',
				);
				$question_list = get_posts( $args );
				?>
				<?php if ( ! empty( $question_list ) ) : ?>
					<ul class="bdlms-qus-list-scroll">
						<?php
						foreach ( $question_list as $key => $question ) :
							$topic = wp_get_post_terms( $question->ID, \BD\Lms\BDLMS_QUESTION_TAXONOMY_TAG, array( 'fields' => 'names' ) );
							?>
							<li>
								<div class="bdlms-setting-checkbox">
									<?php if ( in_array( $question->ID, $questions, true ) ) : ?>
										<input type="checkbox" class="bdlms-choose-existing" id="bdlms-qus-<?php echo (int) $key; ?>" value="<?php echo (int) $question->ID; ?>" checked disabled>
									<?php else : ?>
										<input type="checkbox" class="bdlms-choose-existing" id="bdlms-qus-<?php echo (int) $key; ?>" value="<?php echo (int) $question->ID; ?>">
									<?php endif; ?>
									<label for="bdlms-qus-<?php echo (int) $key; ?>"><?php echo esc_html( $question->post_title ); ?><?php echo ! empty( $topic ) ? ' <strong>(' . esc_html( implode( ', ', $topic ) ) . ')</strong>' : ''; ?></label>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php else : ?>
					<p><?php esc_html_e( 'No questions found.', 'bluedolphin-lms' ); ?></p>
				<?php endif; ?>
			<?php else : ?>
				<span class="spinner is-active"></span>
			<?php endif; ?>
		</div>

		<div class="bdlms-qus-bank-add">
			<button class="button button-primary bdlms-add-question" disabled><?php esc_html_e( 'Add', 'bluedolphin-lms' ); ?></button>
			<span class="bdlms-qus-selected"><?php echo esc_html( sprintf( __( '%d Selected', 'bluedolphin-lms' ), 0 ) ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment ?></span>
			<span class="spinner"></span>
		</div>
	</div>
</div>
