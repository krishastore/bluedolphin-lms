<?php
/**
 * Course Level taxonomy.
 *
 * @package BlueDolphin\Lms
 */

namespace BlueDolphin\Lms\Collections\Taxonomies;

use const BlueDolphin\Lms\BDLMS_COURSE_TAXONOMY_TAG;
use const BlueDolphin\Lms\BDLMS_COURSE_CPT;

/**
 * Registers the `bdlms_course_tag` taxonomy,
 * for use with 'bdlms_course'.
 */
function bdlms_course_tag_init() {
	register_taxonomy(
		BDLMS_COURSE_TAXONOMY_TAG,
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
				'name'                       => __( 'Course Levels', 'bluedolphin-lms' ),
				'singular_name'              => _x( 'Course Levels', 'taxonomy general name', 'bluedolphin-lms' ),
				'search_items'               => __( 'Search Course Levels', 'bluedolphin-lms' ),
				'popular_items'              => __( 'Popular Course Levels', 'bluedolphin-lms' ),
				'all_items'                  => __( 'All Course Levels', 'bluedolphin-lms' ),
				'parent_item'                => __( 'Parent Level', 'bluedolphin-lms' ),
				'parent_item_colon'          => __( 'Parent Level:', 'bluedolphin-lms' ),
				'edit_item'                  => __( 'Edit Course Level', 'bluedolphin-lms' ),
				'update_item'                => __( 'Update Level', 'bluedolphin-lms' ),
				'view_item'                  => __( 'View Level', 'bluedolphin-lms' ),
				'add_new_item'               => __( 'Add New Level', 'bluedolphin-lms' ),
				'new_item_name'              => __( 'New Level', 'bluedolphin-lms' ),
				'separate_items_with_commas' => __( 'Separate Course Levels with commas', 'bluedolphin-lms' ),
				'add_or_remove_items'        => __( 'Add or remove Course Levels', 'bluedolphin-lms' ),
				'choose_from_most_used'      => __( 'Choose from the most used Course Levels', 'bluedolphin-lms' ),
				'not_found'                  => __( 'No Levels found.', 'bluedolphin-lms' ),
				'no_terms'                   => __( 'No Levels', 'bluedolphin-lms' ),
				'menu_name'                  => __( 'Tags', 'bluedolphin-lms' ),
				'items_list_navigation'      => __( 'Course Levels list navigation', 'bluedolphin-lms' ),
				'items_list'                 => __( 'Course Levels list', 'bluedolphin-lms' ),
				'most_used'                  => _x( 'Most Used', 'bdlms_course_tag', 'bluedolphin-lms' ),
				'back_to_items'              => __( '&larr; Back to Course Levels', 'bluedolphin-lms' ),
			),
			'show_in_rest'          => true,
			'rest_base'             => BDLMS_COURSE_TAXONOMY_TAG,
			'rest_controller_class' => 'WP_REST_Terms_Controller',
		)
	);
}

add_action( 'init', __NAMESPACE__ . '\\bdlms_course_tag_init' );

/**
 * Sets the post updated messages for the `bdlms_course_tag` taxonomy.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `bdlms_course_tag` taxonomy.
 */
function bdlms_course_tag_updated_messages( $messages ) {

	$messages[ BDLMS_COURSE_TAXONOMY_TAG ] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => __( 'Level added.', 'bluedolphin-lms' ),
		2 => __( 'Level deleted.', 'bluedolphin-lms' ),
		3 => __( 'Level updated.', 'bluedolphin-lms' ),
		4 => __( 'Level not added.', 'bluedolphin-lms' ),
		5 => __( 'Level not updated.', 'bluedolphin-lms' ),
		6 => __( 'Level deleted.', 'bluedolphin-lms' ),
	);
	return $messages;
}

add_filter( 'term_updated_messages', __NAMESPACE__ . '\\bdlms_course_tag_updated_messages' );
