<?php
/**
 * Class PagesTest
 *
 * @package BlueDolphin\Lms\Admin\Users
 */

/**
 * Pages test case.
 */
class PagesTest extends WP_UnitTestCase {

	/**
	 * Sets up the test methods.
	 */
	public function setUp(): void {
		parent::setUp();
		// avoids error - readfile(/src/wp-includes/js/wp-emoji-loader.js): failed to open stream: No such file or directory.
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	}

	/**
	 * Verify default pages.
	 */
	public function test_verify_pages() {
		\BlueDolphin\Lms\Helpers\Utility::activation_hook();

		$pages = array(
			'All Courses',
			'Login',
			'Term Conditions',
			'My Learning',
		);

		foreach ( $pages as $page ) {
			$this->assertIsInt( post_exists( $page, '', '', 'page', 'publish' ) );
		}
	}
}
