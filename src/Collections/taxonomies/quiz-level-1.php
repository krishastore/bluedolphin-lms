<?php
/**
 * Quiz taxonomy.
 *
 * @package BD\Lms
 */

namespace BD\Lms\Collections\Taxonomies;

use const BD\Lms\BDLMS_QUIZ_TAXONOMY_LEVEL_1;
use const BD\Lms\BDLMS_QUIZ_CPT;

/**
 * Registers the `bdlms_quiz_category` taxonomy,
 * for use with 'bdlms_course'.
 */
function bdlms_quiz_level_1_init() {
	register_taxonomy(
		BDLMS_QUIZ_TAXONOMY_LEVEL_1,
		array( BDLMS_QUIZ_CPT ),
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
				'name'                       => __( 'Category (Level 1)', 'bluedolphin-lms' ),
				'singular_name'              => _x( 'Category (Level 1)', 'taxonomy general name', 'bluedolphin-lms' ),
				'search_items'               => __( 'Search Category (Level 1)', 'bluedolphin-lms' ),
				'popular_items'              => __( 'Popular Category (Level 1)', 'bluedolphin-lms' ),
				'all_items'                  => __( 'All Category (Level 1)', 'bluedolphin-lms' ),
				'parent_item'                => __( 'Parent Category (Level 1)', 'bluedolphin-lms' ),
				'parent_item_colon'          => __( 'Parent Category (Level 1):', 'bluedolphin-lms' ),
				'edit_item'                  => __( 'Edit Category (Level 1)', 'bluedolphin-lms' ),
				'update_item'                => __( 'Update Category (Level 1)', 'bluedolphin-lms' ),
				'view_item'                  => __( 'View Category (Level 1)', 'bluedolphin-lms' ),
				'add_new_item'               => __( 'Add New Category (Level 1)', 'bluedolphin-lms' ),
				'new_item_name'              => __( 'New Category (Level 1)', 'bluedolphin-lms' ),
				'separate_items_with_commas' => __( 'Separate Category (Level 1) with commas', 'bluedolphin-lms' ),
				'add_or_remove_items'        => __( 'Add or remove Category (Level 1)', 'bluedolphin-lms' ),
				'choose_from_most_used'      => __( 'Choose from the most used Category (Level 1)', 'bluedolphin-lms' ),
				'not_found'                  => __( 'No Category (Level 1) found.', 'bluedolphin-lms' ),
				'no_terms'                   => __( 'No Category (Level 1)', 'bluedolphin-lms' ),
				'menu_name'                  => __( 'Categories', 'bluedolphin-lms' ),
				'items_list_navigation'      => __( 'Category (Level 1) list navigation', 'bluedolphin-lms' ),
				'items_list'                 => __( 'Category (Level 1) list', 'bluedolphin-lms' ),
				'most_used'                  => _x( 'Most Used', 'bdlms_quiz_category', 'bluedolphin-lms' ),
				'back_to_items'              => __( '&larr; Back to Category (Level 1)', 'bluedolphin-lms' ),
			),
			'show_in_rest'          => true,
			'rest_base'             => BDLMS_QUIZ_TAXONOMY_LEVEL_1,
			'rest_controller_class' => 'WP_REST_Terms_Controller',
		)
	);
}

add_action( 'init', __NAMESPACE__ . '\\bdlms_quiz_level_1_init' );

/**
 * Sets the post updated messages for the `bdlms_quiz_category` taxonomy.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `bdlms_quiz_category` taxonomy.
 */
function bdlms_quiz_level_1_updated_messages( $messages ) {

	$messages[ BDLMS_QUIZ_TAXONOMY_LEVEL_1 ] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => __( 'Category (Level 1) added.', 'bluedolphin-lms' ),
		2 => __( 'Category (Level 1) deleted.', 'bluedolphin-lms' ),
		3 => __( 'Category (Level 1) updated.', 'bluedolphin-lms' ),
		4 => __( 'Category (Level 1) not added.', 'bluedolphin-lms' ),
		5 => __( 'Category (Level 1) not updated.', 'bluedolphin-lms' ),
		6 => __( 'Category (Level 1) deleted.', 'bluedolphin-lms' ),
	);
	return $messages;
}

add_filter( 'term_updated_messages', __NAMESPACE__ . '\\bdlms_quiz_level_1_updated_messages' );
