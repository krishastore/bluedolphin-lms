<?php
/**
 * The file that defines the courses shortcode functionality.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BlueDolphin\Lms\Shortcode
 */

namespace BlueDolphin\Lms\Shortcode;

use BlueDolphin\Lms\ErrorLog as EL;
use BlueDolphin\Lms\Helpers\SettingOptions as Options;

/**
 * Shortcode register manage class.
 */
class Courses extends \BlueDolphin\Lms\Shortcode\Register implements \BlueDolphin\Lms\Interfaces\Courses {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->set_shortcode_tag( 'courses' );
		add_filter( 'template_include', array( $this, 'template_include' ) );
		add_action( 'template_redirect', array( $this, 'template_redirect' ) );
		add_action( 'bdlms_before_single_course', array( $this, 'fetch_course_data' ) );
		add_action( 'bdlms_after_single_course', array( $this, 'flush_course_data' ) );
		add_action( 'bdlms_single_course_action_bar', array( $this, 'single_course_action_bar' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'bdlms_after_single_course', array( $this, 'update_user_course_view_status' ), 15, 1 );
		add_action( 'wp_ajax_bdlms_check_answer', array( $this, 'quick_check_answer' ) );
		add_action( 'wp_ajax_nopriv_bdlms_check_answer', array( $this, 'quick_check_answer' ) );
		add_action( 'wp_ajax_bdlms_save_quiz_data', array( $this, 'save_quiz_data' ) );
		add_action( 'wp_ajax_nopriv_bdlms_save_quiz_data', array( $this, 'save_quiz_data' ) );
		add_action( 'wp_ajax_bdlms_download_course_certificate', array( $this, 'download_course_certificate' ) );
		add_action( 'bdlms_before_search_bar', array( $this, 'add_userinfo_before_search_bar' ) );
		add_action( 'wp_ajax_bdlms_enrol_course', array( $this, 'enrol_course' ) );
		$this->init();
	}

	/**
	 * Register shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 */
	public function register_shortcode( $atts ) {
		wp_enqueue_script( $this->handler );
		wp_enqueue_style( $this->handler );
		$args = shortcode_atts(
			array(
				'filter'     => 'yes',
				'pagination' => 'yes',
			),
			$atts,
			$this->shortcode_tag
		);
		ob_start();
		load_template( \BlueDolphin\Lms\locate_template( 'courses.php' ), false, $args );
		$content = ob_get_clean();
		return $content;
	}

	/**
	 * Filter courses single page template.
	 *
	 * @param string $template Template path.
	 * @return string
	 */
	public function template_include( $template ) {
		$is_block_theme = function_exists( 'wp_is_block_theme' ) && wp_is_block_theme();
		if ( is_singular( \BlueDolphin\Lms\BDLMS_COURSE_CPT ) ) {
			$suffix = '';
			if ( ! ( get_query_var( 'section' ) && get_query_var( 'item_id' ) ) ) {
				$suffix = '-detail';
			}
			$template_path = $is_block_theme ? "block-theme/single-courses$suffix.php" : "single-courses$suffix.php";
			$template      = \BlueDolphin\Lms\locate_template( $template_path );
		}
		$course_id = ! empty( get_query_var( 'course_id' ) ) ? (int) get_query_var( 'course_id' ) : 0;
		if ( $course_id ) {
			$template_path = $is_block_theme ? 'block-theme/courses-result.php' : 'courses-result.php';
			$template      = \BlueDolphin\Lms\locate_template( $template_path );
		}
		return $template;
	}

	/**
	 * Enqueue scripts.
	 */
	public function enqueue_scripts() {
		if ( ! empty( get_query_var( 'course_id' ) ) && ! is_404() ) {
			// Frontend.
			wp_enqueue_script( $this->handler );
			wp_enqueue_style( $this->handler );
			return;
		}
		if ( is_singular( \BlueDolphin\Lms\BDLMS_COURSE_CPT ) && ! ( get_query_var( 'section' ) && get_query_var( 'item_id' ) ) ) {
			// Swiper.
			wp_enqueue_script( $this->handler . '-swiper' );
			wp_enqueue_style( $this->handler . '-swiper' );
			// Frontend.
			wp_enqueue_script( $this->handler );
			wp_enqueue_style( $this->handler );
			return;
		}
		if ( ! is_singular( \BlueDolphin\Lms\BDLMS_COURSE_CPT ) ) {
			return;
		}
		// CountDownTimer.
		wp_enqueue_script( $this->handler . '-countdowntimer' );
		// Plyr.
		wp_enqueue_script( $this->handler . '-plyr' );
		wp_enqueue_style( $this->handler . '-plyr' );
		// SmartWizard.
		wp_enqueue_script( $this->handler . '-smartwizard' );
		wp_enqueue_style( $this->handler . '-smartwizard' );
		// Frontend.
		wp_enqueue_script( $this->handler );
		wp_enqueue_style( $this->handler );
	}

	/**
	 * Action bar.
	 *
	 * @param int $course_id Course ID.
	 */
	public function single_course_action_bar( $course_id ) {
		global $bdlms_course_data;
		$curriculums     = isset( $bdlms_course_data['curriculums'] ) ? $bdlms_course_data['curriculums'] : array();
		$curriculum_type = isset( $bdlms_course_data['current_curriculum']['media']['media_type'] ) ? $bdlms_course_data['current_curriculum']['media']['media_type'] : '';
		$current_item    = isset( $bdlms_course_data['current_curriculum']['item_id'] ) ? $bdlms_course_data['current_curriculum']['item_id'] : 0;
		load_template(
			\BlueDolphin\Lms\locate_template( 'action-bar.php' ),
			true,
			array(
				'course_id'       => $course_id,
				'curriculums'     => \BlueDolphin\Lms\merge_curriculum_items( $curriculums ),
				'current_item'    => $current_item,
				'curriculum_type' => $curriculum_type,
			)
		);
	}

	/**
	 * Fetch course data.
	 *
	 * @param int $course_id Course ID.
	 */
	public function fetch_course_data( $course_id ) {
		global $bdlms_course_data;
		$curriculums                      = get_post_meta( $course_id, \BlueDolphin\Lms\META_KEY_COURSE_CURRICULUM, true );
		$curriculums                      = ! empty( $curriculums ) ? $curriculums : array();
		$curriculums                      = array_map( '\BlueDolphin\Lms\get_curriculum_section_items', $curriculums );
		$current_curriculum               = \BlueDolphin\Lms\get_current_curriculum( $curriculums );
		$bdlms_course_data['curriculums'] = $curriculums;
		if ( isset( $current_curriculum['media'] ) ) {
			$current_curriculum['media'] = array_filter( $current_curriculum['media'] );
		}
		$bdlms_course_data['current_curriculum'] = $current_curriculum;
	}

	/**
	 * Flush current course data.
	 */
	public function flush_course_data() {
		global $bdlms_course_data;
		if ( apply_filters( 'bdlms_flush_course_data', true ) ) {
			$bdlms_course_data = array();
		}
	}

	/**
	 * Handle template redirect hook.
	 */
	public function template_redirect() {
		if ( ! is_user_logged_in() && is_singular( \BlueDolphin\Lms\BDLMS_COURSE_CPT ) && get_query_var( 'section' ) && get_query_var( 'item_id' ) ) {
			wp_safe_redirect( \BlueDolphin\Lms\get_page_url( 'login' ) );
			exit;
		}
		$this->set_404_page();
	}

	/**
	 * Set 404 page.
	 */
	public function set_404_page() {
		global $wp_query;
		$curriculum_type = get_query_var( 'curriculum_type', '' );
		if ( ! empty( $curriculum_type ) && in_array( $curriculum_type, array( 'quiz', 'lesson' ), true ) ) {
			$item_id = (int) get_query_var( 'item_id', 0 );
			if ( ! get_post( $item_id ) ) {
				$wp_query->set_404();
			}
		}
		$course_id = ! empty( get_query_var( 'course_id' ) ) ? (int) get_query_var( 'course_id' ) : 0;
		if ( ! get_post( $course_id ) ) {
			$wp_query->set_404();
		}
	}

	/**
	 * Update current user course view status in metadata.
	 *
	 * @param int $course_id Course ID.
	 */
	public function update_user_course_view_status( $course_id ) {
		$meta_key        = sprintf( \BlueDolphin\Lms\BDLMS_COURSE_STATUS, $course_id );
		$curriculum_type = get_query_var( 'curriculum_type' );
		$item_id         = $curriculum_type ? get_query_var( 'item_id' ) : 0;
		if ( is_user_logged_in() && $item_id ) {
			$user_id        = get_current_user_id();
			$current_status = get_user_meta( $user_id, $meta_key, true );
			if ( 'lesson' === $curriculum_type ) {
				$view_meta_key = sprintf( \BlueDolphin\Lms\BDLMS_LESSON_VIEW, $item_id );
				update_user_meta( $user_id, $view_meta_key, $item_id );
			}
			if ( $current_status === $item_id ) {
				return;
			}
			$section_id = get_query_var( 'section' ) ? get_query_var( 'section' ) : 1;
			$item_id    = $section_id . '_' . $item_id;
			update_user_meta( $user_id, $meta_key, $item_id );
		}
	}

	/**
	 * Quick check answer.
	 */
	public function quick_check_answer() {
		check_ajax_referer( \BlueDolphin\Lms\BDLMS_QUESTION_VALIDATE_NONCE, 'nonce' );
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		$bdlms_answers = ! empty( $_POST['bdlms_answers'] ) ? map_deep( $_POST['bdlms_answers'], 'sanitize_text_field' ) : array();
		if ( empty( $bdlms_answers ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$bdlms_answers = ! empty( $_POST['bdlms_written_answer'] ) ? map_deep( $_POST['bdlms_written_answer'], 'sanitize_text_field' ) : array();
		}
		$selected_answer = reset( $bdlms_answers );
		$question_id     = array_key_first( $bdlms_answers );
		$question_type   = get_post_meta( $question_id, \BlueDolphin\Lms\META_KEY_QUESTION_TYPE, true );
		if ( 'fill_blank' === $question_type ) {
			$mandatory_answers = get_post_meta( $question_id, \BlueDolphin\Lms\META_KEY_MANDATORY_ANSWERS, true );
			$right_answers     = ! empty( $mandatory_answers ) ? array( $mandatory_answers ) : array();
			$optional_answers  = get_post_meta( $question_id, \BlueDolphin\Lms\META_KEY_OPTIONAL_ANSWERS, true );
			$right_answers     = array_merge( $right_answers, $optional_answers );

			$matched = array();
			foreach ( $right_answers as $text ) {
				similar_text( $selected_answer, $text, $percent );
				$matched[] = $percent;
			}
			$status = array_filter(
				$matched,
				function ( $p ) {
					return $p > 50;
				}
			);
			$status = ! empty( $status );
		} else {
			$right_answer_key = sprintf( \BlueDolphin\Lms\META_KEY_RIGHT_ANSWERS, $question_type );
			$right_answers    = get_post_meta( $question_id, $right_answer_key, true );
			if ( is_array( $selected_answer ) && ! empty( $right_answers ) ) {
				$answer_diff = array_diff( $selected_answer, $right_answers );
				$status      = count( $right_answers ) === count( $selected_answer );
				$status      = empty( $answer_diff ) && $status ? true : false;
			} else {
				$status = $selected_answer === $right_answers;
			}
		}
		$settings = get_post_meta( $question_id, \BlueDolphin\Lms\META_KEY_QUESTION_SETTINGS, true );

		$correct_msg   = isset( $settings['hint'] ) ? $settings['hint'] : '';
		$incorrect_msg = isset( $settings['explanation'] ) ? $settings['explanation'] : '';
		if ( $status ) {
			$message = '<div class="bdlms-alert bdlms-alert-success">
			<div class="bdlms-alert-icon">
				<svg class="icon-cross" width="30" height="30">
					<use xlink:href="' . BDLMS_ASSETS . '/images/sprite-front.svg#circle-check"></use>
				</svg>
			</div>
			<div class="bdlms-alert-text">
				<div class="bdlms-alert-title">' . esc_html__( 'Correct Answer', 'bluedolphin-lms' ) . '</div>
				<p>' . esc_html( $correct_msg ) . '</p>
			</div>
		</div>';
		} else {
			$message = '<div class="bdlms-alert bdlms-alert-error">
			<div class="bdlms-alert-icon">
				<svg class="icon-cross" width="30" height="30">
					<use xlink:href="' . BDLMS_ASSETS . '/images/sprite-front.svg#circle-close"></use>
				</svg>
			</div>
			<div class="bdlms-alert-text">
				<div class="bdlms-alert-title">' . esc_html__( 'Incorrect Answer', 'bluedolphin-lms' ) . '</div>
				<p>' . $incorrect_msg . '</p>
			</div>
		</div>';
		}
		wp_send_json(
			array(
				'status'  => $status,
				'message' => $message,
			)
		);
	}

	/**
	 * Save user quiz data in result post type.
	 */
	public function save_quiz_data() {
		check_ajax_referer( \BlueDolphin\Lms\BDLMS_QUESTION_VALIDATE_NONCE, 'nonce' );
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		$bdlms_answers = ! empty( $_POST['bdlms_answers'] ) ? map_deep( $_POST['bdlms_answers'], 'sanitize_text_field' ) : array();
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		$written_answer  = ! empty( $_POST['bdlms_written_answer'] ) ? map_deep( $_POST['bdlms_written_answer'], 'sanitize_text_field' ) : array();
		$written_answer  = array_filter( $written_answer );
		$quiz_id         = ! empty( $_POST['quiz_id'] ) ? (int) $_POST['quiz_id'] : 0;
		$course_id       = ! empty( $_POST['course_id'] ) ? (int) $_POST['course_id'] : 0;
		$quiz_timestamp  = ! empty( $_POST['quiz_timestamp'] ) ? (int) $_POST['quiz_timestamp'] : 0;
		$timer_timestamp = ! empty( $_POST['timer_timestamp'] ) ? (int) $_POST['timer_timestamp'] : 0;
		$total_questions = ! empty( $_POST['total_questions'] ) ? (int) $_POST['total_questions'] : 0;

		if ( ! $quiz_id ) {
			wp_send_json(
				array(
					'status' => 0,
				)
			);
		}
		$attend_checklist_questions = array_keys( $bdlms_answers );
		$attend_written_questions   = array_keys( $written_answer );
		$total_attend_questions     = array_merge( $attend_checklist_questions, $attend_written_questions );

		$correct_answers = array();
		foreach ( $total_attend_questions as $attend_question_id ) {
			$question_type   = get_post_meta( $attend_question_id, \BlueDolphin\Lms\META_KEY_QUESTION_TYPE, true );
			$status          = false;
			$selected_answer = false;
			if ( 'fill_blank' === $question_type ) {
				$mandatory_answers = get_post_meta( $attend_question_id, \BlueDolphin\Lms\META_KEY_MANDATORY_ANSWERS, true );
				$right_answers     = ! empty( $mandatory_answers ) ? array( $mandatory_answers ) : array();
				$optional_answers  = get_post_meta( $attend_question_id, \BlueDolphin\Lms\META_KEY_OPTIONAL_ANSWERS, true );
				$right_answers     = array_merge( $right_answers, $optional_answers );

				$matched = array();
				foreach ( $right_answers as $text ) {
					similar_text( reset( $written_answer ), $text, $percent );
					$matched[] = $percent;
				}
				$status = array_filter(
					$matched,
					function ( $p ) {
						return $p > 50;
					}
				);
			} elseif ( isset( $bdlms_answers[ $attend_question_id ] ) ) {
				$selected_answer  = $bdlms_answers[ $attend_question_id ];
				$right_answer_key = sprintf( \BlueDolphin\Lms\META_KEY_RIGHT_ANSWERS, $question_type );
				$right_answers    = get_post_meta( $attend_question_id, $right_answer_key, true );
				if ( is_array( $selected_answer ) && ! empty( $right_answers ) ) {
					$answer_diff = array_diff( $selected_answer, $right_answers );
					$status      = count( $right_answers ) === count( $selected_answer );
					$status      = empty( $answer_diff ) && $status ? true : false;
				} else {
					$status = $selected_answer === $right_answers;
				}
			}
			if ( $status ) {
				$correct_answers[] = $selected_answer;
			}
		}

		$quiz_title    = get_the_title( $quiz_id );
		$course_title  = get_the_title( $course_id );
		$quiz_settings = get_post_meta( $quiz_id, \BlueDolphin\Lms\META_KEY_QUIZ_SETTINGS, true );
		$passing_mark  = 0;
		if ( ! empty( $quiz_settings['passing_marks'] ) ) {
			$passing_mark = (int) $quiz_settings['passing_marks'];
		}
		if ( is_user_logged_in() ) {
			$user_info    = wp_get_current_user();
			$result_title = sprintf( '%s - %s - %s', $course_title, $quiz_title, $user_info->display_name );
		} else {
			$result_title = sprintf( '%s - %s', $course_title, $quiz_title );
		}

		$diff_timestamp = abs( $quiz_timestamp - $timer_timestamp );
		// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
		$time_str = sprintf( esc_html__( '%s mins', 'bluedolphin-lms' ), round( $diff_timestamp / 60, 2 ) );
		// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
		$accuracy = sprintf( esc_html__( '%1$d/%2$d', 'bluedolphin-lms' ), intval( count( $total_attend_questions ) ), $total_questions );

		$grade_percentage = round( count( $correct_answers ) / $total_questions * 100, 2 );
		// Quiz data.
		$quiz_data = array(
			'attend_question_ids' => $total_attend_questions,
			'quiz_id'             => $quiz_id,
			'course_id'           => $course_id,
			'quiz_timestamp'      => $quiz_timestamp,
			'timer_timestamp'     => $timer_timestamp,
			'diff_timestamp'      => $diff_timestamp,
			'time_str'            => $time_str,
			'attempted_questions' => $accuracy,
			'correct_answers'     => $correct_answers,
			'total_questions'     => $total_questions,
			'grade_percentage'    => $grade_percentage,
		);

		$result_id   = post_exists( $result_title, '', '', \BlueDolphin\Lms\BDLMS_RESULTS_CPT );
		$result_args = array(
			'post_type'   => \BlueDolphin\Lms\BDLMS_RESULTS_CPT,
			'post_title'  => $result_title,
			'ID'          => $result_id ? $result_id : 0,
			'meta_input'  => $quiz_data,
			'post_author' => 2,
			'post_status' => 'publish',
		);
		$result_id   = wp_insert_post( $result_args );
		if ( ! is_int( $result_id ) ) {
			wp_send_json(
				array(
					'status' => 0,
				)
			);
		}

		$response = array(
			'status'             => 1,
			'time'               => $time_str,
			'attemptedQuestions' => $accuracy,
			'correctAnswers'     => count( $correct_answers ),
			'passed'             => $grade_percentage >= $passing_mark,
		);
		wp_send_json( $response );
	}

	/**
	 * Add userinfo before search bar.
	 */
	public function add_userinfo_before_search_bar() {
		echo do_shortcode( '[bdlms_userinfo]' );
	}

	/**
	 * Download course certificate.
	 */
	public function download_course_certificate() {

		check_ajax_referer( BDLMS_BASEFILE, '_nonce' );
		$course_id = ! empty( $_POST['course_id'] ) ? (int) $_POST['course_id'] : 0;

		$mpdf = new \Mpdf\Mpdf(
			array(
				'tempDir'     => sys_get_temp_dir(),
				'format'      => array( 209, 280 ),
				'orientation' => 'L',
				'fontDir'     => array( BDLMS_ABSPATH . '/assets/font' ), // @phpstan-ignore-line
				'fontdata'    => array(
					'times-new-roman' => array(
						'R' => 'times-new-roman.ttf',
					),
					'inter'           => array(
						'R' => 'Inter.ttf',
						'B' => 'Inter-Bold.ttf',
					),
				),
			)
		);

		// set the sourcefile.
		$mpdf->setSourceFile( BDLMS_ABSPATH . '/assets/images/Certificate-Blank.pdf' ); // @phpstan-ignore-line

		$import_page          = $mpdf->importPage( 1 );
		$userinfo             = wp_get_current_user();
		$user_name            = $userinfo->display_name;
		$course_completed_key = sprintf( \BlueDolphin\Lms\BDLMS_COURSE_COMPLETED_ON, $course_id );
		$completed_on         = get_user_meta( $userinfo->ID, $course_completed_key, true );
		$signature            = get_post_meta( $course_id, \BlueDolphin\Lms\META_KEY_COURSE_SIGNATURE, true );
		$date_format          = get_option( 'date_format' );
		$date                 = gmdate( $date_format, (int) $completed_on );
		$course               = get_the_title( $course_id );
		$logo                 = Options::instance()->get_option( 'company_logo' );
		$fallback_signature   = Options::instance()->get_option( 'certificate_signature' );

		/**
		 * Start MPDF media style.
		 *
		 * @link https://mpdf.github.io/css-stylesheets/introduction.html#example-using-a-stylesheet
		 */
		$print_media_style = '@media print {
			div.bdlms-user-name {
				font-family: times-new-roman !important; font-size:32px !important; width: 900px; margin: 0 auto; text-align: center; color: #191970;
			}
			div.bdlms-course-name {
				font-family: inter !important; font-size:32px; font-weight: bold; width: 900px; margin: 0 auto; text-align: center; color: #191970;
			}
			div.bdlms-text-sign {
				position: absolute; left: 35mm; bottom: 40mm; width: 240px; text-align: center; font-family: inter !important; font-size: 20px; color: #012c58;
			}
			div.bdlms-image-sign {
				width: 300px; position: absolute; left: 100px; bottom: 150px; text-align: center;
			}
			.image-sign img{
				max-width: 220px;
			}
			div.bdlms-date{
				position: absolute; right: 35mm; bottom: 40mm; width: 240px; text-align: center; font-family: inter !important; font-size: 20px; font-weight: bold; color: #012c58;
			}
			.bdlms-pdf-logo img { 
				max-width: 260px; 
			} 
			p.bdlms-pdf-logo { 
				text-align: center; 
			}
		}';
		$mpdf->WriteHTML( $print_media_style, \Mpdf\HTMLParserMode::HEADER_CSS );
		// End MPDF media style.

		$mpdf->useTemplate( $import_page, 0, 0, 280 );
		$mpdf->SetY( 85 );
		$mpdf->WriteHTML( '<div class="bdlms-user-name">' . esc_html( $user_name ) . '</div>' );
		$mpdf->SetY( 120 );
		$mpdf->WriteHTML( '<div class="bdlms-course-name">' . esc_html( $course ) . '</div>' );
		if ( ! empty( $signature['text'] ) ) {
			$mpdf->WriteHTML( '<div class="bdlms-text-sign">' . esc_html( $signature['text'] ) . '</div>' );
		} elseif ( ! empty( $signature['image_id'] ) ) {
			$mpdf->WriteHTML( '<div class="bdlms-image-sign"><img src="' . esc_url( wp_get_attachment_image_url( $signature['image_id'], '' ) ) . '" /></div>' );
		} elseif ( ! empty( $fallback_signature ) ) {
			$mpdf->WriteHTML( '<div class="bdlms-image-sign"><img src="' . esc_url( wp_get_attachment_image_url( $fallback_signature, '' ) ) . '" /></div>' );
		}
		$mpdf->WriteHTML( '<div class="bdlms-date">' . esc_html( $date ) . '</div>' );
		$mpdf->SetY( 160 );
		$mpdf->WriteHTML( '<p class="bdlms-pdf-logo"><img src="' . esc_url( wp_get_attachment_image_url( $logo, '' ) ) . '" /></p>' );
		$mpdf->Output( '', 'D' );
	}

	/**
	 * Enrol to course
	 */
	public function enrol_course() {

		check_ajax_referer( BDLMS_BASEFILE, '_nonce' );

		$course_id     = ! empty( $_POST['course_id'] ) ? (int) $_POST['course_id'] : 0;
		$user_id       = get_current_user_id();
		$enrol_courses = get_user_meta( $user_id, \BlueDolphin\Lms\BDLMS_ENROL_COURSES, true );
		$enrol_courses = ! empty( $enrol_courses ) ? $enrol_courses : array();
		if ( empty( $enrol_courses ) || ! in_array( $course_id, $enrol_courses, true ) ) {
			$enrol_courses[] = $course_id;
			update_user_meta( $user_id, \BlueDolphin\Lms\BDLMS_ENROL_COURSES, $enrol_courses );
		}
		wp_send_json(
			array(
				'url' => get_permalink( $course_id ),
			)
		);
	}
}
