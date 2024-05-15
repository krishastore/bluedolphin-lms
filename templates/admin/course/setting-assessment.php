<?php
/**
 * Template: Course setting - Assessment.
 *
 * @package BlueDolphin\Lms
 */

?>
<div class="bdlms-tab-content<?php echo esc_attr( $active_class ); ?>" data-tab="assessment">
	<div class="bdlms-cs-row">
		<div class="bdlms-cs-col-left"><?php esc_html_e( 'Evaluation', 'bluedolphin-lms' ); ?></div>
		<div class="bdlms-cs-col-right">
			<div class="bdlms-cs-drag-list">
				<ul class="cs-drag-list">
					<?php foreach ( BlueDolphin\Lms\bdlms_evaluation_list( $last_quiz ) as $key => $evaluation ) : ?>
						<li>
							<label><input type="radio" value="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[assessment][evaluation]"<?php checked( $key, $assessment['evaluation'] ); ?>> <?php echo isset( $evaluation['label'] ) ? esc_html( $evaluation['label'] ) : ''; ?></label>
							<?php if ( ! empty( $evaluation['notice'] ) ) : ?>
								<div class="bdlms-cs-passing-grade">
									<?php
									echo wp_kses(
										$evaluation['notice'],
										array(
											'a' => array(
												'href'   => true,
												'target' => true,
												'class'  => true,
											),
										)
									);
									?>
								</div>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
	<div class="bdlms-cs-row cs-passing-grade<?php echo 2 === $assessment['evaluation'] ? ' hidden' : ''; ?>">
		<div class="bdlms-cs-col-left"><?php esc_html_e( 'Passing Grade', 'bluedolphin-lms' ); ?> (%)</div>
		<div class="bdlms-cs-col-right">
			<div class="bdlms-cs-drag-list">
				<ul class="cs-drag-list">
					<li>
						<input type="number" value="<?php echo esc_attr( $assessment['passing_grade'] ); ?>" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[assessment][passing_grade]" min="0" max="100" step="1">
					</li>
					<li><?php esc_html_e( 'The conditions that must be achieved to finish the course.', 'bluedolphin-lms' ); ?></li>
				</ul>
			</div>
		</div>
	</div>
</div>