<?php
/**
 * Template: Course Curriculum - Quiz.
 *
 * @package ST\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended,PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$curriculum     = isset( $args['curriculum'] ) ? $args['curriculum'] : array();
$item_id        = isset( $curriculum['item_id'] ) ? $curriculum['item_id'] : 0;
$questions      = ! empty( $curriculum['questions'] ) ? $curriculum['questions'] : array();
$total_duration = \ST\Lms\count_duration( $curriculum );
$duration_str   = \ST\Lms\seconds_to_hours_str( $total_duration );
$duration_str   = ! empty( $duration_str ) ? trim( $duration_str ) : '';
shuffle( $questions );
$total_questions = count( $questions );
?>

<div class="stlms-lesson-view__body">
	<div class="stlms-quiz-view">
		<div id="smartwizard">
			<ul class="nav" style="display:none;">
				<li class="nav-item">
					<a class="nav-link" href="#step-1">
						<div class="num">1</div>
						<?php
							// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
							echo esc_html( sprintf( __( 'Step %d', 'skilltriks' ), 1 ) );
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
							<a class="nav-link" href="#step-<?php echo esc_attr( (string) $question_index ); ?>">
								<div class="num"><?php echo esc_html( (string) $question_index ); ?></div>
								<?php
								// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
								echo esc_html( sprintf( __( 'Step %s', 'skilltriks' ), $question_index ) );
								?>
							</a>
						</li>
					<?php endforeach; ?>
				<?php endif; ?>
				<li class="nav-item">
					<a class="nav-link" href="#step-<?php echo esc_attr( (string) ( $question_index + 1 ) ); ?>">
						<div class="num"><?php echo esc_html( (string) ( $question_index + 1 ) ); ?></div>
						<?php
							// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
							echo esc_html( sprintf( __( 'Step %s', 'skilltriks' ), $question_index + 1 ) );
						?>
					</a>
				</li>
			</ul>
			<div class="tab-content">
				<div id="step-1" class="tab-pane" role="tabpanel" aria-labelledby="step-1">
					<div class="stlms-quiz-view-content">
						<div class="stlms-quiz-start">
							<h3><?php echo esc_html( get_the_title( $item_id ) ); ?></h3>
							<div class="info">
								<span>
									<?php
									echo esc_html(
										sprintf(
											// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment, WordPress.Security.EscapeOutput.OutputNotEscaped
											_n( ' %s Question', ' %s Questions', (int) $total_questions, 'skilltriks' ),
											number_format_i18n( $total_questions ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										)
									);
									?>
								</span>
								<span><?php echo esc_html( $duration_str ); ?></span>
							</div>
							<button class="stlms-btn stlms-next-wizard"<?php disabled( true, empty( $questions ) ); ?>><?php esc_html_e( 'Let’s Start', 'skilltriks' ); ?></button>
						</div>
					</div>
				</div>
				<?php
				$question_index = 1;
				if ( ! empty( $questions ) ) :
					foreach ( $questions as $current_index => $question ) :
						++$question_index;
						$question_type  = get_post_meta( $question, \ST\Lms\META_KEY_QUESTION_TYPE, true );
						$questions_list = \ST\Lms\get_question_by_type( $question, $question_type );
						?>
				<div id="step-<?php echo esc_attr( (string) $question_index ); ?>" class="tab-pane" role="tabpanel" aria-labelledby="step-<?php echo esc_attr( (string) $question_index ); ?>">
					<div class="stlms-quiz-view-content">
						<div class="stlms-quiz-question">
							<div class="qus-no"><?php echo esc_html( sprintf( __( 'Question %1$s/%2$s', 'skilltriks' ), $current_index + 1, $total_questions ) ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment ?></div>
							<h3><?php echo esc_html( get_the_title( $question ) ); ?></h3>
							<?php
							if ( ! empty( $questions_list[ $question_type ] ) && is_array( $questions_list[ $question_type ] ) ) :
								$answers = $questions_list[ $question_type ];
								shuffle( $answers );
								?>
								<div class="stlms-quiz-option-list">
									<ul>
										<?php foreach ( $answers as $answer ) : ?>
											<li>
												<label>
													<?php if ( in_array( $question_type, array( 'true_or_false', 'single_choice' ), true ) ) : ?>
														<input type="radio" name="stlms_answers[<?php echo esc_attr( (string) $question ); ?>]" class="stlms-check" value="<?php echo esc_attr( wp_hash( trim( $answer ) ) ); ?>">
													<?php else : ?>
														<input type="checkbox" name="stlms_answers[<?php echo esc_attr( (string) $question ); ?>][]" class="stlms-check"  value="<?php echo esc_attr( wp_hash( trim( $answer ) ) ); ?>">
													<?php endif; ?>
													<?php echo esc_html( trim( $answer ) ); ?>
												</label>
											</li>
										<?php endforeach; ?>
									</ul>
								</div>
							<?php elseif ( 'fill_blank' === $question_type ) : ?>
								<div class="stlms-quiz-input-ans">
									<div class="stlms-form-group">
										<label class="stlms-form-label"><?php esc_html_e( 'Your Answer', 'skilltriks' ); ?></label>
										<input type="text" name="stlms_written_answer[<?php echo esc_attr( (string) $question ); ?>]" class="stlms-form-control" placeholder="<?php esc_attr_e( 'Enter Your thoughts here...', 'skilltriks' ); ?>">
									</div>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
				<?php endforeach; ?>
				<?php endif; ?>
				<div id="step-<?php echo esc_attr( (string) ( $question_index + 1 ) ); ?>" class="tab-pane" role="tabpanel" aria-labelledby="step-<?php echo esc_attr( (string) ( $question_index + 1 ) ); ?>">
					<div class="stlms-quiz-complete">
						<div class="quiz-passed-text" style="display: none;">
							<img src="<?php echo esc_url( STLMS_ASSETS ); ?>/images/success-check.svg" alt="passed check">
							<h3><?php esc_html_e( 'You have passed the quiz!', 'skilltriks' ); ?></h3>
							<p><?php esc_html_e( 'Great Job reaching your goal!', 'skilltriks' ); ?></p>
						</div>
						<div class="quiz-failed-text" style="display: none;">
							<img src="<?php echo esc_url( STLMS_ASSETS ); ?>/images/fail-icon.svg" alt="failed check">
							<h3><?php esc_html_e( 'Unfortunately, you didn\'t pass the quiz.', 'skilltriks' ); ?></h3>
							<p><?php esc_html_e( 'Better luck next time.', 'skilltriks' ); ?></p>
						</div>
						<div class="stlms-quiz-result-list">
							<div class="stlms-quiz-result-item">
								<p><?php esc_html_e( 'Correct answers', 'skilltriks' ); ?></p>
								<span id="grade"></span>
							</div>
							<div class="stlms-quiz-result-item">
								<p><?php esc_html_e( 'Attempted Questions', 'skilltriks' ); ?></p>
								<span id="accuracy"></span>
							</div>
							<div class="stlms-quiz-result-item">
								<p><?php esc_html_e( 'Time taken', 'skilltriks' ); ?></p>
								<span id="time"></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="stlms-lesson-view__footer">
	<div class="left">
		<div class="stlms-quiz-timer">
			<svg class="icon-cross" width="16" height="16">
				<use xlink:href="<?php echo esc_url( STLMS_ASSETS ); ?>/images/sprite-front.svg#stopwatch"></use>
			</svg> <span class="stlms-quiz-countdown" id="stlms_quiz_countdown" data-total_questions="<?php echo esc_attr( (string) $total_questions ); ?>" data-timestamp="<?php echo esc_attr( (string) $total_duration ); ?>"></span>
		</div>
	</div>
	<div class="right">
		<?php if ( ! empty( $curriculum['settings']['show_correct_review'] ) ) : ?>
			<button class="stlms-btn stlms-check-answer" disabled><?php esc_html_e( 'Check Answer', 'skilltriks' ); ?></button>
		<?php endif; ?>
		<button class="stlms-btn stlms-next-wizard"><?php esc_html_e( 'Continue', 'skilltriks' ); ?></button>
	</div>
</div>
