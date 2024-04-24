<?php
/**
 * Class QuizTest
 *
 * @package BlueDolphin\Lms\Admin\MetaBoxes
 *
 * phpcs:disable WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput
 */

use const BlueDolphin\Lms\BDLMS_QUIZ_CPT;
use const BlueDolphin\Lms\BDLMS_QUESTION_CPT;
use const BlueDolphin\Lms\META_KEY_QUIZ_GROUPS;
use const BlueDolphin\Lms\BDLMS_QUESTION_TAXONOMY_TAG;
use const BlueDolphin\Lms\BDLMS_QUIZ_TAXONOMY_LEVEL_1;
use const BlueDolphin\Lms\BDLMS_QUIZ_TAXONOMY_LEVEL_2;

/**
 * Quiz test case.
 */
class QuizTest extends WP_UnitTestCase {

	/**
	 * Sets up the test methods.
	 */
	public function setUp(): void {
		parent::setUp();
		// avoids error - readfile(/src/wp-includes/js/wp-emoji-loader.js): failed to open stream: No such file or directory.
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		$this->create_quiz();
	}

	/**
	 * Create quiz.
	 */
	public function create_quiz() {
		global $post;

		do_action( 'init' );

		$user_id = $this->factory->user->create(
			array(
				'role' => 'administrator',
			)
		);
		$this->assertIsInt( $user_id );
		wp_set_current_user( $user_id );

		$_POST['action']          = 'post';
		$_POST['bdlms_nonce']     = wp_create_nonce( BDLMS_BASEFILE );
		$_POST['_bdlms_question'] = array(
			'type'                  => 'true_or_false',
			'true_or_false'         => array(
				'True',
				'False',
			),
			'true_or_false_answers' => 0,
			'settings'              => array(
				'points'      => 1,
				'levels'      => 'easy',
				'status'      => 1,
				'hint'        => 'Correctly Answered Feedback Textarea',
				'explanation' => 'Incorrectly Answered Feedback Textarea',
			),
		);
		$q                        = $this->factory->post->create_and_get(
			array(
				'post_title'  => 'Test Create Question',
				'post_author' => $user_id,
				'post_type'   => BDLMS_QUESTION_CPT,
			)
		);
		$this->assertNotWPError( $q );

		$topic = wp_create_term( 'Topic 2', BDLMS_QUESTION_TAXONOMY_TAG );
		$this->assertNotWPError( $topic );

		$set_term = wp_set_post_terms( $q->ID, $topic['term_id'], BDLMS_QUESTION_TAXONOMY_TAG );
		$this->assertNotWPError( $set_term );

		$post = get_post( $q->ID );
		do_action( 'save_post_' . BDLMS_QUESTION_CPT, $q->ID, $q );

		$p = $this->factory->post->create_and_get(
			array(
				'post_title'  => 'First Quiz',
				'post_author' => $user_id,
				'post_type'   => BDLMS_QUIZ_CPT,
			)
		);
		$this->assertNotWPError( $p );

		// Set leavel 1 category.
		$level_1 = wp_create_term( 'Level 1', BDLMS_QUIZ_TAXONOMY_LEVEL_1 );
		$this->assertNotWPError( $level_1 );

		$set_level_1 = wp_set_post_terms( $q->ID, $level_1['term_id'], BDLMS_QUIZ_TAXONOMY_LEVEL_1 );
		$this->assertNotWPError( $set_level_1 );

		// Set leavel 2 category.
		$level_2 = wp_create_term( 'Level 2', BDLMS_QUIZ_TAXONOMY_LEVEL_2 );
		$this->assertNotWPError( $level_2 );

		$set_level_2 = wp_set_post_terms( $q->ID, $level_2['term_id'], BDLMS_QUIZ_TAXONOMY_LEVEL_2 );
		$this->assertNotWPError( $set_level_2 );

		$_POST['_bdlms_quiz'] = array(
			'question_id' => array( $q ),
			'settings'    => array(
				'duration'            => 3,
				'duration_type'       => 'day',
				'passing_marks'       => 10,
				'negative_marking'    => 1,
				'review'              => 1,
				'show_correct_review' => 1,
			),
		);
		$post                 = get_post( $p->ID );
		do_action( 'save_post_' . BDLMS_QUIZ_CPT, $p->ID, $p );
		$groups = get_post_meta( $p->ID, META_KEY_QUIZ_GROUPS, true );
		if ( empty( $groups ) ) {
			$this->assertTrue( false );
		}
		foreach ( $groups as $group ) {
			$data    = get_post_meta( $p->ID, $group, true );
			$keyname = str_replace( array( '_bdlms_quiz_', 'question_ids' ), array( '', 'question_id' ), $group );
			if ( isset( $_POST['_bdlms_quiz'][ $keyname ] ) && is_array( $_POST['_bdlms_quiz'][ $keyname ] ) ) {
				$this->assertEquals( $_POST['_bdlms_quiz'][ $keyname ], $data );
			} else {
				$this->assertSame( sanitize_text_field( wp_unslash( $_POST['_bdlms_quiz'][ $keyname ] ) ), $data );
			}
		}
	}

	/**
	 * Get quizzes.
	 */
	public function test_get_quizzes() {
		$quizzes = get_posts(
			array(
				'post_type'      => BDLMS_QUIZ_CPT,
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		);
		$this->assertNotEmpty( $quizzes );
	}

	/**
	 * Check category|tag exists or not.
	 */
	public function test_category_tag_exists() {
		$level_1 = term_exists( 'Level 1', BDLMS_QUIZ_TAXONOMY_LEVEL_1 );
		$this->assertNotEmpty( $level_1 );

		$level_2 = term_exists( 'Level 2', BDLMS_QUIZ_TAXONOMY_LEVEL_2 );
		$this->assertNotEmpty( $level_2 );
	}
}
