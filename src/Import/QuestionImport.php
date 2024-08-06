<?php
/**
 * The file that manage the import question.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BlueDolphin\Lms
 */

namespace BlueDolphin\Lms\Import;

/**
 * Import question class
 */
class QuestionImport extends \BlueDolphin\Lms\Helpers\FileImport {
	/**
	 * The main instance var.
	 *
	 * @var QuestionImport|null $instance The one QuestionImport instance.
	 * @since 1.0.0
	 */
	private static $instance = null;

	/**
	 * Init the main singleton instance class.
	 *
	 * @return QuestionImport Return the instance class
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new QuestionImport();
		}
		return self::$instance;
	}

	/**
	 * Class construct.
	 */
	public function __construct() {
		$this->import_type  = 1;
		$this->taxonomy_tag = \BlueDolphin\Lms\BDLMS_QUESTION_TAXONOMY_TAG;
		$this->init();
	}

	/**
	 * Import file header.
	 *
	 * @return array
	 */
	public function file_header() {
		return array( 'title', 'question_type', 'answers', 'right_answers' );
	}

	/**
	 * Import question data.
	 *
	 * @param array $value import file data.
	 *
	 * @return int
	 */
	public function insert_import_data( $value ) {
		$question = array(
			'post_title'   => $value[0],
			'post_content' => ! empty( $value[1] ) ? $value[1] : '',
			'post_status'  => 'publish',
			'post_type'    => \BlueDolphin\Lms\BDLMS_QUESTION_CPT,
			'meta_input'   => array(
				\BlueDolphin\Lms\META_KEY_QUESTION_TYPE => $value[6],
				\BlueDolphin\Lms\META_KEY_QUESTION_SETTINGS => array(),
			),
		);

		if ( ! empty( $value[7] ) ) {

			$choices = explode( '|', $value[7] );
			$choices = array_map( 'trim', $choices );

			if ( isset( $value[6] ) && 'single_choice' === $value[6] ) {
				$question['meta_input'][ \BlueDolphin\Lms\META_KEY_QUESTION_PREFIX . '_single_choice' ] = $choices;
			} elseif ( isset( $value[6] ) && 'multi_choice' === $value[6] ) {
				$question['meta_input'][ \BlueDolphin\Lms\META_KEY_QUESTION_PREFIX . '_multi_choice' ] = $choices;
			} elseif ( isset( $value[6] ) && 'true_or_false' === $value[6] ) {
				$question['meta_input'][ \BlueDolphin\Lms\META_KEY_QUESTION_PREFIX . '_true_or_false' ] = $choices;
			}
		}
		if ( ! empty( $value[5] ) ) {
			$value[5] = 'no' === $value[5] ? 0 : 1;
			$question['meta_input'][ \BlueDolphin\Lms\META_KEY_QUESTION_SETTINGS ]['status'] = $value[5];
		}

		$question['meta_input'][ \BlueDolphin\Lms\META_KEY_QUESTION_SETTINGS ]['points']      = ! empty( $value[2] ) ? $value[2] : 1;
		$question['meta_input'][ \BlueDolphin\Lms\META_KEY_QUESTION_SETTINGS ]['levels']      = ! empty( $value[3] ) ? $value[3] : 'easy';
		$question['meta_input'][ \BlueDolphin\Lms\META_KEY_QUESTION_SETTINGS ]['hint']        = ! empty( $value[10] ) ? $value[10] : '';
		$question['meta_input'][ \BlueDolphin\Lms\META_KEY_QUESTION_SETTINGS ]['explanation'] = ! empty( $value[11] ) ? $value[11] : '';

		if ( isset( $value[6] ) && 'multi_choice' === $value[6] ) {
			$right_ans = sprintf( \BlueDolphin\Lms\META_KEY_RIGHT_ANSWERS, $value[6] );
			$ans       = isset( $value[8] ) && ! empty( $value[8] ) ? explode( '|', $value[8] ) : array();

			if ( ! empty( $ans ) ) {

				$ans = array_map(
					function ( $v ) {
						return wp_hash( trim( $v ) );
					},
					$ans
				);
			}

			$question['meta_input'][ $right_ans ] = $ans;

		} elseif ( isset( $value[6] ) && 'fill_blank' === $value[6] ) {

			$mandatory_ans = explode( '|', $value[9] );
			$question['meta_input'][ \BlueDolphin\Lms\META_KEY_MANDATORY_ANSWERS ] = array_shift( $mandatory_ans );
			$optional_ans = $mandatory_ans;
			$question['meta_input'][ \BlueDolphin\Lms\META_KEY_OPTIONAL_ANSWERS ] = $optional_ans;

		} elseif ( isset( $value[6] ) && 'true_or_false' === $value[6] ) {
			$right_ans = sprintf( \BlueDolphin\Lms\META_KEY_RIGHT_ANSWERS, $value[6] );

			$ans = ! empty( $value[8] ) ? wp_hash( ucfirst( trim( strtolower( $value[8] ) ) ) ) : '';

			$question['meta_input'][ $right_ans ] = $ans;

		} else {
			$right_ans = sprintf( \BlueDolphin\Lms\META_KEY_RIGHT_ANSWERS, $value[6] );

			$ans = ! empty( $value[8] ) ? wp_hash( trim( $value[8] ) ) : '';

			$question['meta_input'][ $right_ans ] = $ans;
		}

		$question_id = wp_insert_post( $question );
		return $question_id;
	}
}
