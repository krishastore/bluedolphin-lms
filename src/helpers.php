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
	$curriculum_ids = array();
	if ( ! is_array( $curriculums ) ) {
		return $curriculum_ids;
	}
	if ( ! empty( $curriculums ) ) {
		$items = array_map(
			function ( $curriculum ) {
				return isset( $curriculum['items'] ) ? $curriculum['items'] : false;
			},
			$curriculums
		);
		foreach ( $items as $item ) {
			foreach ( $item as $i ) {
				if ( get_post_type( $i ) === $reference ) {
					$curriculum_ids[] = $i;
				}
			}
		}
	}
	return $curriculum_ids;
}
