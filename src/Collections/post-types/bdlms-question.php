<?php
/**
 * Question post type collection.
 *
 * @package BlueDolphin\Lms
 */

namespace BlueDolphin\Lms\Collections\PostType;

use const BlueDolphin\Lms\BDLMS_QUESTION_CPT;
use const BlueDolphin\Lms\PARENT_MENU_SLUG;

/**
 * Registers the `bdlms_question` post type.
 */
function bdlms_question_init() {
	register_post_type(
		BDLMS_QUESTION_CPT,
		array(
			'labels'                => array(
				'name'                  => __( 'Question Bank', 'bluedolphin-lms' ),
				'singular_name'         => __( 'Question', 'bluedolphin-lms' ),
				'all_items'             => __( 'Questions', 'bluedolphin-lms' ),
				'archives'              => __( 'Question Archives', 'bluedolphin-lms' ),
				'attributes'            => __( 'Question Attributes', 'bluedolphin-lms' ),
				'insert_into_item'      => __( 'Insert into question', 'bluedolphin-lms' ),
				'uploaded_to_this_item' => __( 'Uploaded to this question', 'bluedolphin-lms' ),
				'featured_image'        => _x( 'Featured Image', 'bdlms_question', 'bluedolphin-lms' ),
				'set_featured_image'    => _x( 'Set featured image', 'bdlms_question', 'bluedolphin-lms' ),
				'remove_featured_image' => _x( 'Remove featured image', 'bdlms_question', 'bluedolphin-lms' ),
				'use_featured_image'    => _x( 'Use as featured image', 'bdlms_question', 'bluedolphin-lms' ),
				'filter_items_list'     => __( 'Filter question list', 'bluedolphin-lms' ),
				'items_list_navigation' => __( 'Questions list navigation', 'bluedolphin-lms' ),
				'items_list'            => __( 'Questions list', 'bluedolphin-lms' ),
				'new_item'              => __( 'New question', 'bluedolphin-lms' ),
				'add_new'               => __( 'Add New', 'bluedolphin-lms' ),
				'add_new_item'          => __( 'Add New question', 'bluedolphin-lms' ),
				'edit_item'             => __( 'Edit question', 'bluedolphin-lms' ),
				'view_item'             => '',
				'view_items'            => '',
				'search_items'          => __( 'Search questions', 'bluedolphin-lms' ),
				'not_found'             => __( 'No questions found', 'bluedolphin-lms' ),
				'not_found_in_trash'    => __( 'No questions found in trash', 'bluedolphin-lms' ),
				'parent_item_colon'     => __( 'Parent question:', 'bluedolphin-lms' ),
				'menu_name'             => __( 'Questions', 'bluedolphin-lms' ),
			),
			'publicly_queryable'    => false,
			'public'                => true,
			'hierarchical'          => false,
			'show_in_menu'          => PARENT_MENU_SLUG,
			'show_ui'               => true,
			'show_in_nav_menus'     => true,
			'supports'              => array( 'title', 'editor', 'revisions', 'author' ),
			'register_meta_box_cb'  => array( new \BlueDolphin\Lms\Admin\MetaBoxes\QuestionBank(), 'register_boxes' ),
			'has_archive'           => true,
			'rewrite'               => true,
			'query_var'             => true,
			'show_in_rest'          => true,
			'rest_base'             => BDLMS_QUESTION_CPT,
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		)
	);
}

add_action( 'init', __NAMESPACE__ . '\\bdlms_question_init' );

/**
 * Sets the post updated messages for the `bdlms_question` post type.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `bdlms_question` post type.
 */
function bdlms_question_updated_messages( $messages ) {
	global $post;

	$permalink = get_permalink( $post );

	$messages[ BDLMS_QUESTION_CPT ] = array(
		0  => '', // Unused. Messages start at index 1.
		/* translators: %s: post permalink */
		1  => __( 'Question updated.', 'bluedolphin-lms' ),
		2  => __( 'Custom field updated.', 'bluedolphin-lms' ),
		3  => __( 'Custom field deleted.', 'bluedolphin-lms' ),
		4  => __( 'Question updated.', 'bluedolphin-lms' ),
		/* translators: %s: date and time of the revision */
		5  => isset( $_GET['revision'] ) ? sprintf( __( 'Question restored to revision from %s', 'bluedolphin-lms' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false, // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		/* translators: %s: post permalink */
		6  => __( 'Question published.', 'bluedolphin-lms' ),
		7  => __( 'Question saved.', 'bluedolphin-lms' ),
		/* translators: %s: post permalink */
		8  => __( 'Question submitted.', 'bluedolphin-lms' ),
		/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
		9  => sprintf( __( 'Question scheduled for: <strong>%1$s</strong>.', 'bluedolphin-lms' ), date_i18n( __( 'M j, Y @ G:i', 'bluedolphin-lms' ), strtotime( $post->post_date ) ) ),
		/* translators: %s: post permalink */
		10 => sprintf( __( 'Question draft updated.', 'bluedolphin-lms' ) ),
	);

	return $messages;
}

add_filter( 'post_updated_messages', __NAMESPACE__ . '\\bdlms_question_updated_messages' );

/**
 * Sets the bulk post updated messages for the `bdlms_question` post type.
 *
 * @param  array $bulk_messages Arrays of messages, each keyed by the corresponding post type. Messages are
 *                              keyed with 'updated', 'locked', 'deleted', 'trashed', and 'untrashed'.
 * @param  int[] $bulk_counts   Array of item counts for each message, used to build internationalized strings.
 * @return array Bulk messages for the `bdlms_question` post type.
 */
function bdlms_question_bulk_updated_messages( $bulk_messages, $bulk_counts ) {
	global $post;

	$bulk_messages[ BDLMS_QUESTION_CPT ] = array(
		/* translators: %s: Number of Questions. */
		'updated'   => _n( '%s Question updated.', '%s Questions updated.', $bulk_counts['updated'], 'bluedolphin-lms' ),
		'locked'    => ( 1 === $bulk_counts['locked'] ) ? __( '1 Question not updated, somebody is editing it.', 'bluedolphin-lms' ) :
						/* translators: %s: Number of Questions. */
						_n( '%s Question not updated, somebody is editing it.', '%s Questions not updated, somebody is editing them.', $bulk_counts['locked'], 'bluedolphin-lms' ),
		/* translators: %s: Number of Questions. */
		'deleted'   => _n( '%s Question permanently deleted.', '%s Questions permanently deleted.', $bulk_counts['deleted'], 'bluedolphin-lms' ),
		/* translators: %s: Number of Questions. */
		'trashed'   => _n( '%s Question moved to the Trash.', '%s Questions moved to the Trash.', $bulk_counts['trashed'], 'bluedolphin-lms' ),
		/* translators: %s: Number of Questions. */
		'untrashed' => _n( '%s Question restored from the Trash.', '%s Questions restored from the Trash.', $bulk_counts['untrashed'], 'bluedolphin-lms' ),
	);

	return $bulk_messages;
}

add_filter( 'bulk_post_updated_messages', __NAMESPACE__ . '\\bdlms_question_bulk_updated_messages', 10, 2 );
