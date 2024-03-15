<?php
/**
 * The file that register metabox for question bank.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BlueDolphin\Lms
 *
 * phpcs:disable WordPress.NamingConventions.ValidHookName.UseUnderscores
 */

namespace BlueDolphin\Lms\Admin\MetaBoxes;

use function BlueDolphin\Lms\column_post_author as postAuthor;
use const BlueDolphin\Lms\BDLMS_QUESTION_CPT;
use const BlueDolphin\Lms\BDLMS_QUESTION_TAXONOMY_TAG;

/**
 * Register metaboxes for question bank.
 */
class QuestionBank extends \BlueDolphin\Lms\Collections\PostTypes {

	/**
	 * Meta key name.
	 *
	 * @var string $meta_key
	 */
	public $meta_key = '_question_options';

	/**
	 * Question alphabets.
	 *
	 * @var string $meta_key
	 */
	public $alphabets = '';

	/**
	 * Class construct.
	 */
	public function __construct() {
		$this->set_metaboxes( $this->meta_boxes_list() );
		$this->alphabets = \BlueDolphin\Lms\question_series();

		// Hooks.
		add_action( 'save_post_' . BDLMS_QUESTION_CPT, array( $this, 'save_metadata' ) );
		add_filter( 'manage_edit-' . BDLMS_QUESTION_CPT . '_sortable_columns', array( $this, 'sortable_columns' ) );
		add_filter( 'manage_' . BDLMS_QUESTION_CPT . '_posts_columns', array( $this, 'add_new_table_columns' ) );
		add_filter( 'post_row_actions', array( $this, 'quick_actions' ), 10, 2 );
		add_action( 'manage_' . BDLMS_QUESTION_CPT . '_posts_custom_column', array( $this, 'manage_custom_column' ), 10, 2 );
		add_action( 'quick_edit_custom_box', array( $this, 'quick_edit_custom_box' ), 10, 2 );
	}

	/**
	 * Meta boxes list.
	 *
	 * @return array
	 */
	private function meta_boxes_list() {
		$list = apply_filters(
			'bluedolphin/questions/meta_boxes',
			array(
				array(
					'id'       => 'answer-options',
					'title'    => __( 'Answer Options', 'bluedolphin-lms' ),
					'callback' => array( $this, 'render_answer_options' ),
				),
				array(
					'id'       => 'question-settings',
					'title'    => __( 'Question Settings', 'bluedolphin-lms' ),
					'callback' => array( $this, 'render_question_settings' ),
				),
			)
		);
		return $list;
	}

	/**
	 * Render answer options metabox.
	 */
	public function render_answer_options() {
		global $post;
		$post_id = isset( $post->ID ) ? $post->ID : 0;
		$data    = get_post_meta( $post_id, $this->meta_key, true );
		$type    = isset( $data['type'] ) ? $data['type'] : 'true_or_false';
		?>
		<?php wp_nonce_field( BDLMS_BASEFILE, 'bdlms_nonce', false ); ?>
		<input type="hidden" value="1" name="<?php echo esc_attr( $this->meta_key ); ?>[status]">
		<div class="bdlms-answer-wrap">
			<div class="bdlms-answer-type">
				<label for="answers_field">
					<?php esc_html_e( 'Select Answer Type', 'bluedolphin-lms' ); ?>
				</label>
				<select name="<?php echo esc_attr( $this->meta_key ); ?>[type]" id="bdlms_answer_type">
					<option value="true_or_false"<?php selected( 'true_or_false', $type ); ?>><?php esc_html_e( 'True Or False ', 'bluedolphin-lms' ); ?></option>
					<option value="multi_choice"<?php selected( 'multi_choice', $type ); ?>><?php esc_html_e( 'Multi Choice ', 'bluedolphin-lms' ); ?></option>
					<option value="single_choice"<?php selected( 'single_choice', $type ); ?>><?php esc_html_e( 'Single Choice ', 'bluedolphin-lms' ); ?></option>
					<option value="fill_blank"<?php selected( 'fill_blank', $type ); ?>><?php esc_html_e( 'Fill In Blanks ', 'bluedolphin-lms' ); ?></option>
				</select>
			</div>

			<div class="bdlms-answer-group<?php echo 'true_or_false' !== $type ? ' hidden' : ''; ?>" id="true_or_false">
				<?php
					$corret_answers = isset( $data['true_or_false_answers'] ) ? $data['true_or_false_answers'] : '';
					$answers        = isset( $data['true_or_false'] ) ? $data['true_or_false'] :
					array(
						0 => __( 'True', 'bluedolphin-lms' ),
						1 => __( 'False', 'bluedolphin-lms' ),
					);
					?>
					<div class="bdlms-options-table">
						<div class="bdlms-options-table__header">
							<ul class="bdlms-options-table__list">
								<li><?php esc_html_e( 'Options ', 'bluedolphin-lms' ); ?></li>
								<li class="bdlms-option-check-td"><?php esc_html_e( 'Correct Option', 'bluedolphin-lms' ); ?></li>
							</ul>
						</div>
						<div class="bdlms-options-table__body bdlms-sortable-answers">
							<div class="bdlms-options-table__list-wrap">
								<?php foreach ( $answers as $key => $answer ) : ?>
									<ul class="bdlms-options-table__list">
										<li>
											<div class="bdlms-options-value">
												<div class="bdlms-options-drag">
													<svg class="icon" width="8" height="13">
														<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
													</svg>
												</div>
												<input type="text" class="bdlms-option-value-input" value="<?php echo esc_attr( $answer ); ?>" name="<?php echo esc_attr( $this->meta_key ); ?>[true_or_false][]" readonly>
											</div>
										</li>
										<li class="bdlms-option-check-td">
											<input type="radio" value="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $this->meta_key ); ?>[true_or_false_answers]"<?php checked( wp_hash( $answer ), $corret_answers ); ?>>
										</li>
									</ul>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
			</div>

			<div class="bdlms-answer-group <?php echo 'multi_choice' !== $type ? ' hidden' : ''; ?>" id="multi_choice">
				<?php
					$corret_answers = isset( $data['multi_choice_answers'] ) ? $data['multi_choice_answers'] : array();
					$answers        = isset( $data['multi_choice'] ) ? $data['multi_choice'] : array_fill( 0, 4, '' );
				?>
					<div class="bdlms-options-table">
						<div class="bdlms-options-table__header">
							<ul class="bdlms-options-table__list">
								<li><?php esc_html_e( 'Options', 'bluedolphin-lms' ); ?></li>
								<li class="bdlms-option-check-td"><?php esc_html_e( 'Correct Option', 'bluedolphin-lms' ); ?></li>
								<li class="bdlms-option-action"></li>
							</ul>
						</div>
						<div class="bdlms-options-table__body bdlms-sortable-answers">
							<div class="bdlms-options-table__list-wrap">
								<?php
								foreach ( $answers as $key => $answer ) :
									?>
									<ul class="bdlms-options-table__list">
										<li>
											<div class="bdlms-options-value">
												<div class="bdlms-options-drag">
													<svg class="icon" width="8" height="13">
														<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
													</svg>
												</div>
												<div class="bdlms-options-no"><?php printf( '%s.', isset( $this->alphabets[ $key ] ) ? esc_html( $this->alphabets[ $key ] ) : '' ); ?></div>
												<input type="text" value="<?php echo esc_attr( $answer ); ?>" name="<?php echo esc_attr( $this->meta_key ); ?>[multi_choice][]">
											</div>
										</li>
										<li class="bdlms-option-check-td">
											<input type="checkbox" value="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $this->meta_key ); ?>[multi_choice_answers][]"<?php echo in_array( wp_hash( $answer ), $corret_answers, true ) ? ' checked' : ''; ?>>
										</li>
										<li class="bdlms-option-action">
											<button type="button" class="bdlms-remove-answer">
												<svg class="icon" width="12" height="12">
													<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#trash"></use>
												</svg>
											</button>
										</li>
									</ul>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
			</div>

			<div class="bdlms-answer-group <?php echo 'single_choice' !== $type ? ' hidden' : ''; ?>" id="single_choice">
				<?php
					$corret_answers = isset( $data['single_choice_answers'] ) ? $data['single_choice_answers'] : '';
					$answers        = isset( $data['single_choice'] ) ? $data['single_choice'] : array_fill( 0, 4, '' );
				?>
					<div class="bdlms-options-table">
						<div class="bdlms-options-table__header">
							<ul class="bdlms-options-table__list">
								<li><?php esc_html_e( 'Options', 'bluedolphin-lms' ); ?></li>
								<li class="bdlms-option-check-td"><?php esc_html_e( 'Correct Option', 'bluedolphin-lms' ); ?></li>
								<li class="bdlms-option-action"></li>
							</ul>
						</div>
						<div class="bdlms-options-table__body bdlms-sortable-answers">
							<div class="bdlms-options-table__list-wrap">
								<?php
								foreach ( $answers as $key => $answer ) :
									?>
									<ul class="bdlms-options-table__list bdlms-sortable-answers">
										<li>
											<div class="bdlms-options-value">
												<div class="bdlms-options-drag">
													<svg class="icon" width="8" height="13">
														<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
													</svg>
												</div>
												<div class="bdlms-options-no"><?php printf( '%s.', isset( $this->alphabets[ $key ] ) ? esc_html( $this->alphabets[ $key ] ) : '' ); ?></div>
												<input type="text" value="<?php echo esc_attr( $answer ); ?>" name="<?php echo esc_attr( $this->meta_key ); ?>[single_choice][]">
											</div>
										</li>
										<li class="bdlms-option-check-td">
											<input type="radio" value="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $this->meta_key ); ?>[single_choice_answers]"<?php checked( wp_hash( $answer ), $corret_answers ); ?>>
										</li>
										<li class="bdlms-option-action">
											<button type="button" class="bdlms-remove-answer">
												<svg class="icon" width="12" height="12">
													<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#trash"></use>
												</svg>
											</button>
										</li>
									</ul>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
			</div>

			<div class="bdlms-answer-group <?php echo 'fill_blank' !== $type ? ' hidden' : ''; ?>" id="fill_blank">
				<?php
					$mandatory_answers = isset( $data['mandatory_answers'] ) ? $data['mandatory_answers'] : '';
					$optional_answers  = isset( $data['optional_answers'] ) ? $data['optional_answers'] : array_fill( 0, 4, '' );
				?>
				<div class="bdlms-add-accepted-answers">
					<h3><?php esc_html_e( 'Add Accepted Answers', 'bluedolphin-lms' ); ?></h3>
					<ul>
						<li>
							<label><?php esc_html_e( 'Mandatory', 'bluedolphin-lms' ); ?></label>
							<input type="text" name="<?php echo esc_attr( $this->meta_key ); ?>[mandatory_answers]" value="<?php echo esc_attr( $mandatory_answers ); ?>">
						</li>
						<?php foreach ( $optional_answers as $optional_answer ) : ?>
							<li>
								<label><?php esc_html_e( 'Optional', 'bluedolphin-lms' ); ?></label>
								<input type="text" name="<?php echo esc_attr( $this->meta_key ); ?>[optional_answers][]" value="<?php echo esc_attr( $optional_answer ); ?>">
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>

			<div class="bdlms-add-option hidden">
				<button type="button" class="button bdlms-add-answer"><?php esc_html_e( 'Add More Options', 'bluedolphin-lms' ); ?></button>
			</div>
		</div>
		<?php
	}

	/**
	 * Render question settings metabox.
	 */
	public function render_question_settings() {
		global $post;
		$post_id  = isset( $post->ID ) ? $post->ID : 0;
		$settings = get_post_meta( $post_id, $this->meta_key, true );
		$settings = isset( $settings['settings'] ) ? $settings['settings'] : array();
		$levels   = isset( $settings['levels'] ) ? $settings['levels'] : '';
		?>
		<div class="bdlms-qus-setting-wrap">
			<div class="bdlms-qus-setting-header">
				<div>
					<label for="points_field">
						<?php esc_html_e( 'Marks/Points: ', 'bluedolphin-lms' ); ?>
					</label>
					<input type="number" name="<?php echo esc_attr( $this->meta_key ); ?>[settings][points]" value="<?php echo isset( $settings['points'] ) ? (int) $settings['points'] : 1; ?>" step="1" min="1">
				</div>
				<div>
					<label for="levels_field">
						<?php esc_html_e( 'Difficulty Level', 'bluedolphin-lms' ); ?>
					</label>
					<select name="<?php echo esc_attr( $this->meta_key ); ?>[settings][levels]">
						<?php
						foreach ( \BlueDolphin\Lms\question_levels() as $key => $level ) {
							?>
								<option value="<?php echo esc_attr( $key ); ?>"<?php selected( $levels, $key ); ?>><?php echo esc_html( $level ); ?></option>
							<?php
						}
						?>
					</select>
				</div>
			</div>
			<div class="bdlms-qus-setting-body">
				<h3><?php esc_html_e( 'Show Feedback/Hint ', 'bluedolphin-lms' ); ?></h3>

				<div class="bdlms-hint-box">
					<label for="hint_field">
						<?php esc_html_e( 'Correctly Answered Feedback: ', 'bluedolphin-lms' ); ?>
						<div class="bdlms-tooltip">
							<svg class="icon" width="12" height="12">
								<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#help"></use>
							</svg>
							<span class="bdlms-tooltiptext">
								<?php esc_html_e( 'The instructions for the user to select the right answer. The text will be shown when users click the \'Hint\' button.', 'bluedolphin-lms' ); ?>
							</span>
						</div>
					</label>
					<textarea name="<?php echo esc_attr( $this->meta_key ); ?>[settings][hint]"><?php echo isset( $settings['hint'] ) ? esc_textarea( $settings['hint'] ) : ''; ?></textarea>
				</div>
				<div class="bdlms-hint-box">
					<label for="explanation_field" style="color: #B20000;">
						<?php esc_html_e( 'Incorrectly Answered Feedback: ', 'bluedolphin-lms' ); ?>
						<div class="bdlms-tooltip">
							<svg class="icon" width="12" height="12">
								<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#help"></use>
							</svg>
							<span class="bdlms-tooltiptext">
								<?php esc_html_e( 'The explanation will be displayed when students click the "Check Answer" button.', 'bluedolphin-lms' ); ?>
							</span>
						</div>
					</label>
					<textarea name="<?php echo esc_attr( $this->meta_key ); ?>[settings][explanation]"><?php echo isset( $settings['explanation'] ) ? esc_textarea( $settings['explanation'] ) : ''; ?></textarea>
				</div>
			</div>
		</div>

		<?php
	}

	/**
	 * Save post meta.
	 */
	public function save_metadata() {
		global $post;
		$post_id   = isset( $post->ID ) ? $post->ID : 0;
		$post_data = array();

		if ( ( isset( $_POST['action'] ) && 'inline-save' !== $_POST['action'] ) && ( isset( $_POST['bdlms_nonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bdlms_nonce'] ) ), BDLMS_BASEFILE ) ) ) {
			return;
		}

		$type = isset( $_POST[ $this->meta_key ]['type'] ) ? sanitize_text_field( wp_unslash( $_POST[ $this->meta_key ]['type'] ) ) : '';
		// Quick edit action.
		if ( isset( $_POST['action'] ) && 'inline-save' === $_POST['action'] ) {
			$post_id   = isset( $_POST['post_ID'] ) ? (int) $_POST['post_ID'] : $post_id;
			$post_data = get_post_meta( $post_id, $this->meta_key, true );
		}

		if ( isset( $_POST[ $this->meta_key ][ $type ] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$post_data[ $type ] = map_deep( $_POST[ $this->meta_key ][ $type ], 'sanitize_text_field' );
		}
		if ( isset( $_POST[ $this->meta_key ][ $type . '_answers' ] ) ) {
			if ( is_array( $_POST[ $this->meta_key ][ $type . '_answers' ] ) ) {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
				$current_answer = map_deep( $_POST[ $this->meta_key ][ $type . '_answers' ], 'intval' );
				$current_answer = array_map(
					function ( $i ) use ( $post_data, $type ) {
						if ( isset( $post_data[ $type ][ $i ] ) ) {
							return wp_hash( $post_data[ $type ][ $i ] );
						}
						return wp_hash( $i );
					},
					$current_answer
				);

				$post_data[ $type . '_answers' ] = $current_answer;
			} else {
				$current_answer                  = (int) $_POST[ $this->meta_key ][ $type . '_answers' ];
				$post_data[ $type . '_answers' ] = isset( $post_data[ $type ][ $current_answer ] ) ? wp_hash( $post_data[ $type ][ $current_answer ] ) : wp_hash( $current_answer );
			}
		}

		if ( isset( $_POST[ $this->meta_key ]['status'] ) ) {
			$post_data['status'] = true;
		}

		if ( isset( $_POST[ $this->meta_key ]['mandatory_answers'] ) ) {
			$post_data['mandatory_answers'] = sanitize_text_field( wp_unslash( $_POST[ $this->meta_key ]['mandatory_answers'] ) );
		}

		if ( isset( $_POST[ $this->meta_key ]['optional_answers'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$post_data['optional_answers'] = map_deep( $_POST[ $this->meta_key ]['optional_answers'], 'sanitize_text_field' );
		}

		if ( isset( $_POST[ $this->meta_key ]['settings']['points'] ) ) {
			$post_data['settings']['points'] = (int) $_POST[ $this->meta_key ]['settings']['points'];
		}
		if ( isset( $_POST[ $this->meta_key ]['settings']['hint'] ) ) {
			$post_data['settings']['hint'] = sanitize_textarea_field( wp_unslash( $_POST[ $this->meta_key ]['settings']['hint'] ) );
		}
		if ( isset( $_POST[ $this->meta_key ]['settings']['explanation'] ) ) {
			$post_data['settings']['explanation'] = sanitize_textarea_field( wp_unslash( $_POST[ $this->meta_key ]['settings']['explanation'] ) );
		}

		if ( isset( $_POST[ $this->meta_key ]['settings']['levels'] ) ) {
			$post_data['settings']['levels'] = sanitize_textarea_field( wp_unslash( $_POST[ $this->meta_key ]['settings']['levels'] ) );
		}

		if ( ! empty( $type ) ) {
			$post_data['type'] = $type;
		}
		update_post_meta( $post_id, $this->meta_key, $post_data );
	}

	/**
	 * Add new table columns.
	 *
	 * @param array $columns Columns list.
	 * @return array
	 */
	public function add_new_table_columns( $columns ) {
		$date = $columns['date'];
		unset( $columns['date'] );

		$topic_key = 'taxonomy-bdlms_quesion_topics';
		$topic     = $columns[ $topic_key ];
		unset( $columns[ $topic_key ] );
		unset( $columns['author'] );
		$columns['post_author'] = __( 'Author', 'bluedolphin-lms' );
		$columns['quiz']        = __( 'Quiz', 'bluedolphin-lms' );
		$columns['levels']      = __( 'Levels', 'bluedolphin-lms' );
		$columns['type']        = __( 'Type', 'bluedolphin-lms' );
		$columns[ $topic_key ]  = __( 'Topic', 'bluedolphin-lms' );
		$columns['date']        = $date;
		return $columns;
	}

	/**
	 * Manage custom column.
	 *
	 * @param string $column Column name.
	 * @param int    $post_id Post ID.
	 *
	 * @return void
	 */
	public function manage_custom_column( $column, $post_id ) {
		$data = get_post_meta( $post_id, $this->meta_key, true );
		switch ( $column ) {
			case 'quiz':
				echo ! empty( $data['quiz'] ) ? esc_html( 'Quiz Name' ) : '—';
				break;

			case 'type':
				echo ! empty( $data['type'] ) ? esc_html( ucwords( str_replace( '_', ' ', $data['type'] ) ) ) : '—';
				break;

			case 'levels':
				echo ! empty( $data['settings']['levels'] ) ? esc_html( ucwords( str_replace( '_', ' ', $data['settings']['levels'] ) ) ) : '—';
				break;

			case 'post_author':
				echo wp_kses_post( postAuthor( $post_id ) );
				break;

			default:
				break;
		}
	}

	/**
	 * Sortable columns list.
	 *
	 * @param array $columns Sortable columns.
	 *
	 * @return array
	 */
	public function sortable_columns( $columns ) {
		$columns['quiz']        = 'quiz';
		$columns['post_author'] = 'author';
		return $columns;
	}

	/**
	 * Quick edit custom box.
	 *
	 * @param string $column_name Column name.
	 * @param string $post_type Post Type.
	 */
	public function quick_edit_custom_box( $column_name, $post_type ) {
		if ( BDLMS_QUESTION_CPT !== $post_type ) {
			return;
		}
		?>
		<fieldset class="inline-edit-col-right inline-edit-levels">
		<div class="inline-edit-col inline-edit-<?php echo esc_attr( $column_name ); ?>">
		<label class="inline-edit-group">
		<?php
		switch ( $column_name ) {
			case 'levels':
				?>
			<span class="title"><?php esc_html_e( 'Difficulty Level', 'bluedolphin-lms' ); ?></span>
				<select name="<?php echo esc_attr( $this->meta_key ); ?>[settings][levels]">
					<?php
					foreach ( \BlueDolphin\Lms\question_levels() as $key => $level ) {
						?>
							<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $level ); ?></option>
						<?php
					}
					?>
				</select>
				<?php
				break;
		}
		?>
		</label>
		</div>
	</fieldset>
		<?php
	}

	/**
	 * Filters the array of row action links on the Posts list table.
	 *
	 * @param array  $actions Row action.
	 * @param object $post Post object.
	 * @return array
	 */
	public function quick_actions( $actions, $post ) {
		if ( BDLMS_QUESTION_CPT === $post->post_type ) {
			$question_data = get_post_meta( $post->ID, $this->meta_key, true );
			$type          = isset( $question_data['type'] ) ? $question_data['type'] : 'true_or_false';
			$answers       = isset( $question_data[ $type . '_answers' ] ) ? $question_data[ $type . '_answers' ] : '';
			$answers_list  = isset( $question_data[ $type ] ) ? $question_data[ $type ] : array();
			$answers_list  = array_map(
				function ( $answer_list ) use ( $answers ) {
					$checked = is_array( $answers ) ? in_array( wp_hash( $answer_list ), $answers, true ) : wp_hash( $answer_list ) === $answers;
					return array(
						'option'  => $answer_list,
						'checked' => $checked,
					);
				},
				$answers_list
			);

			$data = array(
				'title' => $post->post_title,
				'type'  => isset( $question_data['type'] ) ? $question_data['type'] : '',
				'marks' => isset( $question_data['settings']['points'] ) ? $question_data['settings']['points'] : '',
			);
			if ( 'fill_blank' !== $type ) {
				$data[ $type ] = $answers_list;
			}
			if ( 'fill_blank' === $type ) {
				$data['mandatory'] = isset( $question_data['mandatory_answers'] ) ? $question_data['mandatory_answers'] : '';
				$data['optional']  = isset( $question_data['optional_answers'] ) ? $question_data['optional_answers'] : '';
			}
			$actions['show_answer'] = '<a href="javascript:;" data-inline_edit="' . esc_attr( wp_json_encode( $data ) ) . '" aria-expanded="false">' . __( 'Show Answer', 'bluedolphin-lms' ) . '<a>';
		}

		// Clone action.
		if ( in_array( $post->post_type, array( \BlueDolphin\Lms\BDLMS_QUESTION_CPT ), true ) ) {
			$url                   = wp_nonce_url(
				add_query_arg(
					array(
						'action' => 'bdlms_clone',
						'post'   => $post->ID,
					),
					'admin.php'
				),
				BDLMS_BASEFILE,
				'bdlms_nonce'
			);
			$actions['clone_post'] = '<a href="' . esc_url( $url ) . '">' . esc_attr__( 'Clone', 'bluedolphin-lms' ) . ' </a>';
		}
		return $actions;
	}
}
