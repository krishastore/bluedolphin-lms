<?php
/**
 * Results post type collection.
 *
 * @package BD\Lms
 */

namespace BD\Lms\Collections\PostType;

use const BD\Lms\BDLMS_RESULTS_CPT;
use const BD\Lms\PARENT_MENU_SLUG;

/**
 * Registers the `bdlms_results` post type.
 */
function bdlms_results_init() {
	register_post_type(
		BDLMS_RESULTS_CPT,
		array(
			'labels'                => array(
				'name'                  => __( 'Results', 'bluedolphin-lms' ),
				'singular_name'         => __( 'Result', 'bluedolphin-lms' ),
				'all_items'             => __( 'Results', 'bluedolphin-lms' ),
				'archives'              => __( 'Result Archives', 'bluedolphin-lms' ),
				'attributes'            => __( 'Result Attributes', 'bluedolphin-lms' ),
				'insert_into_item'      => __( 'Insert into result', 'bluedolphin-lms' ),
				'uploaded_to_this_item' => __( 'Uploaded to this result', 'bluedolphin-lms' ),
				'featured_image'        => _x( 'Featured Image', 'bdlms_results', 'bluedolphin-lms' ),
				'set_featured_image'    => _x( 'Set featured image', 'bdlms_results', 'bluedolphin-lms' ),
				'remove_featured_image' => _x( 'Remove featured image', 'bdlms_results', 'bluedolphin-lms' ),
				'use_featured_image'    => _x( 'Use as featured image', 'bdlms_results', 'bluedolphin-lms' ),
				'filter_items_list'     => __( 'Filter results list', 'bluedolphin-lms' ),
				'items_list_navigation' => __( 'Results list navigation', 'bluedolphin-lms' ),
				'items_list'            => __( 'Results list', 'bluedolphin-lms' ),
				'new_item'              => '',
				'add_new'               => '',
				'add_new_item'          => '',
				'edit_item'             => '',
				'view_item'             => '',
				'view_items'            => '',
				'search_items'          => __( 'Search results', 'bluedolphin-lms' ),
				'not_found'             => __( 'No results found', 'bluedolphin-lms' ),
				'not_found_in_trash'    => __( 'No results found in trash', 'bluedolphin-lms' ),
				'parent_item_colon'     => __( 'Parent result:', 'bluedolphin-lms' ),
				'menu_name'             => __( 'Results', 'bluedolphin-lms' ),
			),
			'capabilities'          => array(
				'create_posts' => 'do_not_allow',
			),
			'map_meta_cap'          => true,
			'publicly_queryable'    => false,
			'public'                => true,
			'hierarchical'          => false,
			'show_in_menu'          => PARENT_MENU_SLUG,
			'show_ui'               => true,
			'show_in_nav_menus'     => true,
			'supports'              => array( 'title' ),
			'register_meta_box_cb'  => array( new \BD\Lms\Admin\MetaBoxes\Results(), 'register_boxes' ),
			'has_archive'           => false,
			'rewrite'               => false,
			'query_var'             => true,
			'show_in_rest'          => true,
			'rest_base'             => BDLMS_RESULTS_CPT,
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		)
	);
}

add_action( 'init', __NAMESPACE__ . '\\bdlms_results_init' );

/**
 * Sets the post updated messages for the `bdlms_results` post type.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `bdlms_results` post type.
 */
function bdlms_results_updated_messages( $messages ) {
	global $post;

	$permalink = get_permalink( $post );

	$messages[ BDLMS_RESULTS_CPT ] = array(
		0  => '', // Unused. Messages start at index 1.
		/* translators: %s: post permalink */
		1  => sprintf( __( 'Result updated. <a target="_blank" href="%s">View result</a>', 'bluedolphin-lms' ), esc_url( $permalink ) ),
		2  => __( 'Custom field updated.', 'bluedolphin-lms' ),
		3  => __( 'Custom field deleted.', 'bluedolphin-lms' ),
		4  => __( 'Result updated.', 'bluedolphin-lms' ),
		/* translators: %s: date and time of the revision */
		5  => isset( $_GET['revision'] ) ? sprintf( __( 'Result restored to revision from %s', 'bluedolphin-lms' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false, // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		/* translators: %s: post permalink */
		6  => sprintf( __( 'Result published. <a href="%s">View result</a>', 'bluedolphin-lms' ), esc_url( $permalink ) ),
		7  => __( 'Result saved.', 'bluedolphin-lms' ),
		/* translators: %s: post permalink */
		8  => sprintf( __( 'Result submitted. <a target="_blank" href="%s">Preview result</a>', 'bluedolphin-lms' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
		9  => sprintf( __( 'Result scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview result</a>', 'bluedolphin-lms' ), date_i18n( __( 'M j, Y @ G:i', 'bluedolphin-lms' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
		/* translators: %s: post permalink */
		10 => sprintf( __( 'Result draft updated. <a target="_blank" href="%s">Preview result</a>', 'bluedolphin-lms' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	);

	return $messages;
}

add_filter( 'post_updated_messages', __NAMESPACE__ . '\\bdlms_results_updated_messages' );

/**
 * Sets the bulk post updated messages for the `bdlms_results` post type.
 *
 * @param  array $bulk_messages Arrays of messages, each keyed by the corresponding post type. Messages are
 *                              keyed with 'updated', 'locked', 'deleted', 'trashed', and 'untrashed'.
 * @param  int[] $bulk_counts   Array of item counts for each message, used to build internationalized strings.
 * @return array Bulk messages for the `bdlms_results` post type.
 */
function bdlms_results_bulk_updated_messages( $bulk_messages, $bulk_counts ) {
	global $post;

	$bulk_messages[ BDLMS_RESULTS_CPT ] = array(
		/* translators: %s: Number of results. */
		'updated'   => _n( '%s Result updated.', '%s Results updated.', $bulk_counts['updated'], 'bluedolphin-lms' ),
		'locked'    => ( 1 === $bulk_counts['locked'] ) ? __( '1 Result not updated, somebody is editing it.', 'bluedolphin-lms' ) :
						/* translators: %s: Number of results. */
						_n( '%s Result not updated, somebody is editing it.', '%s Results not updated, somebody is editing them.', $bulk_counts['locked'], 'bluedolphin-lms' ),
		/* translators: %s: Number of results. */
		'deleted'   => _n( '%s Result permanently deleted.', '%s Results permanently deleted.', $bulk_counts['deleted'], 'bluedolphin-lms' ),
		/* translators: %s: Number of results. */
		'trashed'   => _n( '%s Result moved to the Trash.', '%s Results moved to the Trash.', $bulk_counts['trashed'], 'bluedolphin-lms' ),
		/* translators: %s: Number of results. */
		'untrashed' => _n( '%s Result restored from the Trash.', '%s Results restored from the Trash.', $bulk_counts['untrashed'], 'bluedolphin-lms' ),
	);

	return $bulk_messages;
}

add_filter( 'bulk_post_updated_messages', __NAMESPACE__ . '\\bdlms_results_bulk_updated_messages', 10, 2 );
