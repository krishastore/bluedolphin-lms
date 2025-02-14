<?php
/**
 * Template: Course Final Result Page
 *
 * @package BlueDolphin\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$course_id          = get_query_var( 'course_id', 0 );
$grade_percentage   = 0;
$curriculums        = get_post_meta( $course_id, \BlueDolphin\Lms\META_KEY_COURSE_CURRICULUM, true );
$assessment         = get_post_meta( $course_id, \BlueDolphin\Lms\META_KEY_COURSE_ASSESSMENT, true );
$completed_results  = \BlueDolphin\Lms\calculate_assessment_result( $assessment, $curriculums, $course_id );
$course_certificate = get_post_meta( $course_id, \BlueDolphin\Lms\META_KEY_COURSE_SIGNATURE, true );
$has_certificate    = isset( $course_certificate['certificate'] ) ? $course_certificate['certificate'] : 0;
// Get result value from array.
list( $passing_grade, $grade_percentage, $completed_on ) = $completed_results;

?>
<div class="bdlms-wrap">
	<div class="bdlms-lesson-view">
		<div class="bdlms-lesson-view__body">
			<div class="bdlms-quiz-view">
				<div class="course-result-box">
					<div class="bdlms-quiz-complete">
						<img src="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/certificate-<?php echo $grade_percentage >= $passing_grade ? 'pass' : 'fail'; ?>.svg" alt="">
						<?php if ( $grade_percentage >= $passing_grade ) : ?>
						<h3><?php esc_html_e( 'Congratulations on completing your course!', 'bluedolphin-lms' ); ?> 🎉</h3>
						<p>
							<?php
							esc_html_e(
								'You\'ve unlocked a world of knowledge and skill. Take a moment to celebrate your
							achievement',
								'bluedolphin-lms'
							);
							?>
						</p>
						<?php else : ?>
							<h3><?php esc_html_e( 'Unfortunately, This Time Wasn\'t Successful', 'bluedolphin-lms' ); ?></h3>
							<p>
							<?php
							esc_html_e(
								'Every Attempt Is A Step Forward!, Don\'t Be Discouraged,
								Keep Going!',
								'bluedolphin-lms'
							);
							?>
							</p>
						<?php endif; ?>
						<div class="bdlms-quiz-result-list bdlms-result-view">
							<div class="bdlms-quiz-result-item">
								<p class="bdlms-text-<?php echo $grade_percentage >= $passing_grade ? 'green' : 'red'; ?>"><?php echo esc_html( $grade_percentage ); ?>%</p>
								<span><?php esc_html_e( 'Your total Grade', 'bluedolphin-lms' ); ?></span>
							</div>
						</div>
						<div class="course-certificate">
							<?php if ( $grade_percentage >= $passing_grade ) : ?>
							<span>
								<?php
									// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
									printf( esc_html__( 'Certificate issued on %s Does not expire', 'bluedolphin-lms' ), esc_html( date_i18n( 'F d, Y', $completed_on ) ) );
								?>
							</span>
								<?php if ( $has_certificate ) : ?>
									<a href="javascript:;" id="download-certificate" data-course="<?php echo esc_attr( $course_id ); ?>"><?php esc_html_e( 'Get your Certificate', 'bluedolphin-lms' ); ?></a> <i class="bdlms-loader"></i>
									<?php
								endif;
							else :
								echo wp_kses(
									// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
									sprintf( __( '<a href="%s">Try Again</a>', 'bluedolphin-lms' ), esc_url( get_permalink( $course_id ) ) ),
									array(
										'a' => array(
											'href' => true,
										),
									)
								);
							endif;
							?>
						</div>
						<div class="course-result-title">
							<span><?php esc_html_e( 'Course', 'bluedolphin-lms' ); ?></span>
							<?php echo esc_html( get_the_title( $course_id ) ); ?>
						</div>
						<div class="bdlms-quiz-result-list bdlms-course-complete-result">
							<div class="bdlms-quiz-result-item">
								<p><?php echo (int) \BlueDolphin\Lms\calculate_assessment_result( $assessment, $curriculums, $course_id, 'lesson' ); ?>%</p>
								<span><?php esc_html_e( 'Lessons Completed', 'bluedolphin-lms' ); ?></span>
							</div>
							<div class="bdlms-quiz-result-item">
								<p><?php echo (int) \BlueDolphin\Lms\calculate_assessment_result( $assessment, $curriculums, $course_id, 'quiz' ); ?>%</p>
								<span><?php esc_html_e( 'Quiz Completed', 'bluedolphin-lms' ); ?></span>
							</div>
						</div>
						<div class="cta">
							<a href="<?php echo esc_url( \BlueDolphin\Lms\get_page_url( 'courses' ) ); ?>" class="bdlms-btn bdlms-btn-flate"><?php esc_html_e( 'Find More Courses', 'bluedolphin-lms' ); ?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>