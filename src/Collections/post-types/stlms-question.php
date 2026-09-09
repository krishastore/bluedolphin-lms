<?php
/**
 * Question post type collection.
 *
 * @package ST\Lms
 */

namespace ST\Lms\Collections\PostType;

use const ST\Lms\STLMS_QUESTION_CPT;
use const ST\Lms\PARENT_MENU_SLUG;

/**
 * Registers the `stlms_question` post type.
 */
function stlms_question_init() {
	$capability = array(
		'edit_post'              => 'edit_question',
		'read_post'              => 'read_question',
		'delete_post'            => 'delete_question',
		'edit_posts'             => 'edit_questions',
		'edit_others_posts'      => 'edit_others_questions',
		'publish_posts'          => 'publish_questions',
		'delete_posts'           => 'delete_questions',
		'delete_published_posts' => 'delete_published_questions',
		'delete_others_posts'    => 'delete_others_questions',
		'edit_published_posts'   => 'edit_published_questions',
		'create_posts'           => 'create_questions',
	);
	$capability = apply_filters( 'stlms/question/capability', $capability );
	register_post_type(
		STLMS_QUESTION_CPT,
		array(
			'labels'                => array(
				'name'                  => __( 'Question Bank', 'skilltriks' ),
				'singular_name'         => __( 'Question', 'skilltriks' ),
				'all_items'             => __( 'Questions', 'skilltriks' ),
				'archives'              => __( 'Question Archives', 'skilltriks' ),
				'attributes'            => __( 'Question Attributes', 'skilltriks' ),
				'insert_into_item'      => __( 'Insert into question', 'skilltriks' ),
				'uploaded_to_this_item' => __( 'Uploaded to this question', 'skilltriks' ),
				'featured_image'        => _x( 'Featured Image', 'stlms_question', 'skilltriks' ),
				'set_featured_image'    => _x( 'Set featured image', 'stlms_question', 'skilltriks' ),
				'remove_featured_image' => _x( 'Remove featured image', 'stlms_question', 'skilltriks' ),
				'use_featured_image'    => _x( 'Use as featured image', 'stlms_question', 'skilltriks' ),
				'filter_items_list'     => __( 'Filter question list', 'skilltriks' ),
				'items_list_navigation' => __( 'Questions list navigation', 'skilltriks' ),
				'items_list'            => __( 'Questions list', 'skilltriks' ),
				'new_item'              => __( 'New question', 'skilltriks' ),
				'add_new'               => __( 'Add New', 'skilltriks' ),
				'add_new_item'          => __( 'Add New Question', 'skilltriks' ),
				'edit_item'             => __( 'Edit question', 'skilltriks' ),
				'view_item'             => '',
				'view_items'            => '',
				'search_items'          => __( 'Search questions', 'skilltriks' ),
				'not_found'             => __( 'No questions found', 'skilltriks' ),
				'not_found_in_trash'    => __( 'No questions found in trash', 'skilltriks' ),
				'parent_item_colon'     => __( 'Parent question:', 'skilltriks' ),
				'menu_name'             => __( 'Questions', 'skilltriks' ),
			),
			'capability_type'       => ! current_user_can( 'manage_options' ) ? 'question' : 'post',
			'map_meta_cap'          => true,
			'capabilities'          => ! current_user_can( 'manage_options' ) ? $capability : array(),
			'publicly_queryable'    => false,
			'public'                => true,
			'hierarchical'          => false,
			'show_in_menu'          => current_user_can( apply_filters( 'stlms/question_menu/capability', 'edit_questions' ) ) ||
				current_user_can( 'manage_options' ) ? PARENT_MENU_SLUG : false,
			'show_ui'               => true,
			'show_in_nav_menus'     => true,
			'supports'              => array( 'title', 'editor', 'revisions', 'author' ),
			'register_meta_box_cb'  => array( new \ST\Lms\Admin\MetaBoxes\QuestionBank(), 'register_boxes' ),
			'has_archive'           => true,
			'rewrite'               => true,
			'query_var'             => true,
			'show_in_rest'          => true,
			'rest_base'             => STLMS_QUESTION_CPT,
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		)
	);
}

add_action( 'init', __NAMESPACE__ . '\\stlms_question_init' );

/**
 * Sets the post updated messages for the `stlms_question` post type.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `stlms_question` post type.
 */
function stlms_question_updated_messages( $messages ) {
	global $post;

	$permalink = get_permalink( $post );

	$messages[ STLMS_QUESTION_CPT ] = array(
		0  => '', // Unused. Messages start at index 1.
		/* translators: %s: post permalink */
		1  => __( 'Question updated.', 'skilltriks' ),
		2  => __( 'Custom field updated.', 'skilltriks' ),
		3  => __( 'Custom field deleted.', 'skilltriks' ),
		4  => __( 'Question updated.', 'skilltriks' ),
		5  => isset( $_GET['revision'] ) ? sprintf( // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			/* translators: %s: date and time of the revision */
			__( 'Question restored to revision from %s', 'skilltriks' ),
			wp_post_revision_title( (int) $_GET['revision'], false ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		) : false,
		/* translators: %s: post permalink */
		6  => __( 'Question published.', 'skilltriks' ),
		7  => __( 'Question saved.', 'skilltriks' ),
		/* translators: %s: post permalink */
		8  => __( 'Question submitted.', 'skilltriks' ),
		9  => sprintf(
			/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
			__( 'Question scheduled for: <strong>%1$s</strong>.', 'skilltriks' ),
			date_i18n( __( 'M j, Y @ G:i', 'skilltriks' ), strtotime( $post->post_date ) )
		),
		/* translators: %s: post permalink */
		10 => sprintf( __( 'Question draft updated.', 'skilltriks' ) ),
	);

	return $messages;
}

add_filter( 'post_updated_messages', __NAMESPACE__ . '\\stlms_question_updated_messages' );

/**
 * Sets the bulk post updated messages for the `stlms_question` post type.
 *
 * @param  array $bulk_messages Arrays of messages, each keyed by the corresponding post type. Messages are
 *                              keyed with 'updated', 'locked', 'deleted', 'trashed', and 'untrashed'.
 * @param  int[] $bulk_counts   Array of item counts for each message, used to build internationalized strings.
 * @return array Bulk messages for the `stlms_question` post type.
 */
function stlms_question_bulk_updated_messages( $bulk_messages, $bulk_counts ) {
	global $post;

	$bulk_messages[ STLMS_QUESTION_CPT ] = array(
		/* translators: %s: Number of Questions. */
		'updated'   => _n( '%s Question updated.', '%s Questions updated.', $bulk_counts['updated'], 'skilltriks' ),
		'locked'    => ( 1 === $bulk_counts['locked'] ) ? __( '1 Question not updated, somebody is editing it.', 'skilltriks' ) :
						/* translators: %s: Number of Questions. */
						_n( '%s Question not updated, somebody is editing it.', '%s Questions not updated, somebody is editing them.', $bulk_counts['locked'], 'skilltriks' ),
		/* translators: %s: Number of Questions. */
		'deleted'   => _n( '%s question permanently deleted.', '%s Questions permanently deleted.', $bulk_counts['deleted'], 'skilltriks' ),
		/* translators: %s: Number of Questions. */
		'trashed'   => _n( '%s question moved to the Trash.', '%s Questions moved to the Trash.', $bulk_counts['trashed'], 'skilltriks' ),
		/* translators: %s: Number of Questions. */
		'untrashed' => _n( '%s question restored from the Trash.', '%s Questions restored from the Trash.', $bulk_counts['untrashed'], 'skilltriks' ),
	);

	return $bulk_messages;
}

add_filter( 'bulk_post_updated_messages', __NAMESPACE__ . '\\stlms_question_bulk_updated_messages', 10, 2 );
