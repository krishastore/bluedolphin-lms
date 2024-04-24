<?php
/**
 * Class QuestionTest
 *
 * @package BlueDolphin\Lms\Admin\MetaBoxes
 *
 * phpcs:disable WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput
 */

use const BlueDolphin\Lms\BDLMS_QUESTION_CPT;
use const BlueDolphin\Lms\BDLMS_QUESTION_TAXONOMY_TAG;
use const BlueDolphin\Lms\META_KEY_QUESTION_GROUPS;

/**
 * Question test case.
 */
class QuestionTest extends WP_UnitTestCase {

	/**
	 * Sets up the test methods.
	 */
	public function setUp(): void {
		parent::setUp();
		// avoids error - readfile(/src/wp-includes/js/wp-emoji-loader.js): failed to open stream: No such file or directory.
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		$this->create_question();
	}

	/**
	 * Create question.
	 */
	public function create_question() {
		global $post;

		do_action( 'init' );
		$user_id = $this->factory->user->create(
			array(
				'role' => 'administrator',
			)
		);
		$this->assertIsInt( $user_id );

		wp_set_current_user( $user_id );
		$p = $this->factory->post->create_and_get(
			array(
				'post_title'  => 'Test Create Question',
				'post_type'   => BDLMS_QUESTION_CPT,
				'post_author' => $user_id,
			)
		);
		$this->assertNotWPError( $p );

		$topic = wp_create_term( 'Topic 1', BDLMS_QUESTION_TAXONOMY_TAG );
		$this->assertNotWPError( $topic );

		$set_term = wp_set_post_terms( $p->ID, $topic['term_id'], BDLMS_QUESTION_TAXONOMY_TAG );
		$this->assertNotWPError( $set_term );

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
		$post                     = get_post( $p->ID );
		do_action( 'save_post_' . BDLMS_QUESTION_CPT, $p->ID, $p );
		$groups = get_post_meta( $p->ID, META_KEY_QUESTION_GROUPS, true );
		if ( empty( $groups ) ) {
			$this->assertTrue( false );
		}
		foreach ( $groups as $group ) {
			$data    = get_post_meta( $p->ID, $group, true );
			$keyname = str_replace( '_bdlms_question_', '', $group );
			if ( isset( $_POST['_bdlms_question'][ $keyname ] ) && is_array( $_POST['_bdlms_question'][ $keyname ] ) ) {
				$this->assertEquals( $_POST['_bdlms_question'][ $keyname ], $data );
			} elseif ( 'true_or_false_answers' === $keyname ) {
				$answer_id = $_POST['_bdlms_question'][ $keyname ];
				$this->assertSame( wp_hash( $_POST['_bdlms_question']['true_or_false'][ $answer_id ] ), $data );
			} else {
				$this->assertSame( sanitize_text_field( wp_unslash( $_POST['_bdlms_question'][ $keyname ] ) ), $data );
			}
		}
	}

	/**
	 * Get questions.
	 */
	public function test_get_questions() {
		$questions = get_posts(
			array(
				'post_type'      => BDLMS_QUESTION_CPT,
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		);
		$this->assertNotEmpty( $questions );
	}

	/**
	 * Check tag exists or not.
	 */
	public function test_tag_exists() {
		$tag = term_exists( 'Topic 1', BDLMS_QUESTION_TAXONOMY_TAG );
		$this->assertNotEmpty( $tag );
	}
}
