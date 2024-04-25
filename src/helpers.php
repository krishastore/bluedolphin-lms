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
 * Evaluation
 */
function bdlms_evaluation_list() {
	return array(
		1 => array(
			'label' => __( 'Evaluate via lessons', 'bluedolphin-lms' ),
		),
		2 => array(
			'label'  => __( 'Evaluate via results of the final quiz', 'bluedolphin-lms' ),
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.WP.I18n.MissingTranslatorsComment
			'notice' => sprintf( __( 'Passing Grade: %1$s - Edit <a href="%2$s">Quiz Name</a>', 'bluedolphin-lms' ), '80%', esc_url( 'http://lms.local/' ) )
		),
		3 => array(
			'label' => __( 'Evaluate via passed quizzes', 'bluedolphin-lms' ),
		),
	);
}
