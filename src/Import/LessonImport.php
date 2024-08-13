<?php
/**
 * The file that manage the import lesson.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BlueDolphin\Lms
 */

namespace BlueDolphin\Lms\Import;

/**
 * Import lesson class
 */
class LessonImport extends \BlueDolphin\Lms\Helpers\FileImport {

	/**
	 * Class construct.
	 */
	public function __construct() {
		$this->import_type  = 2;
		$this->taxonomy_tag = \BlueDolphin\Lms\BDLMS_LESSON_TAXONOMY_TAG;
		$this->init();
	}

	/**
	 * Import file header.
	 *
	 * @return array
	 */
	public function file_header() {
		return array( 'title', 'media_type', 'media_url', 'duration' );
	}

	/**
	 * Import lesson data.
	 *
	 * @param array $value import file data.
	 *
	 * @return int
	 */
	public function insert_import_data( $value ) {
		$lesson_media      = array();
		$lesson_setting    = array();
		$lesson_media_type = array( 'file', 'text', 'video' );

		$lesson = array(
			'post_title'  => $value[0],
			'post_status' => 'publish',
			'post_type'   => \BlueDolphin\Lms\BDLMS_LESSON_CPT,
			'post_author' => 1,
		);

		if ( ! empty( $value[1] ) && in_array( $value[1], $lesson_media_type, true ) ) {
			$media_type   = $value[1];
			$media_url    = ! empty( $value[2] ) ? sanitize_url( $value[2] ) : '';
			$text         = ! empty( $value[3] ) ? wp_kses_post( $value[3] ) : '';
			$lesson_media = array(
				'media_type'      => $media_type,
				'video_id'        => 0,
				'file_id'         => 0,
				'file_url'        => 'file' === $media_type ? $media_url : '',
				'embed_video_url' => 'video' === $media_type ? $media_url : '',
				'text'            => 'text' === $media_type ? $text : '',
			);
		}

		if ( ! empty( $value[6] ) ) {
			$duration_type  = ! empty( $value[7] ) ? rtrim( $value[7], 's' ) : 'minute';
			$lesson_setting = array(
				'duration'      => $value[6],
				'duration_type' => $duration_type,
			);
		}

		$material_title = ! empty( $value[8] ) ? explode( '|', $value[8] ) : array();
		$material_title = array_map( 'trim', $material_title );

		$material_url = ! empty( $value[9] ) ? explode( '|', $value[9] ) : array();
		$material_url = array_map( 'trim', $material_url );

		$material = array();

		foreach ( $material_title as $key => $title ) {
			$material[ $key ]['title']        = $title;
			$material[ $key ]['method']       = 'external';
			$material[ $key ]['external_url'] = isset( $material_url[ $key ] ) ? $material_url[ $key ] : '';
		}

		$lesson['meta_input'][ \BlueDolphin\Lms\META_KEY_LESSON_MEDIA ]    = $lesson_media;
		$lesson['meta_input'][ \BlueDolphin\Lms\META_KEY_LESSON_SETTINGS ] = $lesson_setting;
		$lesson['meta_input'][ \BlueDolphin\Lms\META_KEY_LESSON_MATERIAL ] = $material;

		// create lesson.
		$lesson_id = wp_insert_post( $lesson );

		$courses = ! empty( $value[5] ) ? explode( '|', $value[5] ) : array();
		$courses = array_map( 'trim', $courses );

		foreach ( $courses as $course ) {
			if ( is_numeric( $course ) ) {
				$course_id = get_post( (int) $course ) ? (int) $course : 0;
			} else {
				if ( ! function_exists( 'post_exists' ) ) {
					require_once ABSPATH . 'wp-admin/includes/post.php';
				}
				$course_id = post_exists( $course, '', '', \BlueDolphin\Lms\BDLMS_COURSE_CPT );

				$create_course = apply_filters( 'bdlms_create_new_course', true );
				if ( ! $course_id && $create_course ) {
					$new_course = array(
						'post_title'  => $course,
						'post_status' => 'publish',
						'post_type'   => \BlueDolphin\Lms\BDLMS_COURSE_CPT,
					);

					// create course.
					$course_id = wp_insert_post( $new_course );
				}
			}

			if ( $course_id ) {
				$curriculums = get_post_meta( $course_id, \BlueDolphin\Lms\META_KEY_COURSE_CURRICULUM, true );
				$curriculums = ! empty( $curriculums ) ? $curriculums : array(
					array(
						'section_name' => '',
						'section_desc' => '',
						'items'        => array(),
					),
				);
				$last_index  = array_key_last( $curriculums );
				if ( isset( $curriculums[ $last_index ]['items'] ) && ! in_array( $lesson_id, $curriculums[ $last_index ]['items'], true ) ) {
					$curriculums[ $last_index ]['items'][] = $lesson_id;
				}
				update_post_meta( $course_id, \BlueDolphin\Lms\META_KEY_COURSE_CURRICULUM, $curriculums );
			}
		}

		return $lesson_id;
	}
}
