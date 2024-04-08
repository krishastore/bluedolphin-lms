<?php
/**
 * Template: Question list.
 *
 * @package BlueDolphin\Lms
 */

?>

<?php
foreach ( $questions as $question_id ) :
	$question_title = get_the_title( $question_id );
	$qtype          = get_post_meta( $question_id, $this->question_meta_key . '_type', true );
	$data           = \BlueDolphin\Lms\get_question_by_type( $question_id, $qtype, $this->question_meta_key );
	$qtype          = ! empty( $qtype ) ? $qtype : 'true_or_false';

	// Get question settings.
	$settings    = get_post_meta( $question_id, $this->question_meta_key . '_settings', true );
	$settings    = ! empty( $settings ) ? $settings : array();
	$point       = isset( $settings['points'] ) ? (int) $settings['points'] : 0;
	$hint        = isset( $settings['hint'] ) ? esc_textarea( $settings['hint'] ) : '';
	$explanation = isset( $settings['explanation'] ) ? esc_textarea( $settings['explanation'] ) : '';
	$levels      = isset( $settings['levels'] ) ? esc_textarea( $settings['levels'] ) : '';
	$qstatus     = isset( $settings['status'] ) ? $settings['status'] : 0;
	?>
	<li>
		<input type="hidden" class="bdlms-qid" name="<?php echo esc_attr( $this->meta_key ); ?>[question_id][]" value="<?php echo (int) $question_id; ?>">
		<div class="bdlms-quiz-qus-item">
			<div class="bdlms-quiz-qus-item__header">
				<div class="bdlms-options-drag">
					<svg class="icon" width="8" height="13">
						<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
					</svg>
				</div>
				<div class="bdlms-quiz-qus-name">
					<span><?php echo esc_html( $question_title ); ?></span>
					<span class="bdlms-quiz-qus-point"><?php printf( esc_html__( '%d Point', 'bluedolphin-lms' ), (int) $point ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment ?></span>
				</div>
				<div class="bdlms-quiz-qus-toggle" data-accordion="true">
					<svg class="icon" width="18" height="18">
						<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#down-arrow"></use>
					</svg>
				</div>
			</div>
			<div class="bdlms-quiz-qus-item__body">
				<div class="bdlms-answer-wrap">
					<div class="bdlms-quiz-name">
						<input type="text" name="<?php echo esc_attr( $this->question_meta_key ); ?>[<?php echo (int) $question_id; ?>][post_title]" value="<?php echo esc_attr( $question_title ); ?>" placeholder="<?php esc_attr_e( 'Enter Your Question Name ', 'bluedolphin-lms' ); ?>">
					</div>
					<div class="bdlms-answer-type">
						<label for="answers_field">
							<?php esc_html_e( 'Select Answer Type', 'bluedolphin-lms' ); ?>
						</label>
						<select name="<?php echo esc_attr( $this->question_meta_key ); ?>[<?php echo (int) $question_id; ?>][type]">
							<option value="true_or_false"<?php selected( 'true_or_false', $qtype ); ?>><?php esc_html_e( 'True Or False ', 'bluedolphin-lms' ); ?></option>
							<option value="multi_choice"<?php selected( 'multi_choice', $qtype ); ?>><?php esc_html_e( 'Multi Choice ', 'bluedolphin-lms' ); ?></option>
							<option value="single_choice"<?php selected( 'single_choice', $qtype ); ?>><?php esc_html_e( 'Single Choice ', 'bluedolphin-lms' ); ?></option>
							<option value="fill_blank"<?php selected( 'fill_blank', $qtype ); ?>><?php esc_html_e( 'Fill In Blanks ', 'bluedolphin-lms' ); ?></option>
						</select>
					</div>

					<div class="bdlms-answer-group true_or_false<?php echo 'true_or_false' !== $qtype ? ' hidden' : ''; ?>">
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
														<input type="text" class="bdlms-option-value-input" value="<?php echo esc_attr( $answer ); ?>" name="<?php echo esc_attr( $this->question_meta_key ); ?>[<?php echo (int) $question_id; ?>][true_or_false][]" readonly>
													</div>
												</li>
												<li class="bdlms-option-check-td">
													<input type="radio" value="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $this->question_meta_key ); ?>[<?php echo (int) $question_id; ?>][true_or_false_answers]"<?php checked( wp_hash( $answer ), $corret_answers ); ?>>
												</li>
											</ul>
										<?php endforeach; ?>
									</div>
								</div>
							</div>
					</div>

					<div class="bdlms-answer-group multi_choice<?php echo 'multi_choice' !== $qtype ? ' hidden' : ''; ?>">
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
														<input type="text" value="<?php echo esc_attr( $answer ); ?>" name="<?php echo esc_attr( $this->question_meta_key ); ?>[<?php echo (int) $question_id; ?>][multi_choice][]">
													</div>
												</li>
												<li class="bdlms-option-check-td">
													<input type="checkbox" value="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $this->question_meta_key ); ?>[<?php echo (int) $question_id; ?>][multi_choice_answers][]"<?php echo in_array( wp_hash( $answer ), $corret_answers, true ) ? ' checked' : ''; ?>>
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

					<div class="bdlms-answer-group single_choice<?php echo 'single_choice' !== $qtype ? ' hidden' : ''; ?>">
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
														<input type="text" value="<?php echo esc_attr( $answer ); ?>" name="<?php echo esc_attr( $this->question_meta_key ); ?>[<?php echo (int) $question_id; ?>][single_choice][]">
													</div>
												</li>
												<li class="bdlms-option-check-td">
													<input type="radio" value="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $this->question_meta_key ); ?>[<?php echo (int) $question_id; ?>][single_choice_answers]"<?php checked( wp_hash( $answer ), $corret_answers ); ?>>
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

					<div class="bdlms-answer-group fill_blank<?php echo 'fill_blank' !== $qtype ? ' hidden' : ''; ?>">
						<?php
							$mandatory_answers = isset( $data['mandatory_answers'] ) ? $data['mandatory_answers'] : '';
							$optional_answers  = ! empty( $data['optional_answers'] ) ? $data['optional_answers'] : array_fill( 0, 4, '' );
						?>
						<div class="bdlms-add-accepted-answers">
							<h3><?php esc_html_e( 'Add Accepted Answers', 'bluedolphin-lms' ); ?></h3>
							<ul>
								<li>
									<label><?php esc_html_e( 'Mandatory', 'bluedolphin-lms' ); ?></label>
									<input type="text" name="<?php echo esc_attr( $this->question_meta_key ); ?>[<?php echo (int) $question_id; ?>][mandatory_answers]" value="<?php echo esc_attr( $mandatory_answers ); ?>">
								</li>
								<?php foreach ( $optional_answers as $optional_answer ) : ?>
									<li>
										<label><?php esc_html_e( 'Optional', 'bluedolphin-lms' ); ?></label>
										<input type="text" name="<?php echo esc_attr( $this->question_meta_key ); ?>[<?php echo (int) $question_id; ?>][optional_answers][]" value="<?php echo esc_attr( $optional_answer ); ?>">
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>

					<div class="bdlms-add-option hidden">
						<button type="button"
							class="button bdlms-add-answer"><?php esc_html_e( 'Add More Options', 'bluedolphin-lms' ); ?></button>
					</div>
				</div>
				<div class="bdlms-qus-setting-wrap">
					<div class="bdlms-answer-type">
						<label for="answers_field">
							<?php esc_html_e( 'Question Settings', 'bluedolphin-lms' ); ?>
						</label>
					</div>
					<?php do_action( 'bdlms_question_setting_fields_before', $settings, $question_id, $this ); ?>
					<div class="bdlms-qus-setting-header">
						<div>
							<label for="points_field">
								<?php esc_html_e( 'Marks/Points: ', 'bluedolphin-lms' ); ?>
							</label>
							<input type="number" class="bdlms-question-points" name="<?php echo esc_attr( $this->question_meta_key ); ?>[<?php echo (int) $question_id; ?>][settings][points]" value="<?php echo isset( $settings['points'] ) ? (int) $settings['points'] : 1; ?>" step="1" min="1">
						</div>
						<div>
							<label for="levels_field">
								<?php esc_html_e( 'Difficulty Level', 'bluedolphin-lms' ); ?>
							</label>
							<select name="<?php echo esc_attr( $this->question_meta_key ); ?>[<?php echo (int) $question_id; ?>][settings][levels]">
								<?php
								foreach ( \BlueDolphin\Lms\question_levels() as $key => $level ) {
									?>
								<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $levels, $key ); ?>>
									<?php echo esc_html( $level ); ?></option>
									<?php
								}
								?>
							</select>
						</div>
						<div>
							<label><input type="checkbox" name="<?php echo esc_attr( $this->question_meta_key ); ?>[<?php echo (int) $question_id; ?>][settings][status]" value="1"<?php checked( $qstatus, 1 ); ?>><?php esc_html_e( 'Hide Question? ', 'bluedolphin-lms' ); ?> </label>
						</div>
					</div>
					<div class="bdlms-qus-setting-body">
						<h3><?php esc_html_e( 'Show Feedback/Hint ', 'bluedolphin-lms' ); ?></h3>

						<div class="bdlms-hint-box">
							<label for="hint_field">
								<?php esc_html_e( 'Correctly Answered Feedback: ', 'bluedolphin-lms' ); ?>
								<div class="bdlms-tooltip">
									<svg class="icon" width="12" height="12">
										<use
											xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#help">
										</use>
									</svg>
									<span class="bdlms-tooltiptext">
										<?php esc_html_e( 'The instructions for the user to select the right answer. The text will be shown when users click the \'Hint\' button.', 'bluedolphin-lms' ); ?>
									</span>
								</div>
							</label>
							<textarea name="<?php echo esc_attr( $this->question_meta_key ); ?>[<?php echo (int) $question_id; ?>][settings][hint]"><?php echo isset( $hint ) ? esc_textarea( $hint ) : ''; ?></textarea>
						</div>
						<div class="bdlms-hint-box">
							<label for="explanation_field" style="color: #B20000;">
								<?php esc_html_e( 'Incorrectly Answered Feedback: ', 'bluedolphin-lms' ); ?>
								<div class="bdlms-tooltip">
									<svg class="icon" width="12" height="12">
										<use
											xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#help">
										</use>
									</svg>
									<span class="bdlms-tooltiptext">
										<?php esc_html_e( 'The explanation will be displayed when students click the "Check Answer" button.', 'bluedolphin-lms' ); ?>
									</span>
								</div>
							</label>
							<textarea name="<?php echo esc_attr( $this->question_meta_key ); ?>[<?php echo (int) $question_id; ?>][settings][explanation]"><?php echo isset( $explanation ) ? esc_textarea( $explanation ) : ''; ?></textarea>
						</div>

						<div class="bdlms-add-option">
							<button type="button" class="button button-primary bdlms-save-questions" data-post_id="<?php echo (int) $question_id; ?>"><?php esc_html_e( 'Save', 'bluedolphin-lms' ); ?></button>
							<button type="button" class="button bdlms-cancel-edit"><?php esc_html_e( 'Cancel', 'bluedolphin-lms' ); ?></button>
							<span class="spinner"></span>
						</div>
					</div>
					<?php do_action( 'bdlms_question_setting_fields_after', $settings, $question_id, $this ); ?>
				</div>
			</div>
			<div class="bdlms-quiz-qus-item__footer">
				<a href="javascript:;" data-accordion="true">
					<svg class="icon" width="12" height="12">
						<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#edit"></use>
					</svg>
					<?php esc_html_e( 'Edit', 'bluedolphin-lms' ); ?>
				</a>
				<a href="javascript:;" class="bdlms-duplicate-link">
					<svg class="icon" width="12" height="12">
						<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#duplicate"></use>
					</svg>
					<?php esc_html_e( 'Duplicate', 'bluedolphin-lms' ); ?>
				</a>
				<a href="javascript:;" class="bdlms-delete-link">
					<svg class="icon" width="12" height="12">
						<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#delete"></use>
					</svg>
					<?php esc_html_e( 'Remove', 'bluedolphin-lms' ); ?>
				</a>
			</div>
		</div>
	</li>
<?php endforeach; ?>