<?php
/**
 * Template: Course Curriculum - Quiz.
 *
 * @package BlueDolphin\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended
 */

$curriculum      = isset( $args['curriculum'] ) ? $args['curriculum'] : array();
$item_id         = isset( $curriculum['item_id'] ) ? $curriculum['item_id'] : 0;
$questions       = isset( $curriculum['questions'] ) ? $curriculum['questions'] : array();
$total_duration  = \BlueDolphin\Lms\count_duration( $curriculum );
$duration_str    = \BlueDolphin\Lms\seconds_to_hours_str( $total_duration );
$duration_str    = ! empty( $duration_str ) ? trim( $duration_str ) : '';
$total_questions = count( $questions );
?>

<div class="bdlms-lesson-view__body">
	<div class="bdlms-quiz-view">
		<div id="smartwizard">
			<ul class="nav">
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
						$question_type = get_post_meta( $question, \BlueDolphin\Lms\META_KEY_QUESTION_TYPE, true );
						?>
				<div id="step-<?php echo (int) $question_index; ?>" class="tab-pane" role="tabpanel" aria-labelledby="step-<?php echo (int) $question_index; ?>">
					<div class="bdlms-quiz-view-content">
						<div class="bdlms-quiz-question">
							<div class="qus-no"><?php printf( esc_html__( 'Question %1$d/%2$d', 'bluedolphin-lms' ), (int) $current_index + 1, (int) $total_questions ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment ?></div>
							<h3><?php echo esc_html( get_the_title( $question ) ); ?></h3>
							<div class="bdlms-quiz-option-list">
								<ul>
									<li>
										<label>
											<input type="checkbox" class="bdlms-check">
											Option 1
										</label>
									</li>
									<li>
										<label>
											<input type="checkbox" class="bdlms-check" checked>
											Option 2
										</label>
									</li>
									<li>
										<label>
											<input type="checkbox" class="bdlms-check">
											Option 3
										</label>
									</li>
									<li>
										<label>
											<input type="checkbox" class="bdlms-check">
											Option 4
										</label>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
				<?php endforeach; ?>
				<?php endif; ?>
				<div id="step-<?php echo (int) $question_index + 1; ?>" class="tab-pane" role="tabpanel" aria-labelledby="step-<?php echo (int) $question_index + 1; ?>">
					<div class="bdlms-quiz-complete">
						<img src="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/success-check.svg" alt="">
						<h3>Quiz Completed</h3>
						<p>Great Job reaching your goal!</p>
						<div class="bdlms-quiz-result-list">
							<div class="bdlms-quiz-result-item">
								<p>Grade</p>
								<span>10%</span>
							</div>
							<div class="bdlms-quiz-result-item">
								<p>Accuracy</p>
								<span>2/5</span>
							</div>
							<div class="bdlms-quiz-result-item">
								<p>Time</p>
								<span>15 mins</span>
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
			</svg> <span class="bdlms-quiz-countdown" data-timestamp="<?php echo esc_attr( $total_duration ); ?>"></span>
		</div>
	</div>
	<div class="right">
		<button class="bdlms-btn bdlms-next-wizard"><?php esc_html_e( 'Continue', 'bluedolphin-lms' ); ?></button>
	</div>
</div>