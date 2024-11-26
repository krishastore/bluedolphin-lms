<?php
/**
 * Class LessonImportTest
 *
 * @package BlueDolphin\Lms\Import
 *
 * phpcs:disable WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput
 */

/**
 * Test the csv file upload
 */
class LessonImportTest extends WP_Ajax_UnitTestCase {

	/**
	 * Test lesson import.
	 */
	public function test_lesson_import() {

		global $wpdb;

		$table_name = $wpdb->prefix . \BlueDolphin\Lms\BDLMS_CRON_TABLE;

		$media      = $this->factory->attachment->create_and_get(
			array(
				'post_title' => 'Lesson.csv',
			)
		);
		$attachment = $this->factory->attachment->create_upload_object( BDLMS_ASSETS . '/csv/lesson.csv', $media->ID );

		$_POST['_nonce']        = wp_create_nonce( BDLMS_BASEFILE );
		$_POST['attachment_id'] = $attachment;
		$_POST['import_type']   = 2;

		try {
			$this->_handleAjax( 'bdlms_get_file_attachment_id' );
		} catch ( WPAjaxDieContinueException $e ) { // phpcs:ignore
			// We expected this, do nothing.
		}

		// Check that the exception was thrown.
		$this->assertTrue( isset( $e ) );
		$response = json_decode( $this->_last_response );
		$this->assertIsObject( $response );
		$this->assertNotEmpty( $response->message );
		$this->assertNotEmpty( $response->id );
		$this->assertIsInt( $response->id );
		$this->assertNotEmpty( $response->attachment_id );
		$this->assertNotEmpty( $response->import_type );
		$this->assertNotEmpty( $response->cron_run_time );

		$id            = $response->id;
		$attachment_id = $response->attachment_id;
		$timestamp     = $response->cron_run_time;
		$import_type   = $response->import_type;

		$hook = 'bdlms_cron_import_' . $id;

		// This returns the timestamp only if we provide matching args.
		$this->assertSame( $timestamp, wp_next_scheduled( $hook, array( $id, $attachment_id ) ) );

		// It's a non-recurring event.
		$this->assertFalse( wp_get_schedule( $hook, array( $id, $attachment_id ) ) );

		$lesson = new \BlueDolphin\Lms\Import\LessonImport();
		$lesson->import_data( $id, $attachment_id );

		$data = $wpdb->get_row( $wpdb->prepare( "SELECT total_rows FROM $table_name WHERE id = %d", $id ), ARRAY_A ); //phpcs:ignore

		$db_total_rows = $data['total_rows'];

		$post_type     = \BlueDolphin\Lms\import_post_type();
		$imported_data = get_posts(
			array(
				'post_type'    => $post_type[ $import_type ],
				'numberposts'  => -1,
				// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_key'     => \BlueDolphin\Lms\META_KEY_IMPORT,
				// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
				'meta_value'   => (string) $id,
				'meta_compare' => '=',
				'fields'       => 'ids',
			)
		);

		$rows = count( $imported_data );

		$this->assertEquals( $db_total_rows, $rows );
	}
}
