<?php
/**
 * Course taxonomy.
 *
 * @package BD\Lms
 */

namespace BD\Lms\Collections\Taxonomies;

use const BD\Lms\BDLMS_COURSE_CATEGORY_TAX;
use const BD\Lms\BDLMS_COURSE_CPT;

/**
 * Registers the `bdlms_course_category` taxonomy,
 * for use with 'bdlms_course'.
 */
function bdlms_course_category_init() {
	register_taxonomy(
		BDLMS_COURSE_CATEGORY_TAX,
		array( BDLMS_COURSE_CPT ),
		array(
			'hierarchical'          => true,
			'public'                => true,
			'show_in_nav_menus'     => true,
			'show_in_menu'          => true,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'query_var'             => true,
			'rewrite'               => true,
			'capabilities'          => array(
				'manage_terms' => 'edit_posts',
				'edit_terms'   => 'edit_posts',
				'delete_terms' => 'edit_posts',
				'assign_terms' => 'edit_posts',
			),
			'labels'                => array(
				'name'                       => __( 'Course Categories', 'bluedolphin-lms' ),
				'singular_name'              => _x( 'Course Categories', 'taxonomy general name', 'bluedolphin-lms' ),
				'search_items'               => __( 'Search Course Categories', 'bluedolphin-lms' ),
				'popular_items'              => __( 'Popular Course Categories', 'bluedolphin-lms' ),
				'all_items'                  => __( 'All Course Categories', 'bluedolphin-lms' ),
				'parent_item'                => __( 'Parent Course Categories', 'bluedolphin-lms' ),
				'parent_item_colon'          => __( 'Parent Course Categories:', 'bluedolphin-lms' ),
				'edit_item'                  => __( 'Edit Course Categories', 'bluedolphin-lms' ),
				'update_item'                => __( 'Update Course Category', 'bluedolphin-lms' ),
				'view_item'                  => __( 'View Course Categories', 'bluedolphin-lms' ),
				'add_new_item'               => __( 'Add New Course Category', 'bluedolphin-lms' ),
				'new_item_name'              => __( 'New Course Category', 'bluedolphin-lms' ),
				'separate_items_with_commas' => __( 'Separate Course Categories with commas', 'bluedolphin-lms' ),
				'add_or_remove_items'        => __( 'Add or remove Course Categories', 'bluedolphin-lms' ),
				'choose_from_most_used'      => __( 'Choose from the most used Course Categories', 'bluedolphin-lms' ),
				'not_found'                  => __( 'No Course Categories found.', 'bluedolphin-lms' ),
				'no_terms'                   => __( 'No Course Categories', 'bluedolphin-lms' ),
				'menu_name'                  => __( 'Categories', 'bluedolphin-lms' ),
				'items_list_navigation'      => __( 'Course Categories list navigation', 'bluedolphin-lms' ),
				'items_list'                 => __( 'Course Categories list', 'bluedolphin-lms' ),
				'most_used'                  => _x( 'Most Used', 'bdlms_course_category', 'bluedolphin-lms' ),
				'back_to_items'              => __( '&larr; Back to Course Categories', 'bluedolphin-lms' ),
			),
			'show_in_rest'          => true,
			'rest_base'             => BDLMS_COURSE_CATEGORY_TAX,
			'rest_controller_class' => 'WP_REST_Terms_Controller',
		)
	);
}

add_action( 'init', __NAMESPACE__ . '\\bdlms_course_category_init' );

/**
 * Sets the post updated messages for the `bdlms_course_category` taxonomy.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `bdlms_course_category` taxonomy.
 */
function bdlms_course_category_updated_messages( $messages ) {

	$messages[ BDLMS_COURSE_CATEGORY_TAX ] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => __( 'Course category added.', 'bluedolphin-lms' ),
		2 => __( 'Course category deleted.', 'bluedolphin-lms' ),
		3 => __( 'Course category updated.', 'bluedolphin-lms' ),
		4 => __( 'Course category not added.', 'bluedolphin-lms' ),
		5 => __( 'Course category not updated.', 'bluedolphin-lms' ),
		6 => __( 'Course category deleted.', 'bluedolphin-lms' ),
	);
	return $messages;
}

add_filter( 'term_updated_messages', __NAMESPACE__ . '\\bdlms_course_category_updated_messages' );
