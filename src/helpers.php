<?php
/**
 * Helpers functions,
 *
 * @package BlueDolphin/Lms
 */

namespace BlueDolphin\Lms;

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
 * @return void
 */
function column_post_author( $post_id = 0 ) {
	global $post;
	$postdata = $post ? $post : get_post( $post_id );

	$args = array(
		'post_type' => $postdata->post_type,
		'author'    => get_the_author_meta( 'ID' ),
	);

	$author_link = esc_url_raw( add_query_arg( $args, 'edit.php' ) );
	// phpcs:ignore Universal.CodeAnalysis.NoEchoSprintf.Found
	echo sprintf( '<span class="post-author">%s<a href="%s">%s</a></span>', get_avatar( get_the_author_meta( 'ID' ), 32 ), esc_url( $author_link ), get_the_author() );
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
		$mandatory_answers = get_post_meta( $post_id, \BlueDolphin\Lms\META_KEY_MANDATORY_ANSWERS, true );
		$optional_answers  = get_post_meta( $post_id, \BlueDolphin\Lms\META_KEY_OPTIONAL_ANSWERS, true );
		if ( ! empty( $mandatory_answers ) ) {
			$data['mandatory_answers'] = $mandatory_answers;
		}
		if ( ! empty( $optional_answers ) ) {
			$data['optional_answers'] = $optional_answers;
		}
	} else {
		$type_data = get_post_meta( $post_id, sprintf( \BlueDolphin\Lms\META_KEY_ANSWERS_LIST, $type ), true );
		$answers   = get_post_meta( $post_id, sprintf( \BlueDolphin\Lms\META_KEY_RIGHT_ANSWERS, $type ), true );
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
		$settings      = get_post_meta( $quiz_id, \BlueDolphin\Lms\META_KEY_QUIZ_SETTINGS, true );
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
			'notice' => $quiz_id ? sprintf( __( 'Passing Grade: %1$s - Edit <a href="%2$s" target="_blank">%3$s</a>', 'bluedolphin-lms' ), $passing_marks . '%', esc_url( get_edit_post_link( $quiz_id, null ) ), get_the_title( $quiz_id ) ) : __( 'No Quiz in this course!	', 'bluedolphin-lms' ),
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
			function ( $curriculum ) use ( $reference ) {
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
	if ( file_exists( get_stylesheet_directory() . '/bluedolphin/' . $template ) ) {
		$template = get_stylesheet_directory() . '/bluedolphin/' . $template;
	} elseif ( file_exists( get_template_directory() . '/bluedolphin/' . $template ) ) {
		$template = get_template_directory() . '/bluedolphin/' . $template;
	} elseif ( file_exists( BDLMS_TEMPLATEPATH . '/frontend/' . $template ) ) {
		$template = BDLMS_TEMPLATEPATH . '/frontend/' . $template;
	}
	return $template;
}

/**
 * Get locate template.
 *
 * @param string $option_name Option name.
 * @param string $page_uri Page base URI.
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
				$meta_key = \BlueDolphin\Lms\META_KEY_QUIZ_SETTINGS;
				if ( \BlueDolphin\Lms\BDLMS_LESSON_CPT === get_post_type( $curriculum ) ) {
					$meta_key = \BlueDolphin\Lms\META_KEY_LESSON_SETTINGS;
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
 * @return float Duration number.
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
			_n( '%s Hour', '%s Hours', $hours, 'bluedolphin-lms' ),
			$hours
		);
	}

	$mins = $seconds % 3600;
	if ( ! empty( $mins ) ) {
		$mins          = gmdate( 'i', $mins );
		$duration_str .= sprintf(
			// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
			_n( ' %s Min', ' %s Mins', $mins, 'bluedolphin-lms' ),
			$mins
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
	$items = \BlueDolphin\Lms\get_curriculums( $section_data, 'item_list' );
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
				if ( \BlueDolphin\Lms\BDLMS_LESSON_CPT === get_post_type( $item_id ) ) {
					$media    = get_post_meta( $item_id, \BlueDolphin\Lms\META_KEY_LESSON_MEDIA, true );
					$settings = get_post_meta( $item_id, \BlueDolphin\Lms\META_KEY_LESSON_SETTINGS, true );
					return array(
						'curriculum_type' => \BlueDolphin\Lms\BDLMS_LESSON_CPT,
						'item_id'         => $item_id,
						'media'           => $media,
						'settings'        => $settings,
					);
				}

				if ( \BlueDolphin\Lms\BDLMS_QUIZ_CPT === get_post_type( $item_id ) ) {
					$questions = get_post_meta( $item_id, \BlueDolphin\Lms\META_KEY_QUIZ_QUESTION_IDS, true );
					$settings  = get_post_meta( $item_id, \BlueDolphin\Lms\META_KEY_QUIZ_SETTINGS, true );
					return array(
						'curriculum_type' => \BlueDolphin\Lms\BDLMS_QUIZ_CPT,
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
 * @param int $item_key Item key.
 * @param int $item_index Item array index key.
 * @return string
 */
function get_curriculum_link( $item_key, $item_index ) {
	$item_key   = explode( '_', $item_key );
	$section_id = reset( $item_key );
	$item_id    = end( $item_key );
	if ( $item_id ) {
		$type = get_post_type( $item_id );
		$type = str_replace( 'bdlms_', '', $type );
		if ( 0 === $item_index ) {
			return sprintf( '%s', get_the_permalink( get_the_ID() ) );
		}
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
 * @param int   $course_id Course ID.
 * @param array $quiz_ids Quiz ID.
 */
function restart_course( $course_id = 0, $quiz_ids = array() ) {
	if ( empty( $quiz_ids ) ) {
		return true;
	}

	if ( empty( $course_id ) ) {
		return false;
	}

	$results = get_posts(
		array(
			'post_type'      => \BlueDolphin\Lms\BDLMS_RESULTS_CPT,
			'fields'         => 'ids',
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_key'       => 'course_id',
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'meta_value'     => $course_id,
			'meta_compare'   => '=',
			'posts_per_page' => -1,
		)
	);

	if ( empty( $results ) ) {
		return false;
	}

	$results = array_map(
		function ( $result_id ) {
			return get_post_meta( $result_id, 'quiz_id', true );
		},
		$results
	);
	$results = array_filter( $results );
	$results = array_map( 'intval', $results );

	$results_diff = array_diff( $quiz_ids, $results );
	return empty( $results_diff );
}
