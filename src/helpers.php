<?php
/**
 * Helpers functions,
 *
 * @package BD\Lms
 */

namespace BD\Lms;

/**
 * Utility method to insert before specific key
 * in an associative array.
 *
 * @param string $key The key before to insert.
 * @param array  $item The array in which to insert the new key.
 * @param string $new_key The new key name.
 * @param mixed  $new_value The new key value.
 *
 * @return array|bool
 * @since   1.0.0
 * @access  public
 */
function array_insert_before( $key, $item, $new_key, $new_value ) {
	if ( array_key_exists( $key, $item ) ) {
		$new = array();
		foreach ( $item as $k => $value ) {
			if ( $k === $key ) {
				$new[ $new_key ] = $new_value;
			}
			$new[ $k ] = $value;
		}

		return $new;
	}

	return false;
}

/**
 * Get author link.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function column_post_author( $post_id = 0 ) {
	global $post;
	$postdata = $post ? $post : get_post( $post_id );

	$args = array(
		'post_type' => $postdata->post_type,
		'author'    => get_the_author_meta( 'ID' ),
	);

	$author_link = esc_url_raw( add_query_arg( $args, 'edit.php' ) );
	return sprintf( '<span class="post-author">%s<a href="%s">%s</a></span>', get_avatar( get_the_author_meta( 'ID' ), 32 ), esc_url( $author_link ), get_the_author() );
}

/**
 * Question levels.
 */
function question_levels() {
	return apply_filters(
		'bdlms_question_levels',
		array(
			'easy'   => __( 'Easy', 'bluedolphin-lms' ),
			'medium' => __( 'Medium', 'bluedolphin-lms' ),
			'hard'   => __( 'Hard', 'bluedolphin-lms' ),
		)
	);
}

/**
 * Get question alphabets.
 *
 * @return array
 */
function question_series() {
	return range( 'A', 'Z' );
}

/**
 * Get question by type.
 *
 * @param int    $post_id Question ID.
 * @param string $type Question type.
 * @return array
 */
function get_question_by_type( $post_id = 0, $type = '' ) {
	$data = array();
	if ( empty( $type ) ) {
		return $data;
	}
	if ( 'fill_blank' === $type ) {
		$mandatory_answers = get_post_meta( $post_id, \BD\Lms\META_KEY_MANDATORY_ANSWERS, true );
		$optional_answers  = get_post_meta( $post_id, \BD\Lms\META_KEY_OPTIONAL_ANSWERS, true );
		if ( ! empty( $mandatory_answers ) ) {
			$data['mandatory_answers'] = $mandatory_answers;
		}
		if ( ! empty( $optional_answers ) ) {
			$data['optional_answers'] = $optional_answers;
		}
	} else {
		$type_data = get_post_meta( $post_id, sprintf( \BD\Lms\META_KEY_ANSWERS_LIST, $type ), true );
		$answers   = get_post_meta( $post_id, sprintf( \BD\Lms\META_KEY_RIGHT_ANSWERS, $type ), true );
		if ( ! empty( $type_data ) ) {
			$data[ $type . '_answers' ] = $answers;
			$data[ $type ]              = $type_data;
		}
	}
	return $data;
}

/**
 * Evaluation.
 *
 * @param int $quiz_id Quiz ID.
 * @return array
 */
function bdlms_evaluation_list( $quiz_id = 0 ) {
	$passing_marks = 0;
	if ( $quiz_id ) {
		$settings      = get_post_meta( $quiz_id, \BD\Lms\META_KEY_QUIZ_SETTINGS, true );
		$settings      = ! empty( $settings ) ? $settings : array();
		$passing_marks = ! empty( $settings['passing_marks'] ) ? $settings['passing_marks'] : 0;
	}
	return array(
		1 => array(
			'label' => __( 'Evaluate via lessons', 'bluedolphin-lms' ),
		),
		2 => array(
			'label'  => __( 'Evaluate via results of the final quiz / last quiz', 'bluedolphin-lms' ),
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.WP.I18n.MissingTranslatorsComment
			'notice' => $quiz_id ? sprintf( __( 'Passing Grade: %1$s - Edit <a href="%2$s" target="_blank">%3$s</a>', 'bluedolphin-lms' ), $passing_marks . '%', esc_url( get_edit_post_link( $quiz_id, '' ) ), get_the_title( $quiz_id ) ) : __( 'No Quiz in this course!	', 'bluedolphin-lms' ),
		),
		3 => array(
			'label' => __( 'Evaluate via passed quizzes', 'bluedolphin-lms' ),
		),
	);
}

/**
 * Get curriculums.
 *
 * @param array  $curriculums Curriculums list.
 * @param string $reference Ref. post type.
 * @return array
 */
function get_curriculums( $curriculums = array(), $reference = '' ) {
	$curriculums_list = array();
	if ( ! is_array( $curriculums ) ) {
		return $curriculums_list;
	}
	if ( ! empty( $curriculums ) ) {
		$items = array_map(
			function ( $curriculum ) {
				return isset( $curriculum['items'] ) ? $curriculum['items'] : false;
			},
			$curriculums
		);
		$items = array_filter( $items );
		foreach ( $items as $item_key => $item ) {
			++$item_key;
			foreach ( $item as $key => $i ) {
				++$key;
				$item_id = isset( $i['item_id'] ) ? $i['item_id'] : $i;
				if ( ! empty( $reference ) && get_post_type( $item_id ) === $reference ) {
					$curriculums_list[] = $i;
				} elseif ( 'item_list' === $reference ) {
					$curriculums_list[ $item_key . '_' . $key . '_' . $item_id ] = $item_id;
				}
			}
		}
	}
	return $curriculums_list;
}

/**
 * Get locate template.
 *
 * @param string $template Template path.
 * @return string
 */
function locate_template( $template ) {

	$layout = 'default';
	if ( function_exists( 'bdlms_addons_template' ) ) {
		$layout = \bdlms_addons_template();
	}
	if ( file_exists( get_stylesheet_directory() . '/bluedolphin/' . $layout . '/' . $template ) ) {
		$template = get_stylesheet_directory() . '/bluedolphin/' . $layout . '/' . $template;
	} elseif ( file_exists( get_template_directory() . '/bluedolphin/' . $layout . '/' . $template ) ) {
		$template = get_template_directory() . '/bluedolphin/' . $layout . '/' . $template;
	} elseif ( 'default' !== $layout && defined( 'BDLMS_ADDONS_TEMPLATEPATH' ) && file_exists( BDLMS_ADDONS_TEMPLATEPATH . '/' . $layout . '/' . $template ) ) {
		$template = BDLMS_ADDONS_TEMPLATEPATH . '/' . $layout . '/' . $template;
	} elseif ( file_exists( BDLMS_TEMPLATEPATH . '/frontend/' . $template ) ) {
		$template = BDLMS_TEMPLATEPATH . '/frontend/' . $template;
	}

	return $template;
}

/**
 * Get locate template.
 *
 * @param string      $option_name Option name.
 * @param string|bool $page_uri Page base URI.
 * @return string
 */
function get_page_url( $option_name = '', $page_uri = false ) {
	$page_id = get_option( 'bdlms_' . $option_name . '_page_id', 0 );
	if ( $page_uri ) {
		return get_page_uri( $page_id );
	}
	if ( $page_id ) {
		return get_the_permalink( $page_id );
	}
	return home_url( '/' );
}

/**
 * Check current user is LMS user or not.
 */
function is_lms_user() {
	if ( is_user_logged_in() && current_user_can( 'read' ) ) {
		$user  = wp_get_current_user();
		$roles = (array) $user->roles;
		return in_array( 'bdlms', $roles, true );
	}
	return false;
}

/**
 * Convert seconds to decimal hours.
 *
 * @param int $total_seconds Total seconds.
 * @return float Duration number.
 */
function seconds_to_decimal_hours( $total_seconds ) {
	$start_total_seconds = $total_seconds;
	$hours               = floor( $total_seconds / 3600 );
	$total_seconds      %= 3600;
	$minutes             = floor( $total_seconds / 60 );
	$seconds             = $start_total_seconds - ( $minutes * 60 );
	$duration_number     = $hours * 60 + $minutes;
	return round( $duration_number / 60, 2 );
}

/**
 * Count duration.
 *
 * @param array $curriculums Curriculums list.
 * @return int
 */
function count_duration( $curriculums = array() ) {
	if ( ! is_array( $curriculums ) ) {
		return 0;
	}
	$lessons_duration = array_map(
		function ( $curriculum ) {
			if ( ! isset( $curriculum['settings'] ) ) {
				$meta_key = \BD\Lms\META_KEY_QUIZ_SETTINGS;
				if ( \BD\Lms\BDLMS_LESSON_CPT === get_post_type( $curriculum ) ) {
					$meta_key = \BD\Lms\META_KEY_LESSON_SETTINGS;
				}
				$settings = get_post_meta( $curriculum, $meta_key, true );
			} else {
				$settings = $curriculum['settings'];
			}
			if ( empty( $settings ) ) {
				return 0;
			}
			$duration      = $settings['duration'];
			$duration_type = $settings['duration_type'];
			if ( 'minute' === $duration_type ) {
				return $duration * MINUTE_IN_SECONDS;
			}
			if ( 'week' === $duration_type ) {
				return $duration * WEEK_IN_SECONDS;
			}
			if ( 'day' === $duration_type ) {
				return $duration * DAY_IN_SECONDS;
			}
			if ( 'hour' === $duration_type ) {
				return $duration * HOUR_IN_SECONDS;
			}
		},
		$curriculums
	);

	$duration = array_filter( $lessons_duration );
	$duration = array_sum( $lessons_duration );
	return $duration;
}

/**
 * Convert seconds to hours string.
 *
 * @param int $seconds Total seconds.
 * @return string Duration number.
 */
function seconds_to_hours_str( $seconds ) {
	$hours        = floor( $seconds / 3600 );
	$duration_str = '';
	if ( empty( $seconds ) ) {
		return $duration_str;
	}
	if ( ! empty( $hours ) ) {
		$duration_str .= sprintf(
			// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
			_n( '%s Hour', '%s Hours', (int) $hours, 'bluedolphin-lms' ),
			number_format_i18n( $hours )
		);
	}

	$mins = $seconds % 3600;
	if ( ! empty( $mins ) ) {
		$mins          = (int) gmdate( 'i', $mins );
		$duration_str .= sprintf(
			// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
			_n( ' %s Min', ' %s Mins', (int) $mins, 'bluedolphin-lms' ),
			number_format_i18n( $mins )
		);
	}
	return $duration_str;
}

/**
 * Merge curriculum items.
 *
 * @param array $section_data Section data.
 * @return array
 */
function merge_curriculum_items( $section_data ) {
	$items = \BD\Lms\get_curriculums( $section_data, 'item_list' );
	return $items;
}

/**
 * Get curriculum section items data.
 *
 * @param array $item Curriculum list.
 */
function get_curriculum_section_items( $item ) {
	if ( ! empty( $item['items'] ) ) {
		$item['items'] = array_map(
			function ( $item_id ) {
				if ( \BD\Lms\BDLMS_LESSON_CPT === get_post_type( $item_id ) ) {
					$media    = get_post_meta( $item_id, \BD\Lms\META_KEY_LESSON_MEDIA, true );
					$settings = get_post_meta( $item_id, \BD\Lms\META_KEY_LESSON_SETTINGS, true );
					return array(
						'curriculum_type' => \BD\Lms\BDLMS_LESSON_CPT,
						'item_id'         => $item_id,
						'media'           => $media,
						'settings'        => $settings,
					);
				}

				if ( \BD\Lms\BDLMS_QUIZ_CPT === get_post_type( $item_id ) ) {
					$questions = get_post_meta( $item_id, \BD\Lms\META_KEY_QUIZ_QUESTION_IDS, true );
					$settings  = get_post_meta( $item_id, \BD\Lms\META_KEY_QUIZ_SETTINGS, true );
					return array(
						'curriculum_type' => \BD\Lms\BDLMS_QUIZ_CPT,
						'item_id'         => $item_id,
						'questions'       => $questions,
						'settings'        => $settings,
					);
				}
			},
			$item['items']
		);
	}
	return $item;
}

/**
 * Get current curriculum item.
 *
 * @param array $curriculums Curriculums list.
 */
function get_current_curriculum( $curriculums ) {
	$curriculums = wp_list_pluck( $curriculums, 'items' );
	$curriculums = array_reduce( $curriculums, 'array_merge', array() );
	$item_id     = get_query_var( 'curriculum_type' ) ? (int) get_query_var( 'item_id' ) : 0;
	if ( $item_id ) {
		$find_item = array_search( $item_id, array_column( $curriculums, 'item_id' ), true );
		if ( false !== $find_item && isset( $curriculums[ $find_item ] ) ) {
			return $curriculums[ $find_item ];
		}
	}
	return reset( $curriculums );
}

/**
 * Get curriculum link.
 *
 * @param string $item_key   Item key.
 * @return string
 */
function get_curriculum_link( $item_key ) {
	$item_key   = explode( '_', $item_key );
	$section_id = reset( $item_key );
	$item_id    = (int) end( $item_key );
	if ( $item_id ) {
		$type = get_post_type( $item_id );
		$type = str_replace( 'bdlms_', '', $type );
		return sprintf( '%s/%d/%s/%d', untrailingslashit( get_the_permalink( get_the_ID() ) ), (int) $section_id, esc_html( $type ), (int) $item_id );
	}
	return '';
}

/**
 * Find current curriculum index key.
 *
 * @param int   $value Current item index.
 * @param array $items Item array.
 * @param int   $section_id Section ID.
 * @return string
 */
function find_current_curriculum_index( $value, $items, $section_id ) {
	$find_item = '';
	foreach ( $items as $key => $item ) {
		$item_key = explode( '_', $key );
		$s_id     = (int) reset( $item_key );
		$item_id  = (int) end( $item_key );
		if ( $s_id === $section_id && $value === $item ) {
			$find_item = $key;
		}
	}
	return $find_item;
}

/**
 * Restart course.
 *
 * @param int $course_id Course ID.
 */
function restart_course( $course_id = 0 ) {
	if ( empty( $course_id ) || ! is_user_logged_in() ) {
		return false;
	}
	$course_completed_key = sprintf( \BD\Lms\BDLMS_COURSE_COMPLETED_ON, $course_id );
	$completed_on         = get_user_meta( get_current_user_id(), $course_completed_key, true );
	return ! empty( $completed_on );
}

/**
 * Get course results by course ID.
 *
 * @param int $course_id Course ID.
 * @param int $per_page Posts per page.
 * @return array Results Ids.
 */
function get_results_course_by_id( $course_id = 0, $per_page = -1 ) {
	if ( empty( $course_id ) ) {
		$course_id = get_query_var( 'course_id', 0 );
	}
	$results = get_posts(
		array(
			'post_type'      => \BD\Lms\BDLMS_RESULTS_CPT,
			'fields'         => 'ids',
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_key'       => 'course_id',
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'meta_value'     => $course_id,
			'meta_compare'   => '=',
			'posts_per_page' => $per_page,
			'order'          => 'DESC',
		)
	);
	return $results;
}

/**
 * Calculate assessment result.
 *
 * @param int    $assessment Course assessment.
 * @param array  $curriculums Curriculums list.
 * @param int    $course_id Course ID.
 * @param string $curriculum_type Curriculums type.
 * @return array|float|int Results Ids.
 */
function calculate_assessment_result( $assessment, $curriculums = array(), $course_id = 0, $curriculum_type = '' ) {
	$passing_grade     = isset( $assessment['passing_grade'] ) ? (int) $assessment['passing_grade'] : 0;
	$evaluation        = isset( $assessment['evaluation'] ) ? $assessment['evaluation'] : 1;
	$user_id           = get_current_user_id();
	$completed_grade   = 0;
	$return_grade_only = true;
	if ( empty( $curriculum_type ) ) {
		$return_grade_only = false;
		$curriculum_type   = 'quiz';
		if ( 1 === $evaluation ) {
			$curriculum_type = 'lesson';
		} elseif ( 2 === $evaluation ) {
			$curriculum_type = 'last_quiz';
		}
	}
	if ( 'lesson' === $curriculum_type ) {
		$lessons = \BD\Lms\get_curriculums( $curriculums, \BD\Lms\BDLMS_LESSON_CPT );
		if ( ! empty( $lessons ) ) {
			$completed_lesson = 0;
			foreach ( $lessons as $lesson ) {
				$meta_key      = sprintf( \BD\Lms\BDLMS_LESSON_VIEW, $lesson );
				$viewed_lesson = (int) get_user_meta( $user_id, $meta_key, true );
				if ( $viewed_lesson ) {
					++$completed_lesson;
				}
			}
			if ( $completed_lesson ) {
				$completed_grade = round( $completed_lesson / count( $lessons ) * 100, 2 );
			}
		}
	} elseif ( 'quiz' === $curriculum_type ) {
		$results               = \BD\Lms\get_results_course_by_id();
		$total_questions       = 0;
		$total_correct_answers = 0;
		if ( ! empty( $results ) ) {
			foreach ( $results as $result ) {
				$correct_answers        = get_post_meta( $result, 'correct_answers', true );
				$total_correct_answers += ! empty( $correct_answers ) ? count( $correct_answers ) : 0;
				$total_questions       += (int) get_post_meta( $result, 'total_questions', true );
			}
		}
		if ( $total_correct_answers ) {
			$completed_grade = round( $total_correct_answers / $total_questions * 100, 2 );
		}
	} elseif ( 'last_quiz' === $curriculum_type ) {
		$results   = \BD\Lms\get_results_course_by_id( 0, 1 );
		$result_id = ! empty( $results ) ? reset( $results ) : 0;
		if ( $result_id ) {
			$grade_percentage = get_post_meta( $result_id, 'grade_percentage', true );
			$completed_grade  = (float) str_replace( '%', '', $grade_percentage );
		}
	}
	if ( $return_grade_only ) {
		return $completed_grade;
	}
	$course_completed_key = sprintf( \BD\Lms\BDLMS_COURSE_COMPLETED_ON, $course_id );
	$completed_on         = get_user_meta( $user_id, $course_completed_key, true );
	if ( empty( $completed_on ) ) {
		$completed_on = time();
		update_user_meta( $user_id, $course_completed_key, $completed_on );
	}
	return array(
		$passing_grade,
		$completed_grade,
		$completed_on,
	);
}

/**
 * Fetches the import table data from database.
 *
 * @param int  $status import log status.
 * @param bool $status_count count import log according to their status.
 */
function fetch_import_data( $status = 0, $status_count = false ) {
	global $wpdb;

	$table_name = $wpdb->prefix . \BD\Lms\BDLMS_CRON_TABLE;

	$import_log = get_transient( 'bdlms_import_data' );

	if ( empty( $status ) ) {
		$status = ! empty( $_REQUEST['status'] ) ? trim( sanitize_text_field( wp_unslash( $_REQUEST['status'] ) ) ) : ''; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	if ( ! empty( $import_log ) ) {

		$import_log = array_map(
			function ( $data ) use ( $status, $status_count ) {
				if ( empty( $status ) || $status_count ) {
						return $data;
				}
				if ( $status === $data['import_status'] ) {
					return $data;
				}
				return false;
			},
			$import_log
		);
		$import_log = array_filter( $import_log );
		return $import_log;
	}

	$import_log = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A ); // phpcs:ignore

	set_transient( 'bdlms_import_data', $import_log );
	return $import_log;
}

/**
 * Import job status.
 *
 * @return array
 */
function import_job_status() {

	return array(
		1 => __( 'In-Progress', 'bluedolphin-lms' ),
		2 => __( 'Complete', 'bluedolphin-lms' ),
		3 => __( 'Cancelled', 'bluedolphin-lms' ),
		4 => __( 'Failed', 'bluedolphin-lms' ),
	);
}

/**
 * Import post type.
 *
 * @return array
 */
function import_post_type() {

	return array(
		1 => \BD\Lms\BDLMS_QUESTION_CPT,
		2 => \BD\Lms\BDLMS_LESSON_CPT,
		3 => \BD\Lms\BDLMS_COURSE_CPT,
	);
}

/**
 * Explode import data.
 *
 * @param int|string $data import file data.
 * @return array
 */
function explode_import_data( $data ) {

	$data = explode( '|', $data );
	$data = array_map( 'trim', $data );

	return $data;
}

/**
 * Get course statistics.
 *
 * @return array
 */
function course_statistics() {
	$completed     = array();
	$progressed    = array();
	$not_started   = array();
	$total_course  = 0;
	$enrol_courses = get_user_meta( get_current_user_id(), \BD\Lms\BDLMS_ENROL_COURSES, true );

	$course_args = array(
		'post_type'      => \BD\Lms\BDLMS_COURSE_CPT,
		'post_status'    => 'publish',
		'posts_per_page' => -1,
	);

	if ( ! empty( $enrol_courses ) ) {
		$course_args['post__in'] = $enrol_courses;
		$courses                 = new \WP_Query( $course_args );
		$total_course            = $courses->found_posts;
	}

	if ( ! empty( $enrol_courses ) && $courses->have_posts() ) {
		while ( $courses->have_posts() ) {
			$courses->the_post();
			$course_id = get_the_ID();

			$user_id     = get_current_user_id();
			$curriculums = get_post_meta( $course_id, \BD\Lms\META_KEY_COURSE_CURRICULUM, true );
			if ( ! empty( $curriculums ) ) {

				$curriculums     = \BD\Lms\merge_curriculum_items( $curriculums );
				$curriculums     = array_keys( $curriculums );
				$last_curriculum = end( $curriculums );
				$last_curriculum = explode( '_', $last_curriculum );
				$last_curriculum = array_map( 'intval', $last_curriculum );
				$course_status   = get_user_meta( $user_id, sprintf( \BD\Lms\BDLMS_COURSE_STATUS, $course_id ), true );
				if ( ! empty( $course_status ) ) {

					$course_status = ! is_string( $course_status ) ? end( $course_status ) : $course_status;
					$course_status = explode( '_', $course_status );
					$section_id    = reset( $course_status );
					$item_id       = end( $course_status );

					$course_status = \BD\Lms\restart_course( $course_id );
					if ( $course_status && reset( $last_curriculum ) === (int) $section_id && end( $last_curriculum ) === (int) $item_id ) {
						$completed[] = $course_id;
					} else {
						$progressed[] = $course_id;
					}
				} else {
					$not_started[] = $course_id;
				}
			}
		}
	}
	return array(
		'total_course' => $total_course,
		'completed'    => $completed,
		'in_progress'  => $progressed,
		'not_started'  => $not_started,
	);
}

/**
 * Calculate course progress.
 *
 * @param int   $course_id      Course id.
 * @param array $curriculums    Course curriculums.
 * @param array $current_status Course current completed status.
 *
 * @return int
 */
function calculate_course_progress( $course_id, $curriculums, $current_status = array() ) {

	if ( empty( $curriculums ) ) {
		return 0;
	}

	if ( empty( $current_status ) ) {
		$current_status = get_user_meta( get_current_user_id(), sprintf( \BD\Lms\BDLMS_COURSE_STATUS, $course_id ), true );
	}
	$course_completed = 0;
	if ( ! empty( $current_status ) ) {
		$current_status    = is_string( $current_status ) ? array( $current_status ) : $current_status;
		$total_curriculums = count( $curriculums );
		$completed_course  = count( $current_status );
		$course_completed  = (int) ( ( ( $completed_course - 1 ) / $total_curriculums ) * 100 );

		if ( $total_curriculums === $completed_course ) {
			$course_status    = \BD\Lms\restart_course( $course_id );
			$course_completed = $course_status ? 100 : $course_completed;
		}
	}
	return $course_completed;
}

/**
 * Get the taxonomies.
 *
 * @param  string $tax Taxonomy to get.
 * @return array
 */
function course_taxonomies( $tax ) {
	$get_terms  = get_terms(
		array(
			'taxonomy'   => $tax,
			'hide_empty' => true,
		)
	);
	$terms_list = array();
	if ( ! empty( $get_terms ) ) {
		$terms_list = array_map(
			function ( $term ) {
				return array(
					'id'    => $term->term_id,
					'name'  => $term->name,
					'count' => $term->count,
				);
			},
			$get_terms
		);
	}
	return $terms_list;
}

/**
 * Get layout colors.
 *
 * @return array
 */
function layout_colors() {
	$colors = array(
		'layout-2' => array(
			'primary_color'          => '#893bf8',
			'secondary_color'        => '#00cfbe',
			'background_color'       => '#f6f6f7',
			'background_light_color' => '#fbfbfb',
			'border_color'           => '#ededed',
			'white_color'            => '#ffffff',
			'heading_color'          => '#101011',
			'paragraph_color'        => '#5d5d73',
			'paragraph_light_color'  => '#85859d',
			'link_color'             => '#7d3cd9',
			'icon_color'             => '#a9a9a9',
			'success_color'          => '#25af3d',
			'error_color'            => '#c53434',
		),
	);

	return $colors;
}

/**
 * Get layout typographies.
 *
 * @return array
 */
function layout_typographies() {
	$layout = array(
		'typography' => array(
			'font_weight'     => array( '100', '200', '300', '400', '500', '600', '700', '800', '900' ),
			'font_size'       => array( '12px', '14px', '18px', '20px', '24px', '30px', '48px', '60px', '72px', '96px', '128px' ),
			'text_transform'  => array( 'none', 'capitalize', 'uppercase', 'lowercase' ),
			'line_height'     => array( '1', '1.1', '1.2', '1.3', '1.4', '1.5', '1.7', '2' ),
			'letter_spacing'  => array( '1px', '2px', '3px', '4px', '5px' ),
			'text_decoration' => array( 'none', 'line-through', 'overline', 'underline' ),
		),
		'tag'        => array( 'heading_1', 'heading_2', 'heading_3', 'heading_4', 'heading_5', 'heading_6', 'paragraph', 'paragraph_large', 'paragraph_small', 'link' ),
	);

	return $layout;
}
