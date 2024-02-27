<?php
/**
 * Question tag taxonomy.
 *
 * @package BlueDolphin\Lms
 */

namespace BlueDolphin\Lms\Collections\Taxonomies;

use const BlueDolphin\Lms\BDLMS_QUESTION_TAXONOMY_TAG;
use const BlueDolphin\Lms\BDLMS_QUESTION_CPT;

/**
 * Registers the `bdlms_quesion_tag` taxonomy,
 * for use with 'bdlms_question'.
 */
function bdlms_quesion_tag_init() {
	register_taxonomy(
		BDLMS_QUESTION_TAXONOMY_TAG,
		array( BDLMS_QUESTION_CPT ),
		array(
			'hierarchical'          => false,
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
				'name'                       => __( 'Question Tags', 'bluedolphin-lms' ),
				'singular_name'              => _x( 'Question Tags', 'taxonomy general name', 'bluedolphin-lms' ),
				'search_items'               => __( 'Search Question Tags', 'bluedolphin-lms' ),
				'popular_items'              => __( 'Popular Question Tags', 'bluedolphin-lms' ),
				'all_items'                  => __( 'All Question Tags', 'bluedolphin-lms' ),
				'parent_item'                => __( 'Parent Question Tags', 'bluedolphin-lms' ),
				'parent_item_colon'          => __( 'Parent Question Tags:', 'bluedolphin-lms' ),
				'edit_item'                  => __( 'Edit Question Tag', 'bluedolphin-lms' ),
				'update_item'                => __( 'Update Question Tag', 'bluedolphin-lms' ),
				'view_item'                  => __( 'View Question Tag', 'bluedolphin-lms' ),
				'add_new_item'               => __( 'Add New Question Tag', 'bluedolphin-lms' ),
				'new_item_name'              => __( 'New Question Tags', 'bluedolphin-lms' ),
				'separate_items_with_commas' => __( 'Separate question tags with commas', 'bluedolphin-lms' ),
				'add_or_remove_items'        => __( 'Add or remove question tags', 'bluedolphin-lms' ),
				'choose_from_most_used'      => __( 'Choose from the most used question tags', 'bluedolphin-lms' ),
				'not_found'                  => __( 'No tags found.', 'bluedolphin-lms' ),
				'no_terms'                   => __( 'No tags', 'bluedolphin-lms' ),
				'menu_name'                  => __( 'Tags', 'bluedolphin-lms' ),
				'items_list_navigation'      => __( 'Question tags list navigation', 'bluedolphin-lms' ),
				'items_list'                 => __( 'Question tags list', 'bluedolphin-lms' ),
				'most_used'                  => _x( 'Most Used', 'bdlms_quesion_tag', 'bluedolphin-lms' ),
				'back_to_items'              => __( '&larr; Back to Question Tags', 'bluedolphin-lms' ),
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
		1 => __( 'Tag added.', 'bluedolphin-lms' ),
		2 => __( 'Tag deleted.', 'bluedolphin-lms' ),
		3 => __( 'Tag updated.', 'bluedolphin-lms' ),
		4 => __( 'Tag not added.', 'bluedolphin-lms' ),
		5 => __( 'Tag not updated.', 'bluedolphin-lms' ),
		6 => __( 'Tag deleted.', 'bluedolphin-lms' ),
	);
	return $messages;
}

add_filter( 'term_updated_messages', __NAMESPACE__ . '\\bdlms_quesion_tag_updated_messages' );
