<?php
/**
 * Order post type collection.
 *
 * @package BlueDolphin\Lms
 */

namespace BlueDolphin\Lms\Collections\PostType;

use const BlueDolphin\Lms\BDLMS_ORDER_CPT;

/**
 * Registers the `bdlms_order` post type.
 */
function bdlms_order_init() {
	register_post_type(
		BDLMS_ORDER_CPT,
		array(
			'labels'                => array(
				'name'                  => __( 'Orders', 'bluedolphin-lms' ),
				'singular_name'         => __( 'Order', 'bluedolphin-lms' ),
				'all_items'             => __( 'Orders', 'bluedolphin-lms' ),
				'archives'              => __( 'Order Archives', 'bluedolphin-lms' ),
				'attributes'            => __( 'Order Attributes', 'bluedolphin-lms' ),
				'insert_into_item'      => __( 'Insert into order', 'bluedolphin-lms' ),
				'uploaded_to_this_item' => __( 'Uploaded to this order', 'bluedolphin-lms' ),
				'featured_image'        => _x( 'Featured Image', 'bdlms_order', 'bluedolphin-lms' ),
				'set_featured_image'    => _x( 'Set featured image', 'bdlms_order', 'bluedolphin-lms' ),
				'remove_featured_image' => _x( 'Remove featured image', 'bdlms_order', 'bluedolphin-lms' ),
				'use_featured_image'    => _x( 'Use as featured image', 'bdlms_order', 'bluedolphin-lms' ),
				'filter_items_list'     => __( 'Filter orders list', 'bluedolphin-lms' ),
				'items_list_navigation' => __( 'Orders list navigation', 'bluedolphin-lms' ),
				'items_list'            => __( 'Orders list', 'bluedolphin-lms' ),
				'new_item'              => __( 'New order', 'bluedolphin-lms' ),
				'add_new'               => __( 'Add New', 'bluedolphin-lms' ),
				'add_new_item'          => __( 'Add New order', 'bluedolphin-lms' ),
				'edit_item'             => __( 'Edit order', 'bluedolphin-lms' ),
				'view_item'             => __( 'View order', 'bluedolphin-lms' ),
				'view_items'            => __( 'View order', 'bluedolphin-lms' ),
				'search_items'          => __( 'Search orders', 'bluedolphin-lms' ),
				'not_found'             => __( 'No orders found', 'bluedolphin-lms' ),
				'not_found_in_trash'    => __( 'No orders found in trash', 'bluedolphin-lms' ),
				'parent_item_colon'     => __( 'Parent order:', 'bluedolphin-lms' ),
				'menu_name'             => __( 'Orders', 'bluedolphin-lms' ),
			),
			'public'                => true,
			'hierarchical'          => false,
			'show_in_menu'          => 'bluedolphin-lms',
			'show_ui'               => true,
			'show_in_nav_menus'     => true,
			'supports'              => array( 'title', 'editor' ),
			'has_archive'           => true,
			'rewrite'               => true,
			'query_var'             => true,
			'menu_position'         => null,
			'menu_icon'             => null,
			'show_in_rest'          => true,
			'rest_base'             => BDLMS_ORDER_CPT,
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		)
	);
}

add_action( 'init', __NAMESPACE__ . '\\bdlms_order_init' );

/**
 * Sets the post updated messages for the `bdlms_order` post type.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `bdlms_order` post type.
 */
function bdlms_order_updated_messages( $messages ) {
	global $post;

	$permalink = get_permalink( $post );

	$messages[ BDLMS_ORDER_CPT ] = array(
		0  => '', // Unused. Messages start at index 1.
		/* translators: %s: post permalink */
		1  => sprintf( __( 'Order updated. <a target="_blank" href="%s">View order</a>', 'bluedolphin-lms' ), esc_url( $permalink ) ),
		2  => __( 'Custom field updated.', 'bluedolphin-lms' ),
		3  => __( 'Custom field deleted.', 'bluedolphin-lms' ),
		4  => __( 'Order updated.', 'bluedolphin-lms' ),
		/* translators: %s: date and time of the revision */
		5  => isset( $_GET['revision'] ) ? sprintf( __( 'Order restored to revision from %s', 'bluedolphin-lms' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false, // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		/* translators: %s: post permalink */
		6  => sprintf( __( 'Order published. <a href="%s">View order</a>', 'bluedolphin-lms' ), esc_url( $permalink ) ),
		7  => __( 'Order saved.', 'bluedolphin-lms' ),
		/* translators: %s: post permalink */
		8  => sprintf( __( 'Order submitted. <a target="_blank" href="%s">Preview order</a>', 'bluedolphin-lms' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		/* translators: 1: Publish box date format, see https://secure.php.net/date 2: Post permalink */
		9  => sprintf( __( 'Order scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Order</a>', 'bluedolphin-lms' ), date_i18n( __( 'M j, Y @ G:i', 'bluedolphin-lms' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
		/* translators: %s: post permalink */
		10 => sprintf( __( 'Order draft updated. <a target="_blank" href="%s">Preview order</a>', 'bluedolphin-lms' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	);

	return $messages;
}

add_filter( 'post_updated_messages', __NAMESPACE__ . '\\bdlms_order_updated_messages' );

/**
 * Sets the bulk post updated messages for the `bdlms_order` post type.
 *
 * @param  array $bulk_messages Arrays of messages, each keyed by the corresponding post type. Messages are
 *                              keyed with 'updated', 'locked', 'deleted', 'trashed', and 'untrashed'.
 * @param  int[] $bulk_counts   Array of item counts for each message, used to build internationalized strings.
 * @return array Bulk messages for the `bdlms_order` post type.
 */
function bdlms_order_bulk_updated_messages( $bulk_messages, $bulk_counts ) {
	global $post;

	$bulk_messages[ BDLMS_ORDER_CPT ] = array(
		/* translators: %s: Number of Orders. */
		'updated'   => _n( '%s Order updated.', '%s Orders updated.', $bulk_counts['updated'], 'bluedolphin-lms' ),
		'locked'    => ( 1 === $bulk_counts['locked'] ) ? __( '1 Order not updated, somebody is editing it.', 'bluedolphin-lms' ) :
						/* translators: %s: Number of Orders. */
						_n( '%s Order not updated, somebody is editing it.', '%s Orders not updated, somebody is editing them.', $bulk_counts['locked'], 'bluedolphin-lms' ),
		/* translators: %s: Number of Orders. */
		'deleted'   => _n( '%s Order permanently deleted.', '%s Orders permanently deleted.', $bulk_counts['deleted'], 'bluedolphin-lms' ),
		/* translators: %s: Number of Orders. */
		'trashed'   => _n( '%s Order moved to the Trash.', '%s Orders moved to the Trash.', $bulk_counts['trashed'], 'bluedolphin-lms' ),
		/* translators: %s: Number of Orders. */
		'untrashed' => _n( '%s Order restored from the Trash.', '%s Orders restored from the Trash.', $bulk_counts['untrashed'], 'bluedolphin-lms' ),
	);

	return $bulk_messages;
}

add_filter( 'bulk_post_updated_messages', __NAMESPACE__ . '\\bdlms_order_bulk_updated_messages', 10, 2 );
