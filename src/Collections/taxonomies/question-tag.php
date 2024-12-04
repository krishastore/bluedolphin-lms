<?php
/**
 * Question tag taxonomy.
 *
 * @package BD\Lms
 */

namespace BD\Lms\Collections\Taxonomies;

use const BD\Lms\BDLMS_QUESTION_TAXONOMY_TAG;
use const BD\Lms\BDLMS_QUESTION_CPT;

/**
 * Registers the `bdlms_quesion_tag` taxonomy,
 * for use with 'bdlms_question'.
 */
function bdlms_quesion_tag_init() {
	register_taxonomy(
		BDLMS_QUESTION_TAXONOMY_TAG,
		array( BDLMS_QUESTION_CPT ),
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
				'name'                       => __( 'Topic', 'bluedolphin-lms' ),
				'singular_name'              => _x( 'Topic', 'taxonomy general name', 'bluedolphin-lms' ),
				'search_items'               => __( 'Search topic', 'bluedolphin-lms' ),
				'popular_items'              => __( 'Popular topic', 'bluedolphin-lms' ),
				'all_items'                  => __( 'All Topic', 'bluedolphin-lms' ),
				'parent_item'                => __( 'Parent Topic', 'bluedolphin-lms' ),
				'parent_item_colon'          => __( 'Parent Topic:', 'bluedolphin-lms' ),
				'edit_item'                  => __( 'Edit Topic', 'bluedolphin-lms' ),
				'update_item'                => __( 'Update Topic', 'bluedolphin-lms' ),
				'view_item'                  => __( 'View Topic', 'bluedolphin-lms' ),
				'add_new_item'               => __( 'Add New Topic', 'bluedolphin-lms' ),
				'new_item_name'              => __( 'New Topic', 'bluedolphin-lms' ),
				'separate_items_with_commas' => __( 'Separate topic with commas', 'bluedolphin-lms' ),
				'add_or_remove_items'        => __( 'Add or remove topic', 'bluedolphin-lms' ),
				'choose_from_most_used'      => __( 'Choose from the most used topic', 'bluedolphin-lms' ),
				'not_found'                  => __( 'No topics found.', 'bluedolphin-lms' ),
				'no_terms'                   => __( 'No topics', 'bluedolphin-lms' ),
				'menu_name'                  => __( 'Topics', 'bluedolphin-lms' ),
				'items_list_navigation'      => __( 'Topic list navigation', 'bluedolphin-lms' ),
				'items_list'                 => __( 'Topic list', 'bluedolphin-lms' ),
				'most_used'                  => _x( 'Most Used', 'bdlms_quesion_tag', 'bluedolphin-lms' ),
				'back_to_items'              => __( '&larr; Back to topic', 'bluedolphin-lms' ),
			),
			'show_in_rest'          => true,
			'rest_base'             => BDLMS_QUESTION_TAXONOMY_TAG,
			'rest_controller_class' => 'WP_REST_Terms_Controller',
		)
	);
}

add_action( 'init', __NAMESPACE__ . '\\bdlms_quesion_tag_init' );

/**
 * Sets the post updated messages for the `bdlms_quesion_tag` taxonomy.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `bdlms_quesion_tag` taxonomy.
 */
function bdlms_quesion_tag_updated_messages( $messages ) {

	$messages[ BDLMS_QUESTION_TAXONOMY_TAG ] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => __( 'Topic added.', 'bluedolphin-lms' ),
		2 => __( 'Topic deleted.', 'bluedolphin-lms' ),
		3 => __( 'Topic updated.', 'bluedolphin-lms' ),
		4 => __( 'Topic not added.', 'bluedolphin-lms' ),
		5 => __( 'Topic not updated.', 'bluedolphin-lms' ),
		6 => __( 'Topic deleted.', 'bluedolphin-lms' ),
	);
	return $messages;
}

add_filter( 'term_updated_messages', __NAMESPACE__ . '\\bdlms_quesion_tag_updated_messages' );
