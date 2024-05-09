<?php
/**
 * Template: Course Curriculum - Text.
 *
 * @package BlueDolphin\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended
 */

$content = isset( $args['curriculum']['media']['text'] ) ? $args['curriculum']['media']['text'] : '';
?>
<div class="bdlms-lesson-view__body">
	<div class="bdlms-quiz-view">
		<div class="bdlms-quiz-content">
			<?php
				echo wp_kses_post( $content );
			?>
		</div>
	</div>
</div>
