<?php
/**
 * Class CourseTest
 *
 * @package BlueDolphin\Lms\Admin\MetaBoxes
 *
 * phpcs:disable WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput
 */

use const BlueDolphin\Lms\BDLMS_COURSE_CPT;
use const BlueDolphin\Lms\BDLMS_COURSE_CATEGORY_TAX;
use const BlueDolphin\Lms\BDLMS_COURSE_TAXONOMY_TAG;

/**
 * Course test case.
 */
class CourseTest extends WP_UnitTestCase {

	/**
	 * Sets up the test methods.
	 */
	public function setUp(): void {
		parent::setUp();
		// avoids error - readfile(/src/wp-includes/js/wp-emoji-loader.js): failed to open stream: No such file or directory.
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		$this->create_course();
	}

	/**
	 * Create lesson.
	 */
	public function create_course() {
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
				'post_title'  => 'Create First Course',
				'post_type'   => BDLMS_COURSE_CPT,
				'post_author' => $user_id,
			)
		);
		$this->assertNotWPError( $p );

		$category = wp_create_term( 'WordPress Basic', BDLMS_COURSE_CATEGORY_TAX );
		$this->assertNotWPError( $category );

		$set_term = wp_set_post_terms( $p->ID, $category['term_id'], BDLMS_COURSE_CATEGORY_TAX );
		$this->assertNotWPError( $set_term );

		$tag = wp_create_term( 'Tag 1', BDLMS_COURSE_TAXONOMY_TAG );
		$this->assertNotWPError( $tag );

		$set_tag = wp_set_post_terms( $p->ID, $tag['term_id'], BDLMS_COURSE_TAXONOMY_TAG );
		$this->assertNotWPError( $set_tag );

		$media = $this->factory->attachment->create_and_get(
			array(
				'post_title' => 'PDF URL 1',
			)
		);
		$this->assertNotWPError( $media );

		$signature = $this->factory->attachment->create_and_get(
			array(
				'post_title' => 'Signature URL',
			)
		);
		$this->assertNotWPError( $signature );

		$_POST['bdlms_nonce']   = wp_create_nonce( BDLMS_BASEFILE );
		$_POST['_bdlms_course'] = array(
			'information' => array(
				'requirement'     => array(
					'Course Requirement 1',
					'Course Requirement 2',
				),
				'what_you_learn'  => array(
					'What You\'ll Learn 1',
					'What You\'ll Learn 2',
				),
				'skills_you_gain' => array(
					'Skills You\'ll Gain 1',
					'Skills You\'ll Gain 2',
				),
				'course_includes' => array(
					'Skills You\'ll Gain 1',
				),
				'faq_question'    => array(
					'WP',
					'Laravel',
					'Drupal',
				),
				'faq_answer'      => array(
					'CMS',
					'Framework',
					'CMS',
				),
			),
			'signature'   => array(
				'text'     => 'Test signature',
				'image_id' => $signature->ID,
			),
			'assessment'  => array(
				'evaluation'    => 2,
				'passing_grade' => 70,
			),
			'material'    => array(
				array(
					'title'    => 'Course Material 1',
					'method'   => 'upload',
					'media_id' => $media->ID,
				),
				array(
					'title'    => 'Course Material 2',
					'method'   => 'external',
					'media_id' => 'https://getbluedolphin.com/',
				),
			),
			'curriculum'  => array(
				array(
					'section_name' => 'Section 1',
					'section_desc' => 'Section 1 description',
					'items'        => array(
						483,
						480,
						484,
						485,
					),
				),
				array(
					'section_name' => 'Section 2',
					'section_desc' => 'Section 2 description',
					'items'        => array(
						492,
					),
				),
			),
		);
		$post                   = get_post( $p->ID );
		do_action( 'save_post_' . BDLMS_COURSE_CPT, $p->ID, $p );

		foreach ( $_POST['_bdlms_course'] as $key => $data ) {
			$key        = '_bdlms_course_' . $key;
			$saved_data = get_post_meta( $p->ID, $key, true );
			if ( is_array( $data ) ) {
				$this->assertEquals( $data, $saved_data );
			} else {
				$this->assertSame( sanitize_text_field( wp_unslash( $data ) ), $saved_data );
			}
		}
	}

	/**
	 * Get courses.
	 */
	public function test_get_courses() {
		$courses = get_posts(
			array(
				'post_type'      => BDLMS_COURSE_CPT,
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		);
		$this->assertNotEmpty( $courses );
	}

	/**
	 * Check category exists or not.
	 */
	public function test_category_exists() {
		$category = term_exists( 'WordPress Basic', BDLMS_COURSE_CATEGORY_TAX );
		$this->assertNotEmpty( $category );
	}

	/**
	 * Check course tag exists or not.
	 */
	public function test_tag_exists() {
		$tag = term_exists( 'Tag 1', BDLMS_COURSE_TAXONOMY_TAG );
		$this->assertNotEmpty( $tag );
	}
}
