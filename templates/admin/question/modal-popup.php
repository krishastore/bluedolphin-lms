<?php
/**
 * Template: Popup html template.
 *
 * @package BlueDolphin\Lms
 */

$quiz_id = isset( $post->ID ) ? $post->ID : 0;
?>

<div id="assign_quiz" class="hidden" style="max-width:463px">
    <div class="bdlms-qus-bank-modal">

        <div class="bdlms-tab-container">
            <div class="bdlms-tabs-nav">
                <button class="bdlms-tab active" data-tab="assign-quiz-tab1">All Topics</button>
                <button class="bdlms-tab" data-tab="assign-quiz-tab2">Most Used</button>
            </div>

            <div class="bdlms-tab-content active" data-tab="assign-quiz-tab1">
                <input type="hidden" id="question_id" value="<?php echo (int) $quiz_id; ?>">
                <input type="text"
                    placeholder="<?php esc_attr_e( 'Type here to search for the quiz', 'bluedolphin-lms' ); ?>"
                    class="bdlms-qus-bank-search">
                <?php
					$args = array(
						'posts_per_page' => 5,
						'orderby'        => 'rand',
						'post_type'      => \BlueDolphin\Lms\BDLMS_QUIZ_CPT,
						'post_status'    => 'publish',
					);
					if ( isset( $s ) ) {
						$args['s'] = $s;
					}
					$quizzes = get_posts( $args );
				?>
                <div class="bdlms-qus-list" id="bdlms_quiz_list">
                    <?php if ( ! empty( $quizzes ) ) : ?>
                    <ul>
                        <?php
					foreach ( $quizzes as $key => $quiz ) :
						?>
                        <li>
                            <div class="bdlms-setting-checkbox">
                                <input type="checkbox" class="bdlms-choose-quiz"
                                    id="bdlms-qus-<?php echo (int) $key; ?>" value="<?php echo (int) $quiz->ID; ?>">
                                <label
                                    for="bdlms-qus-<?php echo (int) $key; ?>"><?php echo esc_html( $quiz->post_title ); ?></label>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php else : ?>
                    <p><?php esc_html_e( 'No quiz found.', 'bluedolphin-lms' ); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="bdlms-tab-content" data-tab="assign-quiz-tab2">
                Content of Tab 2
            </div>
        </div>

        <div class="bdlms-qus-bank-add">
            <button class="button button-primary bdlms-add-quiz"
                disabled><?php esc_html_e( 'Add', 'bluedolphin-lms' ); ?></button>
            <span
                class="bdlms-qus-selected"><?php echo esc_html( sprintf( __( '%d Selected', 'bluedolphin-lms' ), 0 ) ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment ?></span>
            <span class="spinner"></span>
        </div>
    </div>
</div>