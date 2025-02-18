<?php
/**
 * The file that manage the import course.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BD\Lms
 */

namespace BD\Lms\Import;

use function BD\Lms\explode_import_data as explodeData;
use const BD\Lms\BDLMS_COURSE_CATEGORY_TAX;
use const BD\Lms\META_KEY_COURSE_CURRICULUM;
use const BD\Lms\META_KEY_COURSE_ASSESSMENT;
use const BD\Lms\META_KEY_COURSE_MATERIAL;
use const BD\Lms\META_KEY_COURSE_INFORMATION;
use const BD\Lms\BDLMS_COURSE_TAXONOMY_TAG;
use const BD\Lms\BDLMS_QUIZ_CPT;
use const BD\Lms\BDLMS_LESSON_CPT;


/**
 * Import lesson class
 */
class CourseImport extends \BD\Lms\Helpers\FileImport {

	/**
	 * Class construct.
	 */
	public function __construct() {
		$this->import_type  = 3;
		$this->taxonomy_tag = BDLMS_COURSE_CATEGORY_TAX;
		$this->init();
	}

	/**
	 * Import file header.
	 *
	 * @return array
	 */
	public function file_header() {
		return array( 'title', 'content', 'curriculum_title', 'lesson/quiz' );
	}

	/**
	 * Import lesson data.
	 *
	 * @param array $value import file data.
	 *
	 * @return int
	 */
	public function insert_import_data( $value ) {

		$terms_id   = array();
		$evaluation = array(
			1 => 'lessons',
			2 => 'final quiz / last quiz',
			3 => 'passed quizzes',
		);

		$course = array(
			'post_title'   => $value[0],
			'post_excerpt' => ! empty( $value[1] ) ? $value[1] : '',
			'post_content' => ! empty( $value[2] ) ? $value[2] : '',
			'post_status'  => 'publish',
			'post_type'    => \BD\Lms\BDLMS_COURSE_CPT,
			'post_author'  => 1,
		);

		$terms = ! empty( $value[3] ) ? explodeData( $value[3] ) : array();

		foreach ( $terms as $_term ) {
			if ( term_exists( $_term, BDLMS_COURSE_TAXONOMY_TAG ) ) {
				$existing_term = get_term_by( 'name', $_term, BDLMS_COURSE_TAXONOMY_TAG );
				$terms_id[]    = $existing_term->term_id;
			} else {
				$terms      = wp_insert_term( $_term, BDLMS_COURSE_TAXONOMY_TAG );
				$terms_id[] = $terms['term_id'];
			}
		}

		$section_name = ! empty( $value[5] ) ? $value[5] : '';
		$section_desc = ! empty( $value[6] ) ? $value[6] : '';
		$items        = ! empty( $value[7] ) ? explodeData( $value[7] ) : array();
		$items_id     = array();

		foreach ( $items as $item ) {
			if ( is_numeric( $item ) ) {
				$item_id = get_post( (int) $item ) ? (int) $item : 0;
			} else {
				$item_id = 0;
				if ( str_contains( $item, 'Quiz:' ) ) {
					$item      = ltrim( $item, 'Quiz:' );
					$quiz_data = get_posts(
						array(
							'title'       => $item,
							'post_type'   => BDLMS_QUIZ_CPT,
							'numberposts' => 1,
							'fields'      => 'ids',
						)
					);
					if ( ! empty( $quiz_data ) ) {
						$item_id = reset( $quiz_data );
					}
				} else {
					$lesson_data = get_posts(
						array(
							'title'       => $item,
							'post_type'   => BDLMS_LESSON_CPT,
							'numberposts' => 1,
							'fields'      => 'ids',
						)
					);
					if ( ! empty( $lesson_data ) ) {
						$item_id = reset( $lesson_data );
					}

					if ( ! $item_id ) {
						$quiz_data = get_posts(
							array(
								'title'       => $item,
								'post_type'   => BDLMS_QUIZ_CPT,
								'numberposts' => 1,
								'fields'      => 'ids',
							)
						);
						if ( ! empty( $quiz_data ) ) {
							$item_id = reset( $quiz_data );
						}
					}
				}
			}

			if ( $item_id ) {
				$items_id[] = $item_id;
			}
		}

		$course['meta_input'][ META_KEY_COURSE_CURRICULUM ][] = array(
			'section_name' => $section_name,
			'section_desc' => $section_desc,
			'items'        => $items_id,
		);

		$course['meta_input'][ META_KEY_COURSE_INFORMATION ]['requirement']     = ! empty( $value[8] ) ? explodeData( $value[8] ) : array();
		$course['meta_input'][ META_KEY_COURSE_INFORMATION ]['what_you_learn']  = ! empty( $value[9] ) ? explodeData( $value[9] ) : array();
		$course['meta_input'][ META_KEY_COURSE_INFORMATION ]['skills_you_gain'] = ! empty( $value[10] ) ? explodeData( $value[10] ) : array();
		$course['meta_input'][ META_KEY_COURSE_INFORMATION ]['course_includes'] = ! empty( $value[11] ) ? explodeData( $value[11] ) : array();
		$course['meta_input'][ META_KEY_COURSE_INFORMATION ]['faq_question']    = ! empty( $value[12] ) ? explodeData( $value[12] ) : array();
		$course['meta_input'][ META_KEY_COURSE_INFORMATION ]['faq_answer']      = ! empty( $value[13] ) ? explodeData( $value[13] ) : array();

		$evaluation_via = ! empty( $value[14] ) ? (int) array_search( $value[14], $evaluation, true ) : 0;

		$course['meta_input'][ META_KEY_COURSE_ASSESSMENT ]['evaluation']    = $evaluation_via;
		$course['meta_input'][ META_KEY_COURSE_ASSESSMENT ]['passing_grade'] = ! empty( $value[15] ) ? (int) $value[15] : 0;

		$material_title = ! empty( $value[16] ) ? explodeData( $value[16] ) : array();
		$material_url   = ! empty( $value[17] ) ? explodeData( $value[17] ) : array();

		$material = array();

		foreach ( $material_title as $key => $title ) {
			$material[ $key ]['title']        = $title;
			$material[ $key ]['method']       = 'external';
			$material[ $key ]['external_url'] = isset( $material_url[ $key ] ) ? $material_url[ $key ] : '';
		}

		$course['meta_input'][ META_KEY_COURSE_MATERIAL ] = $material;

		// create course.
		$course_id = wp_insert_post( $course );
		wp_set_post_terms( $course_id, $terms_id, BDLMS_COURSE_TAXONOMY_TAG );
		return $course_id;
	}
}
