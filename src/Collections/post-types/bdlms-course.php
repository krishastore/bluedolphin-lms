<?php
/**
 * Course post type collection.
 *
 * @package BlueDolphin\Lms
 */

namespace BlueDolphin\Lms\Collections\PostType;

use const BlueDolphin\Lms\BDLMS_COURSE_CPT;
use const BlueDolphin\Lms\PARENT_MENU_SLUG;

/**
 * Registers the `bdlms_course` post type.
 */
function bdlms_course_init() {
	register_post_type(
		BDLMS_COURSE_CPT,
		array(
			'labels'                => array(
				'name'                  => __( 'Courses', 'bluedolphin-lms' ),
				'singular_name'         => __( 'Course', 'bluedolphin-lms' ),
				'all_items'             => __( 'Courses', 'bluedolphin-lms' ),
				'archives'              => __( 'Course Archives', 'bluedolphin-lms' ),
				'attributes'            => __( 'Course Attributes', 'bluedolphin-lms' ),
				'insert_into_item'      => __( 'Insert into Course', 'bluedolphin-lms' ),
				'uploaded_to_this_item' => __( 'Uploaded to this Course', 'bluedolphin-lms' ),
				'featured_image'        => _x( 'Featured Image', 'bdlms_course', 'bluedolphin-lms' ),
				'set_featured_image'    => _x( 'Set featured image', 'bdlms_course', 'bluedolphin-lms' ),
				'remove_featured_image' => _x( 'Remove featured image', 'bdlms_course', 'bluedolphin-lms' ),
				'use_featured_image'    => _x( 'Use as featured image', 'bdlms_course', 'bluedolphin-lms' ),
				'filter_items_list'     => __( 'Filter Courses list', 'bluedolphin-lms' ),
				'items_list_navigation' => __( 'Courses list navigation', 'bluedolphin-lms' ),
				'items_list'            => __( 'Courses list', 'bluedolphin-lms' ),
				'new_item'              => __( 'New Course', 'bluedolphin-lms' ),
				'add_new'               => __( 'Add New', 'bluedolphin-lms' ),
				'add_new_item'          => __( 'Add New Course', 'bluedolphin-lms' ),
				'edit_item'             => __( 'Edit Course', 'bluedolphin-lms' ),
				'view_item'             => __( 'View Course', 'bluedolphin-lms' ),
				'view_items'            => __( 'View Courses', 'bluedolphin-lms' ),
				'search_items'          => __( 'Search Courses', 'bluedolphin-lms' ),
				'not_found'             => __( 'No Courses found', 'bluedolphin-lms' ),
				'not_found_in_trash'    => __( 'No Courses found in trash', 'bluedolphin-lms' ),
				'parent_item_colon'     => __( 'Parent Course:', 'bluedolphin-lms' ),
				'menu_name'             => __( 'Courses', 'bluedolphin-lms' ),
			),
			'public'                => true,
			'hierarchical'          => false,
			'show_in_menu'          => PARENT_MENU_SLUG,
			'show_ui'               => true,
			'show_in_nav_menus'     => true,
			'supports'              => array( 'title', 'editor', 'thumbnail', 'revisions', 'comments', 'excerpt' ),
			'has_archive'           => true,
			'rewrite'               => true,
			'query_var'             => true,
			'menu_position'         => null,
			'menu_icon'             => null,
			'show_in_rest'          => true,
			'rest_base'             => BDLMS_COURSE_CPT,
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		)
	);
}

add_action( 'init', __NAMESPACE__ . '\\bdlms_course_init' );

/**
 * Sets the post updated messages for the `bdlms_course` post type.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `bdlms_course` post type.
 */
function bdlms_course_updated_messages( $messages ) {
	global $post;

	$permalink = get_permalink( $post );

	$messages[ BDLMS_COURSE_CPT ] = array(
		0  => '', // Unused. Messages start at index 1.
		/* translators: %s: post permalink */
		1  => sprintf( __( 'Course updated. <a target="_blank" href="%s">View Course</a>', 'bluedolphin-lms' ), esc_url( $permalink ) ),
		2  => __( 'Custom field updated.', 'bluedolphin-lms' ),
		3  => __( 'Custom field deleted.', 'bluedolphin-lms' ),
		4  => __( 'Course updated.', 'bluedolphin-lms' ),
		/* translators: %s: date and time of the revision */
		5  => isset( $_GET['revision'] ) ? sprintf( __( 'Course restored to revision from %s', 'bluedolphin-lms' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false, // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		/* translators: %s: post permalink */
		6  => sprintf( __( 'Course published. <a href="%s">View Course</a>', 'bluedolphin-lms' ), esc_url( $permalink ) ),
		7  => __( 'Course saved.', 'bluedolphin-lms' ),
		/* translators: %s: post permalink */
		8  => sprintf( __( 'Course submitted. <a target="_blank" href="%s">Preview Course</a>', 'bluedolphin-lms' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
		9  => sprintf( __( 'Course scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Course</a>', 'bluedolphin-lms' ), date_i18n( __( 'M j, Y @ G:i', 'bluedolphin-lms' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
		/* translators: %s: post permalink */
		10 => sprintf( __( 'Course draft updated. <a target="_blank" href="%s">Preview Course</a>', 'bluedolphin-lms' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	);

	return $messages;
}

add_filter( 'post_updated_messages', __NAMESPACE__ . '\\bdlms_course_updated_messages' );

/**
 * Sets the bulk post updated messages for the `bdlms_course` post type.
 *
 * @param  array $bulk_messages Arrays of messages, each keyed by the corresponding post type. Messages are
 *                              keyed with 'updated', 'locked', 'deleted', 'trashed', and 'untrashed'.
 * @param  int[] $bulk_counts   Array of item counts for each message, used to build internationalized strings.
 * @return array Bulk messages for the `bdlms_course` post type.
 */
function bdlms_course_bulk_updated_messages( $bulk_messages, $bulk_counts ) {
	global $post;

	$bulk_messages[ BDLMS_COURSE_CPT ] = array(
		/* translators: %s: Number of Courses. */
		'updated'   => _n( '%s Course updated.', '%s Courses updated.', $bulk_counts['updated'], 'bluedolphin-lms' ),
		'locked'    => ( 1 === $bulk_counts['locked'] ) ? __( '1 Course not updated, somebody is editing it.', 'bluedolphin-lms' ) :
						/* translators: %s: Number of Courses. */
						_n( '%s Course not updated, somebody is editing it.', '%s Courses not updated, somebody is editing them.', $bulk_counts['locked'], 'bluedolphin-lms' ),
		/* translators: %s: Number of Courses. */
		'deleted'   => _n( '%s Course permanently deleted.', '%s Courses permanently deleted.', $bulk_counts['deleted'], 'bluedolphin-lms' ),
		/* translators: %s: Number of Courses. */
		'trashed'   => _n( '%s Course moved to the Trash.', '%s Courses moved to the Trash.', $bulk_counts['trashed'], 'bluedolphin-lms' ),
		/* translators: %s: Number of Courses. */
		'untrashed' => _n( '%s Course restored from the Trash.', '%s Courses restored from the Trash.', $bulk_counts['untrashed'], 'bluedolphin-lms' ),
	);

	return $bulk_messages;
}

add_filter( 'bulk_post_updated_messages', __NAMESPACE__ . '\\bdlms_course_bulk_updated_messages', 10, 2 );
