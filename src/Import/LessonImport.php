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
	 * The main instance var.
	 *
	 * @var LessonImport|null $instance The one LessonImport instance.
	 * @since 1.0.0
	 */
	private static $instance = null;

	/**
	 * Init the main singleton instance class.
	 *
	 * @return LessonImport Return the instance class
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new LessonImport();
		}
		return self::$instance;
	}

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
		$lesson_media   = array();
		$lesson_setting = array();

		$lesson = array(
			'post_title'  => $value[0],
			'post_status' => 'publish',
			'post_type'   => \BlueDolphin\Lms\BDLMS_LESSON_CPT,
		);

		if ( ! empty( $value[1] ) ) {
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

		if ( ! empty( $value[5] ) ) {
			$duration_type  = ! empty( $value[6] ) ? rtrim( $value[6], 's' ) : 'minute';
			$lesson_setting = array(
				'duration'      => $value[5],
				'duration_type' => $duration_type,
			);
		}

		$material_title = ! empty( $value[7] ) ? explode( '|', $value[7] ) : array();
		$material_title = array_map( 'trim', $material_title );

		$material_url = ! empty( $value[8] ) ? explode( '|', $value[8] ) : array();
		$material_url = array_map( 'trim', $material_url );

		$material = array();

		foreach ( $material_title as $key => $title ) {
			$material[ $key ]['title']        = $title;
			$material[ $key ]['method']       = 'external';
			$material[ $key ]['external_url'] = $material_url[ $key ];
		}

		$lesson['meta_input'][ \BlueDolphin\Lms\META_KEY_LESSON_MEDIA ]    = $lesson_media;
		$lesson['meta_input'][ \BlueDolphin\Lms\META_KEY_LESSON_SETTINGS ] = $lesson_setting;
		$lesson['meta_input'][ \BlueDolphin\Lms\META_KEY_LESSON_MATERIAL ] = $material;
		$lesson_id = wp_insert_post( $lesson );

		return $lesson_id;
	}
}
