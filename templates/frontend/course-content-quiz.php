<?php
/**
 * Template: Course Curriculum - Quiz.
 *
 * @package BlueDolphin\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended
 */

$curriculum     = isset( $args['curriculum'] ) ? $args['curriculum'] : array();
$item_id        = isset( $curriculum['item_id'] ) ? $curriculum['item_id'] : 0;
$questions      = isset( $curriculum['questions'] ) ? $curriculum['questions'] : array();
$total_duration = \BlueDolphin\Lms\count_duration( $curriculum );
$duration_str   = \BlueDolphin\Lms\seconds_to_hours_str( $total_duration );
$duration_str   = ! empty( $duration_str ) ? trim( $duration_str ) : '';
shuffle( $questions );
$total_questions = count( $questions );
?>

<div class="bdlms-lesson-view__body">
	<div class="bdlms-quiz-view">
		<div id="smartwizard">
			<ul class="nav" style="display:none;">
				<li class="nav-item">
					<a class="nav-link" href="#step-1">
						<div class="num">1</div>
						<?php
							// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
							printf( esc_html__( 'Step %d', 'bluedolphin-lms' ), 1 );
						?>
					</a>
				</li>
				<?php
				$question_index = 1;
				if ( ! empty( $questions ) ) :
					foreach ( $questions as $question ) :
						++$question_index;
						?>
						<li class="nav-item">
							<a class="nav-link" href="#step-<?php echo (int) $question_index; ?>">
								<div class="num"><?php echo (int) $question_index; ?></div>
								<?php
								// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
								printf( esc_html__( 'Step %d', 'bluedolphin-lms' ), (int) $question_index );
								?>
							</a>
						</li>
					<?php endforeach; ?>
				<?php endif; ?>
				<li class="nav-item">
					<a class="nav-link" href="#step-<?php echo (int) $question_index + 1; ?>">
						<div class="num"><?php echo (int) $question_index + 1; ?></div>
						<?php
							// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
							printf( esc_html__( 'Step %d', 'bluedolphin-lms' ), (int) $question_index + 1 );
						?>
					</a>
				</li>
			</ul>
			<div class="tab-content">
				<div id="step-1" class="tab-pane" role="tabpanel" aria-labelledby="step-1">
					<div class="bdlms-quiz-view-content">
						<div class="bdlms-quiz-start">
							<h3><?php echo esc_html( get_the_title( $item_id ) ); ?></h3>
							<div class="info">
								<span>
									<?php
										printf(
											// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment, WordPress.Security.EscapeOutput.OutputNotEscaped
											_n( ' %s Question', ' %s Questions', $total_questions, 'bluedolphin-lms' ),
											$total_questions // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										);
										?>
								</span>
								<span><?php echo esc_html( $duration_str ); ?></span>
							</div>
							<button class="bdlms-btn bdlms-next-wizard"<?php disabled( true, empty( $questions ) ); ?>><?php esc_html_e( 'Let’s Start', 'bluedolphin-lms' ); ?></button>
						</div>
					</div>
				</div>
				<?php
				$question_index = 1;
				if ( ! empty( $questions ) ) :
					foreach ( $questions as $current_index => $question ) :
						++$question_index;
						$question_type  = get_post_meta( $question, \BlueDolphin\Lms\META_KEY_QUESTION_TYPE, true );
						$questions_list = \BlueDolphin\Lms\get_question_by_type( $question, $question_type );
						?>
				<div id="step-<?php echo (int) $question_index; ?>" class="tab-pane" role="tabpanel" aria-labelledby="step-<?php echo (int) $question_index; ?>">
					<div class="bdlms-quiz-view-content">
						<div class="bdlms-quiz-question">
							<div class="qus-no"><?php printf( esc_html__( 'Question %1$d/%2$d', 'bluedolphin-lms' ), (int) $current_index + 1, (int) $total_questions ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment ?></div>
							<h3><?php echo esc_html( get_the_title( $question ) ); ?></h3>
							<?php
							if ( ! empty( $questions_list[ $question_type ] ) && is_array( $questions_list[ $question_type ] ) ) :
								$answers = $questions_list[ $question_type ];
								shuffle( $answers );
								?>
								<div class="bdlms-quiz-option-list">
									<ul>
										<?php foreach ( $answers as $answer ) : ?>
											<li>
												<label>
													<?php if ( in_array( $question_type, array( 'true_or_false', 'single_choice' ), true ) ) : ?>
														<input type="radio" name="bdlms_answers[<?php echo (int) $question; ?>]" class="bdlms-check" value="<?php echo esc_attr( wp_hash( trim( $answer ) ) ); ?>">
													<?php else : ?>
														<input type="checkbox" name="bdlms_answers[<?php echo (int) $question; ?>][]" class="bdlms-check"  value="<?php echo esc_attr( wp_hash( trim( $answer ) ) ); ?>">
													<?php endif; ?>
													<?php echo esc_html( trim( $answer ) ); ?>
												</label>
											</li>
										<?php endforeach; ?>
									</ul>
								</div>
							<?php elseif ( 'fill_blank' === $question_type ) : ?>
								<div class="bdlms-quiz-input-ans">
									<div class="bdlms-form-group">
										<label class="bdlms-form-label"><?php esc_html_e( 'Your Answer', 'bluedolphin-lms' ); ?></label>
										<input type="text" name="bdlms_written_answer[<?php echo (int) $question; ?>]" class="bdlms-form-control" placeholder="<?php esc_attr_e( 'Enter Your thoughts here...', 'bluedolphin-lms' ); ?>">
									</div>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
				<?php endforeach; ?>
				<?php endif; ?>
				<div id="step-<?php echo (int) $question_index + 1; ?>" class="tab-pane" role="tabpanel" aria-labelledby="step-<?php echo (int) $question_index + 1; ?>">
					<div class="bdlms-quiz-complete">
						<img src="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/success-check.svg" alt="success check">
						<h3><?php esc_html_e( 'Quiz Completed', 'bluedolphin-lms' ); ?></h3>
						<p><?php esc_html_e( 'Great Job reaching your goal!', 'bluedolphin-lms' ); ?></p>
						<div class="bdlms-quiz-result-list">
							<div class="bdlms-quiz-result-item">
								<p><?php esc_html_e( 'Grade', 'bluedolphin-lms' ); ?></p>
								<span id="grade"></span>
							</div>
							<div class="bdlms-quiz-result-item">
								<p><?php esc_html_e( 'Accuracy', 'bluedolphin-lms' ); ?></p>
								<span id="accuracy"></span>
							</div>
							<div class="bdlms-quiz-result-item">
								<p><?php esc_html_e( 'Time', 'bluedolphin-lms' ); ?></p>
								<span id="time"></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="bdlms-lesson-view__footer">
	<div class="left">
		<div class="bdlms-quiz-timer">
			<svg class="icon-cross" width="16" height="16">
				<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#stopwatch"></use>
			</svg> <span class="bdlms-quiz-countdown" id="bdlms_quiz_countdown" data-total_questions="<?php echo esc_attr( $total_questions ); ?>" data-timestamp="<?php echo esc_attr( $total_duration ); ?>"></span>
		</div>
	</div>
	<div class="right">
		<?php if ( ! empty( $curriculum['settings']['show_correct_review'] ) ) : ?>
			<button class="bdlms-btn bdlms-check-answer" disabled><?php esc_html_e( 'Check Answer', 'bluedolphin-lms' ); ?></button>
		<?php endif; ?>
		<button class="bdlms-btn bdlms-next-wizard"><?php esc_html_e( 'Continue', 'bluedolphin-lms' ); ?></button>
	</div>
</div>