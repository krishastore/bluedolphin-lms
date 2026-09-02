<?php
/**
 * Template: Course setting - Assessment.
 *
 * @package ST\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="stlms-tab-content<?php echo esc_attr( $active_class ); ?>" data-tab="assessment">
	<div class="stlms-cs-row">
		<div class="stlms-cs-col-left"><?php esc_html_e( 'Evaluation', 'skilltriks' ); ?></div>
		<div class="stlms-cs-col-right">
			<div class="stlms-cs-drag-list">
				<ul class="cs-drag-list">
					<?php foreach ( ST\Lms\stlms_evaluation_list( $last_quiz ) as $key => $evaluation ) : ?>
						<li>
							<label><input type="radio" value="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[assessment][evaluation]"<?php checked( $key, $assessment['evaluation'] ); ?>> <?php echo isset( $evaluation['label'] ) ? esc_html( $evaluation['label'] ) : ''; ?></label>
							<?php if ( ! empty( $evaluation['notice'] ) ) : ?>
								<div class="stlms-cs-passing-grade">
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
	<div class="stlms-cs-row cs-passing-grade<?php echo 2 === $assessment['evaluation'] ? ' hidden' : ''; ?>">
		<div class="stlms-cs-col-left"><?php esc_html_e( 'Passing Grade', 'skilltriks' ); ?> (%)</div>
		<div class="stlms-cs-col-right">
			<div class="stlms-cs-drag-list">
				<ul class="cs-drag-list">
					<li>
						<input type="number" value="<?php echo esc_attr( $assessment['passing_grade'] ); ?>" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[assessment][passing_grade]" min="0" max="100" step="1">
					</li>
					<li><?php esc_html_e( 'The conditions that must be achieved to finish the course.', 'skilltriks' ); ?></li>
				</ul>
			</div>
		</div>
	</div>
</div>
