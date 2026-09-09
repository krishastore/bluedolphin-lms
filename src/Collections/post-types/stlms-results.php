<?php
/**
 * Results post type collection.
 *
 * @package ST\Lms
 */

namespace ST\Lms\Collections\PostType;

use const ST\Lms\STLMS_RESULTS_CPT;
use const ST\Lms\PARENT_MENU_SLUG;

/**
 * Registers the `stlms_results` post type.
 */
function stlms_results_init() {
	register_post_type(
		STLMS_RESULTS_CPT,
		array(
			'labels'                => array(
				'name'                  => __( 'Results', 'skilltriks' ),
				'singular_name'         => __( 'Result', 'skilltriks' ),
				'all_items'             => __( 'Results', 'skilltriks' ),
				'archives'              => __( 'Result Archives', 'skilltriks' ),
				'attributes'            => __( 'Result Attributes', 'skilltriks' ),
				'insert_into_item'      => __( 'Insert into result', 'skilltriks' ),
				'uploaded_to_this_item' => __( 'Uploaded to this result', 'skilltriks' ),
				'featured_image'        => _x( 'Featured Image', 'stlms_results', 'skilltriks' ),
				'set_featured_image'    => _x( 'Set featured image', 'stlms_results', 'skilltriks' ),
				'remove_featured_image' => _x( 'Remove featured image', 'stlms_results', 'skilltriks' ),
				'use_featured_image'    => _x( 'Use as featured image', 'stlms_results', 'skilltriks' ),
				'filter_items_list'     => __( 'Filter results list', 'skilltriks' ),
				'items_list_navigation' => __( 'Results list navigation', 'skilltriks' ),
				'items_list'            => __( 'Results list', 'skilltriks' ),
				'new_item'              => '',
				'add_new'               => '',
				'add_new_item'          => '',
				'edit_item'             => '',
				'view_item'             => '',
				'view_items'            => '',
				'search_items'          => __( 'Search results', 'skilltriks' ),
				'not_found'             => __( 'No results found', 'skilltriks' ),
				'not_found_in_trash'    => __( 'No results found in trash', 'skilltriks' ),
				'parent_item_colon'     => __( 'Parent result:', 'skilltriks' ),
				'menu_name'             => __( 'Results', 'skilltriks' ),
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
			'supports'              => false,
			'register_meta_box_cb'  => array( new \ST\Lms\Admin\MetaBoxes\Results(), 'register_boxes' ),
			'has_archive'           => false,
			'rewrite'               => false,
			'query_var'             => true,
			'show_in_rest'          => true,
			'rest_base'             => STLMS_RESULTS_CPT,
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		)
	);
}

add_action( 'init', __NAMESPACE__ . '\\stlms_results_init' );

/**
 * Sets the post updated messages for the `stlms_results` post type.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `stlms_results` post type.
 */
function stlms_results_updated_messages( $messages ) {
	global $post;

	$permalink = get_permalink( $post );

	$messages[ STLMS_RESULTS_CPT ] = array(
		0  => '', // Unused. Messages start at index 1.
		/* translators: %s: post permalink */
		1  => sprintf( __( 'Result updated. <a target="_blank" href="%s">View result</a>', 'skilltriks' ), esc_url( $permalink ) ),
		2  => __( 'Custom field updated.', 'skilltriks' ),
		3  => __( 'Custom field deleted.', 'skilltriks' ),
		4  => __( 'Result updated.', 'skilltriks' ),
		5  => isset( $_GET['revision'] ) ? sprintf( // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			/* translators: %s: date and time of the revision */
			__( 'Result restored to revision from %s', 'skilltriks' ),
			wp_post_revision_title( (int) $_GET['revision'], false ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		) : false,
		/* translators: %s: post permalink */
		6  => sprintf( __( 'Result published. <a href="%s">View result</a>', 'skilltriks' ), esc_url( $permalink ) ),
		7  => __( 'Result saved.', 'skilltriks' ),
		8  => sprintf(
			/* translators: %s: post permalink */
			__( 'Result submitted. <a target="_blank" href="%s">Preview result</a>', 'skilltriks' ),
			esc_url( add_query_arg( 'preview', 'true', $permalink ) )
		),
		9  => sprintf(
			/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
			__( 'Result scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview result</a>', 'skilltriks' ),
			date_i18n( __( 'M j, Y @ G:i', 'skilltriks' ), strtotime( $post->post_date ) ),
			esc_url( $permalink )
		),
		10 => sprintf(
			/* translators: %s: post permalink */
			__( 'Result draft updated. <a target="_blank" href="%s">Preview result</a>', 'skilltriks' ),
			esc_url( add_query_arg( 'preview', 'true', $permalink ) )
		),
	);

	return $messages;
}

add_filter( 'post_updated_messages', __NAMESPACE__ . '\\stlms_results_updated_messages' );

/**
 * Sets the bulk post updated messages for the `stlms_results` post type.
 *
 * @param  array $bulk_messages Arrays of messages, each keyed by the corresponding post type. Messages are
 *                              keyed with 'updated', 'locked', 'deleted', 'trashed', and 'untrashed'.
 * @param  int[] $bulk_counts   Array of item counts for each message, used to build internationalized strings.
 * @return array Bulk messages for the `stlms_results` post type.
 */
function stlms_results_bulk_updated_messages( $bulk_messages, $bulk_counts ) {
	global $post;

	$bulk_messages[ STLMS_RESULTS_CPT ] = array(
		/* translators: %s: Number of results. */
		'updated'   => _n( '%s Result updated.', '%s Results updated.', $bulk_counts['updated'], 'skilltriks' ),
		'locked'    => ( 1 === $bulk_counts['locked'] ) ? __( '1 Result not updated, somebody is editing it.', 'skilltriks' ) :
						/* translators: %s: Number of results. */
						_n( '%s Result not updated, somebody is editing it.', '%s Results not updated, somebody is editing them.', $bulk_counts['locked'], 'skilltriks' ),
		/* translators: %s: Number of results. */
		'deleted'   => _n( '%s Result permanently deleted.', '%s Results permanently deleted.', $bulk_counts['deleted'], 'skilltriks' ),
		/* translators: %s: Number of results. */
		'trashed'   => _n( '%s Result moved to the Trash.', '%s Results moved to the Trash.', $bulk_counts['trashed'], 'skilltriks' ),
		/* translators: %s: Number of results. */
		'untrashed' => _n( '%s Result restored from the Trash.', '%s Results restored from the Trash.', $bulk_counts['untrashed'], 'skilltriks' ),
	);

	return $bulk_messages;
}

add_filter( 'bulk_post_updated_messages', __NAMESPACE__ . '\\stlms_results_bulk_updated_messages', 10, 2 );
