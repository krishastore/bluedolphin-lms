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

/**
 * Shortcode register manage class.
 */
class Courses extends \BlueDolphin\Lms\Shortcode\Register implements \BlueDolphin\Lms\Interfaces\Courses {

	/**
	 * Init.
	 */
	public function init() {
		$this->shortcode_tag = 'bdlms_courses';
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
		add_action( 'bdlms_before_search_bar', array( $this, 'add_userinfo_before_search_bar' ) );
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
		$prefix = '';
		if ( is_singular( \BlueDolphin\Lms\BDLMS_COURSE_CPT ) ) {
			if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {
				$prefix = 'block-theme-';
			}
			$template = \BlueDolphin\Lms\locate_template( $prefix . 'single-courses.php' );
		}
		$course_id = ! empty( get_query_var( 'course_id' ) ) ? (int) get_query_var( 'course_id' ) : 0;
		if ( $course_id ) {
			$template = \BlueDolphin\Lms\locate_template( $prefix . 'course-result.php' );
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
		global $course_data;
		$curriculums     = isset( $course_data['curriculums'] ) ? $course_data['curriculums'] : array();
		$curriculum_type = isset( $course_data['current_curriculum']['media']['media_type'] ) ? $course_data['current_curriculum']['media']['media_type'] : '';
		$current_item    = isset( $course_data['current_curriculum']['item_id'] ) ? $course_data['current_curriculum']['item_id'] : 0;
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
		global $course_data;
		$curriculums                = get_post_meta( $course_id, \BlueDolphin\Lms\META_KEY_COURSE_CURRICULUM, true );
		$curriculums                = ! empty( $curriculums ) ? $curriculums : array();
		$curriculums                = array_map( '\BlueDolphin\Lms\get_curriculum_section_items', $curriculums );
		$current_curriculum         = \BlueDolphin\Lms\get_current_curriculum( $curriculums );
		$course_data['curriculums'] = $curriculums;
		if ( isset( $current_curriculum['media'] ) ) {
			$current_curriculum['media'] = array_filter( $current_curriculum['media'] );
		}
		$course_data['current_curriculum'] = $current_curriculum;
	}

	/**
	 * Flush current course data.
	 */
	public function flush_course_data() {
		global $course_data;
		if ( apply_filters( 'bdlms_flush_course_data', true ) ) {
			$course_data = array();
		}
	}

	/**
	 * Handle template redirect hook.
	 */
	public function template_redirect() {
		if ( ! is_user_logged_in() && is_singular( \BlueDolphin\Lms\BDLMS_COURSE_CPT ) ) {
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
		exit;
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
			exit;
		}
		$attend_checklist_questions = array_keys( $bdlms_answers );
		$attend_written_questions   = array_keys( $written_answer );
		$total_attend_questions     = array_merge( $attend_checklist_questions, $attend_written_questions );

		$correct_answers = array();
		foreach ( $total_attend_questions as $attend_question_id ) {
			$question_type = get_post_meta( $attend_question_id, \BlueDolphin\Lms\META_KEY_QUESTION_TYPE, true );
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
		$user_info = wp_get_current_user();
		if ( $user_info ) {
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
		if ( is_wp_error( $result_id ) ) {
			wp_send_json(
				array(
					'status' => 0,
				)
			);
			exit;
		}

		$response = array(
			'status'             => 1,
			'time'               => $time_str,
			'attemptedQuestions' => $accuracy,
			'correctAnswers'     => count( $correct_answers ),
			'passed'             => $grade_percentage >= $passing_mark,
		);
		wp_send_json( $response );
		exit;
	}

	/**
	 * Add userinfo before search bar.
	 */
	public function add_userinfo_before_search_bar() {
		echo do_shortcode( '[bdlms_userinfo]' );
	}
}