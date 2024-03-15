<?php
/**
 * Template: Answer Options Metabox.
 *
 * @package BlueDolphin\Lms\Admin
 */

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