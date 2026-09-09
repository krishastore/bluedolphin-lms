<?php
/**
 * Course post type collection.
 *
 * @package ST\Lms
 */

namespace ST\Lms\Collections\PostType;

use const ST\Lms\STLMS_COURSE_CPT;
use const ST\Lms\PARENT_MENU_SLUG;

/**
 * Registers the `stlms_course` post type.
 */
function stlms_course_init() {
	$capability = array(
		'edit_post'              => 'edit_course',
		'read_post'              => 'read_course',
		'delete_post'            => 'delete_course',
		'edit_posts'             => 'edit_courses',
		'edit_others_posts'      => 'edit_others_courses',
		'publish_posts'          => 'publish_courses',
		'delete_posts'           => 'delete_courses',
		'delete_published_posts' => 'delete_published_courses',
		'delete_others_posts'    => 'delete_others_courses',
		'edit_published_posts'   => 'edit_published_courses',
		'create_posts'           => 'create_courses',
	);
	$capability = apply_filters( 'stlms/course/capability', $capability );
	register_post_type(
		STLMS_COURSE_CPT,
		array(
			'labels'                => array(
				'name'                  => __( 'Courses', 'skilltriks' ),
				'singular_name'         => __( 'Course', 'skilltriks' ),
				'all_items'             => __( 'Courses', 'skilltriks' ),
				'archives'              => __( 'Course Archives', 'skilltriks' ),
				'attributes'            => __( 'Course Attributes', 'skilltriks' ),
				'insert_into_item'      => __( 'Insert into Course', 'skilltriks' ),
				'uploaded_to_this_item' => __( 'Uploaded to this Course', 'skilltriks' ),
				'featured_image'        => _x( 'Featured Image', 'stlms_course', 'skilltriks' ),
				'set_featured_image'    => _x( 'Set featured image', 'stlms_course', 'skilltriks' ),
				'remove_featured_image' => _x( 'Remove featured image', 'stlms_course', 'skilltriks' ),
				'use_featured_image'    => _x( 'Use as featured image', 'stlms_course', 'skilltriks' ),
				'filter_items_list'     => __( 'Filter Courses list', 'skilltriks' ),
				'items_list_navigation' => __( 'Courses list navigation', 'skilltriks' ),
				'items_list'            => __( 'Courses list', 'skilltriks' ),
				'new_item'              => __( 'New Course', 'skilltriks' ),
				'add_new'               => __( 'Add New', 'skilltriks' ),
				'add_new_item'          => __( 'Add New Course', 'skilltriks' ),
				'edit_item'             => __( 'Edit Course', 'skilltriks' ),
				'view_item'             => __( 'View Course', 'skilltriks' ),
				'view_items'            => __( 'View Courses', 'skilltriks' ),
				'search_items'          => __( 'Search Courses', 'skilltriks' ),
				'not_found'             => __( 'No courses found', 'skilltriks' ),
				'not_found_in_trash'    => __( 'No Courses found in trash', 'skilltriks' ),
				'parent_item_colon'     => __( 'Parent Course:', 'skilltriks' ),
				'menu_name'             => __( 'Courses', 'skilltriks' ),
			),
			'capability_type'       => ! current_user_can( 'manage_options' ) ? 'course' : 'post',
			'map_meta_cap'          => true,
			'capabilities'          => ! current_user_can( 'manage_options' ) ? $capability : array(),
			'publicly_queryable'    => true,
			'public'                => true,
			'hierarchical'          => false,
			'show_in_menu'          => current_user_can( apply_filters( 'stlms/course_menu/capability', 'edit_courses' ) ) ||
				current_user_can( 'manage_options' ) ? PARENT_MENU_SLUG : false,
			'show_ui'               => true,
			'show_in_nav_menus'     => true,
			'supports'              => array( 'title', 'editor', 'thumbnail', 'revisions', 'comments', 'excerpt' ),
			'register_meta_box_cb'  => array( new \ST\Lms\Admin\MetaBoxes\Course(), 'register_boxes' ),
			'has_archive'           => false,
			'rewrite'               => array(
				'slug'       => \ST\Lms\get_page_url( 'courses', true ),
				'with_front' => false,
			),
			'query_var'             => true,
			'show_in_rest'          => true,
			'rest_base'             => STLMS_COURSE_CPT,
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		)
	);
}

add_action( 'init', __NAMESPACE__ . '\\stlms_course_init' );

/**
 * Sets the post updated messages for the `stlms_course` post type.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `stlms_course` post type.
 */
function stlms_course_updated_messages( $messages ) {
	global $post;

	$permalink = get_permalink( $post );

	$messages[ STLMS_COURSE_CPT ] = array(
		0  => '', // Unused. Messages start at index 1.
		/* translators: %s: post permalink */
		1  => sprintf( __( 'Course updated. <a target="_blank" href="%s">View Course</a>', 'skilltriks' ), esc_url( $permalink ) ),
		2  => __( 'Custom field updated.', 'skilltriks' ),
		3  => __( 'Custom field deleted.', 'skilltriks' ),
		4  => __( 'Course updated.', 'skilltriks' ),
		5  => isset( $_GET['revision'] ) ? sprintf( // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			/* translators: %s: date and time of the revision */
			__( 'Course restored to revision from %s', 'skilltriks' ),
			wp_post_revision_title( (int) $_GET['revision'], false ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		) : false,
		/* translators: %s: post permalink */
		6  => sprintf( __( 'Course published. <a href="%s">View Course</a>', 'skilltriks' ), esc_url( $permalink ) ),
		7  => __( 'Course saved.', 'skilltriks' ),
		8  => sprintf(
			/* translators: %s: post permalink */
			__( 'Course submitted. <a target="_blank" href="%s">Preview Course</a>', 'skilltriks' ),
			esc_url( add_query_arg( 'preview', 'true', $permalink ) )
		),
		9  => sprintf(
			/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
			__( 'Course scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Course</a>', 'skilltriks' ),
			date_i18n( __( 'M j, Y @ G:i', 'skilltriks' ), strtotime( $post->post_date ) ),
			esc_url( $permalink )
		),
		10 => sprintf(
			/* translators: %s: post permalink */
			__( 'Course draft updated. <a target="_blank" href="%s">Preview Course</a>', 'skilltriks' ),
			esc_url( add_query_arg( 'preview', 'true', $permalink ) )
		),
	);

	return $messages;
}

add_filter( 'post_updated_messages', __NAMESPACE__ . '\\stlms_course_updated_messages' );

/**
 * Sets the bulk post updated messages for the `stlms_course` post type.
 *
 * @param  array $bulk_messages Arrays of messages, each keyed by the corresponding post type. Messages are
 *                              keyed with 'updated', 'locked', 'deleted', 'trashed', and 'untrashed'.
 * @param  int[] $bulk_counts   Array of item counts for each message, used to build internationalized strings.
 * @return array Bulk messages for the `stlms_course` post type.
 */
function stlms_course_bulk_updated_messages( $bulk_messages, $bulk_counts ) {
	global $post;

	$bulk_messages[ STLMS_COURSE_CPT ] = array(
		/* translators: %s: Number of Courses. */
		'updated'   => _n( '%s Course updated.', '%s Courses updated.', $bulk_counts['updated'], 'skilltriks' ),
		'locked'    => ( 1 === $bulk_counts['locked'] ) ? __( '1 Course not updated, somebody is editing it.', 'skilltriks' ) :
						/* translators: %s: Number of Courses. */
						_n( '%s Course not updated, somebody is editing it.', '%s Courses not updated, somebody is editing them.', $bulk_counts['locked'], 'skilltriks' ),
		/* translators: %s: Number of Courses. */
		'deleted'   => _n( '%s Course permanently deleted.', '%s Courses permanently deleted.', $bulk_counts['deleted'], 'skilltriks' ),
		/* translators: %s: Number of Courses. */
		'trashed'   => _n( '%s Course moved to the Trash.', '%s Courses moved to the Trash.', $bulk_counts['trashed'], 'skilltriks' ),
		/* translators: %s: Number of Courses. */
		'untrashed' => _n( '%s Course restored from the Trash.', '%s Courses restored from the Trash.', $bulk_counts['untrashed'], 'skilltriks' ),
	);

	return $bulk_messages;
}

add_filter( 'bulk_post_updated_messages', __NAMESPACE__ . '\\stlms_course_bulk_updated_messages', 10, 2 );
