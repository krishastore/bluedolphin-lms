<?php
/**
 * Class LessonsTest
 *
 * @package BlueDolphin\Lms\Admin\MetaBoxes
 *
 * phpcs:disable WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput
 */

use const BlueDolphin\Lms\BDLMS_LESSON_CPT;
use const BlueDolphin\Lms\BDLMS_LESSON_TAXONOMY_TAG;

/**
 * Question test case.
 */
class LessonsTest extends WP_UnitTestCase {

	/**
	 * Sets up the test methods.
	 */
	public function setUp(): void {
		parent::setUp();
		// avoids error - readfile(/src/wp-includes/js/wp-emoji-loader.js): failed to open stream: No such file or directory.
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		$this->create_lesson();
	}

	/**
	 * Create lesson.
	 */
	public function create_lesson() {
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
				'post_title'  => 'Create First Lesson',
				'post_type'   => BDLMS_LESSON_CPT,
				'post_author' => $user_id,
			)
		);
		$this->assertNotWPError( $p );

		$topic = wp_create_term( 'Lesson Topic 1', BDLMS_LESSON_TAXONOMY_TAG );
		$this->assertNotWPError( $topic );

		$set_term = wp_set_post_terms( $p->ID, $topic['term_id'], BDLMS_LESSON_TAXONOMY_TAG );
		$this->assertNotWPError( $set_term );

		$media = $this->factory->attachment->create_and_get(
			array(
				'post_title' => 'PDF URL 1',
			)
		);
		$this->assertNotWPError( $media );

		$_POST['bdlms_nonce']   = wp_create_nonce( BDLMS_BASEFILE );
		$_POST['_bdlms_lesson'] = array(
			'media'    => array(
				'media_type'      => 'text',
				'video_id'        => 0,
				'embed_video_url' => '',
				'text'            => 'Test TEXT mode',
			),
			'settings' => array(
				'duration'      => 2,
				'duration_type' => 'week',
			),
			'material' => array(
				array(
					'title'        => 'Standards',
					'method'       => 'external',
					'external_url' => 'https://getbluedolphin.com/',
				),
				array(
					'title'    => 'PDF URL 1',
					'method'   => 'upload',
					'media_id' => $media->ID,
				),
			),
		);
		$post                   = get_post( $p->ID );
		do_action( 'save_post_' . BDLMS_LESSON_CPT, $p->ID, $p );

		foreach ( $_POST['_bdlms_lesson'] as $key => $data ) {
			$key        = '_bdlms_lesson_' . $key;
			$saved_data = get_post_meta( $p->ID, $key, true );
			if ( is_array( $data ) ) {
				$this->assertEquals( $data, $saved_data );
			} else {
				$this->assertSame( sanitize_text_field( wp_unslash( $data ) ), $saved_data );
			}
		}
	}

	/**
	 * Get lessons.
	 */
	public function test_get_lessons() {
		$lessons = get_posts(
			array(
				'post_type'      => BDLMS_LESSON_CPT,
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		);
		$this->assertNotEmpty( $lessons );
	}

	/**
	 * Check tag exists or not.
	 */
	public function test_tag_exists() {
		$tag = term_exists( 'Lesson Topic 1', BDLMS_LESSON_TAXONOMY_TAG );
		$this->assertNotEmpty( $tag );
	}
}
