<?php
/**
 * Javascript templates.
 *
 * @package BD\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$alphabets       = \BD\Lms\question_series();
$meta_key_prefix = \BD\Lms\META_KEY_QUESTION_PREFIX;
?>
<script type="text/template" id="show_answer">
	<?php wp_nonce_field( 'inlineeditnonce', '_inline_edit', false ); ?>
	<input type="hidden" name="_status" value="">
	<input type="hidden" name="screen" value="edit-<?php echo esc_attr( \BD\Lms\BDLMS_QUESTION_CPT ); ?>">
	<input type="hidden" name="post_view" value="list">
	<td colspan="8" class="colspanchange">
		<div class="inline-edit-wrapper" role="region" aria-labelledby="quick-edit-legend">
			<fieldset class="inline-edit-col-left">
				<div class="bdlms-show-ans-wrap">
					<div class="bdlms-show-ans-header">
						<legend class="inline-edit-legend"><?php esc_html_e( 'Show Answers', 'bluedolphin-lms' ); ?></legend>
						<div>
							<label><?php esc_html_e( 'Type:', 'bluedolphin-lms' ); ?></label>
							<select name="<?php echo esc_attr( $meta_key_prefix ); ?>[type]" id="bdlms_answer_type">
								<option value="true_or_false"><?php esc_html_e( 'True Or False ', 'bluedolphin-lms' ); ?></option>
								<option value="multi_choice"><?php esc_html_e( 'Multi Choice ', 'bluedolphin-lms' ); ?></option>
								<option value="single_choice"><?php esc_html_e( 'Single Choice ', 'bluedolphin-lms' ); ?></option>
								<option value="fill_blank"><?php esc_html_e( 'Fill In Blanks ', 'bluedolphin-lms' ); ?></option>
							</select>
						</div>
					</div>
					<div class="bdlms-show-ans-title-marks">
						<div>
							<label><?php esc_html_e( 'Title', 'bluedolphin-lms' ); ?></label>
							<input type="text" name="post_title">
						</div>
						<div class="marks-input">
							<label><?php esc_html_e( 'Marks', 'bluedolphin-lms' ); ?></label>
							<input type="number" name="<?php echo esc_attr( $meta_key_prefix ); ?>[settings][points]" step="1" min="0">
						</div>
					</div>

					<div class="bdlms-options-table hidden" id="multi_choice">
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
									$answers = array_fill( 0, 4, '' );
								?>
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
												<div class="bdlms-options-no"><?php echo esc_html( sprintf( '%s.', isset( $alphabets[ $key ] ) ? $alphabets[ $key ] : '' ) ); ?></div>
												<input type="text" value="<?php echo esc_attr( $answer ); ?>" name="<?php echo esc_attr( $meta_key_prefix ); ?>[multi_choice][]">
											</div>
										</li>
										<li class="bdlms-option-check-td">
											<input type="checkbox" value="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $meta_key_prefix ); ?>[multi_choice_answers][]">
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

					<div class="bdlms-options-table hidden" id="true_or_false">
						<div class="bdlms-options-table__header">
							<ul class="bdlms-options-table__list">
								<li><?php esc_html_e( 'Options', 'bluedolphin-lms' ); ?></li>
								<li class="bdlms-option-check-td"><?php esc_html_e( 'Correct Option', 'bluedolphin-lms' ); ?></li>
							</ul>
						</div>
						<div class="bdlms-options-table__body bdlms-sortable-answers">
							<div class="bdlms-options-table__list-wrap">
								<?php
								$answers = array(
									0 => __( 'True', 'bluedolphin-lms' ),
									1 => __( 'False', 'bluedolphin-lms' ),
								);
								?>
								<?php foreach ( $answers as $key => $answer ) : ?>
									<ul class="bdlms-options-table__list">
										<li>
											<div class="bdlms-options-value">
												<div class="bdlms-options-drag">
													<svg class="icon" width="8" height="13">
														<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
													</svg>
												</div>
												<input type="text" class="bdlms-option-value-input" value="<?php echo esc_attr( $answer ); ?>" name="<?php echo esc_attr( $meta_key_prefix ); ?>[true_or_false][]" readonly>
											</div>
										</li>
										<li class="bdlms-option-check-td">
											<input type="radio" value="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $meta_key_prefix ); ?>[true_or_false_answers]">
										</li>
									</ul>
								<?php endforeach; ?>
							</div>
						</div>
					</div>

					<div class="bdlms-options-table hidden" id="single_choice">
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
									$answers = array_fill( 0, 4, '' );
								?>
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
												<div class="bdlms-options-no"><?php echo esc_html( sprintf( '%s.', isset( $alphabets[ $key ] ) ? $alphabets[ $key ] : '' ) ); ?></div>
												<input type="text" value="<?php echo esc_attr( $answer ); ?>" name="<?php echo esc_attr( $meta_key_prefix ); ?>[single_choice][]">
											</div>
										</li>
										<li class="bdlms-option-check-td">
											<input type="radio" value="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $meta_key_prefix ); ?>[single_choice_answers]">
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

					<div class="bdlms-add-accepted-answers hidden" id="fill_blank">
						<h3><?php esc_html_e( 'Add Accepted Answers', 'bluedolphin-lms' ); ?></h3>
						<ul>
							<li>
								<label><?php esc_html_e( 'Mandatory', 'bluedolphin-lms' ); ?></label>
								<input type="text" name="<?php echo esc_attr( $meta_key_prefix ); ?>[mandatory_answers]" value="">
							</li>
							<li>
								<label><?php esc_html_e( 'Optional', 'bluedolphin-lms' ); ?></label>
								<input type="text" name="<?php echo esc_attr( $meta_key_prefix ); ?>[optional_answers]" value="">
							</li>
							<li>
								<label><?php esc_html_e( 'Optional', 'bluedolphin-lms' ); ?></label>
								<input type="text" name="<?php echo esc_attr( $meta_key_prefix ); ?>[optional_answers]" value="">
							</li>
							<li>
								<label><?php esc_html_e( 'Optional', 'bluedolphin-lms' ); ?></label>
								<input type="text" name="<?php echo esc_attr( $meta_key_prefix ); ?>[optional_answers]" value="">
							</li>
							<li>
								<label><?php esc_html_e( 'Optional', 'bluedolphin-lms' ); ?></label>
								<input type="text" name="<?php echo esc_attr( $meta_key_prefix ); ?>[optional_answers]" value="">
							</li>
						</ul>
					</div>

					<div class="bdlms-show-ans-action">
						<button type="button" class="button bdlms-add-answer"><?php esc_html_e( 'Add a New Answer', 'bluedolphin-lms' ); ?></button>
						<button type="button" class="button bdlms-cancel-answer"><?php esc_html_e( 'Cancel', 'bluedolphin-lms' ); ?></button>
						<button type="button" class="button button-primary bdlms-save-answer"><?php esc_html_e( 'Save', 'bluedolphin-lms' ); ?></button>
						<span class="spinner"></span>
					</div>
				</div>
			</fieldset>
		</div>
	</td>
</script>

<script type="text/template" id="true_or_false_option">
	<ul class="bdlms-options-table__list">
		<li>
			<div class="bdlms-options-value">
				<div class="bdlms-options-drag">
					<svg class="icon" width="8" height="13">
						<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
					</svg>
				</div>
				<input type="text" value="{{VALUE}}" class="bdlms-option-value-input" name="<?php echo esc_attr( $meta_key_prefix ); ?>[true_or_false][]" readonly>
			</div>
		</li>
		<li class="bdlms-option-check-td">
			<input type="radio" value="{{ANSWER_ID}}" name="<?php echo esc_attr( $meta_key_prefix ); ?>[true_or_false_answers]" {{checked}}>
		</li>
	</ul>
</script>

<script type="text/template" id="multi_choice_option">
	<ul class="bdlms-options-table__list">
		<li>
			<div class="bdlms-options-value">
				<div class="bdlms-options-drag">
					<svg class="icon" width="8" height="13">
						<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
					</svg>
				</div>
				<div class="bdlms-options-no">{{OPTION_NO}}.</div>
				<input type="text" value="{{VALUE}}" name="<?php echo esc_attr( $meta_key_prefix ); ?>[multi_choice][]">
			</div>
		</li>
		<li class="bdlms-option-check-td">
			<input type="checkbox" value="{{ANSWER_ID}}" name="<?php echo esc_attr( $meta_key_prefix ); ?>[multi_choice_answers][]" {{checked}}>
		</li>
		<li class="bdlms-option-action">
			<button type="button" class="bdlms-remove-answer">
				<svg class="icon" width="12" height="12">
					<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#trash"></use>
				</svg>
			</button>
		</li>
	</ul>
</script>

<script type="text/template" id="single_choice_option">
	<ul class="bdlms-options-table__list">
		<li>
			<div class="bdlms-options-value">
				<div class="bdlms-options-drag">
					<svg class="icon" width="8" height="13">
						<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
					</svg>
				</div>
				<div class="bdlms-options-no">{{OPTION_NO}}.</div>
				<input type="text" value="{{VALUE}}" name="<?php echo esc_attr( $meta_key_prefix ); ?>[single_choice][]">
			</div>
		</li>
		<li class="bdlms-option-check-td">
			<input type="radio" value="{{ANSWER_ID}}" name="<?php echo esc_attr( $meta_key_prefix ); ?>[single_choice_answers]" {{checked}}>
		</li>
		<li class="bdlms-option-action">
			<button type="button" class="bdlms-remove-answer">
				<svg class="icon" width="12" height="12">
					<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#trash"></use>
				</svg>
			</button>
		</li>
	</ul>
</script>

<script type="text/template" id="fill_blank_mandatory">
	<li>
		<label><?php esc_html_e( 'Mandatory', 'bluedolphin-lms' ); ?></label>
		<input type="text" name="<?php echo esc_attr( $meta_key_prefix ); ?>[mandatory_answers]" value="{{VALUE}}">
	</li>
</script>

<script type="text/template" id="fill_blank_optional">
	<li>
		<label><?php esc_html_e( 'Optional', 'bluedolphin-lms' ); ?></label>
		<input type="text" name="<?php echo esc_attr( $meta_key_prefix ); ?>[optional_answers][]" value="{{VALUE}}">
	</li>
</script>
