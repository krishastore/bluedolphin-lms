<?php
/**
 * The file that defines the admin plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BlueDolphin\Lms\Admin
 *
 * phpcs:disable WordPress.NamingConventions.ValidHookName.UseUnderscores
 */

namespace BlueDolphin\Lms\Admin;

use const BlueDolphin\Lms\PARENT_MENU_SLUG;

/**
 * Admin class
 */
class Core implements \BlueDolphin\Lms\Interfaces\AdminCore {

	/**
	 * Plugin version.
	 *
	 * @var int Plugin version.
	 * @since 1.0.0
	 */
	public $version;

	/**
	 * The main instance.
	 *
	 * @var BlueDolphin Main class instance.
	 * @since 1.0.0
	 */
	public $instance;

	/**
	 * Calling class construct.
	 *
	 * @param string $version Plugin version.
	 * @param object $instance Plugin main instance.
	 */
	public function __construct( $version, \BlueDolphin\Lms\BlueDolphin $instance ) { // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint
		$this->version  = $version;
		$this->instance = $instance;

		// Load modules.
		new \BlueDolphin\Lms\Admin\Users\Users();

		// Hooks.
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'backend_scripts' ) );
		add_filter( 'use_block_editor_for_post_type', array( $this, 'disable_gutenberg_editor' ), 10, 2 );
		add_action( 'admin_footer', array( $this, 'js_templates' ) );
	}

	/**
	 * Register admin menu.
	 */
	public function register_admin_menu() {
		$hook = add_menu_page(
			__( 'BlueDolphin LMS', 'bluedolphin-lms' ),
			__( 'BlueDolphin LMS', 'bluedolphin-lms' ),
			apply_filters( 'bluedolphin/menu/capability', 'manage_options' ),
			PARENT_MENU_SLUG,
			array( $this, 'render_menu_page' ),
			'dashicons-welcome-learn-more',
			apply_filters( 'bluedolphin/menu/position', 4 )
		);
	}

	/**
	 * Render admin page.
	 */
	public function render_menu_page() {
		echo 'main page';
	}

	/**
	 * Filters whether a post is able to be edited in the block editor.
	 *
	 * @since 5.0.0
	 *
	 * @param bool   $use_block_editor  Whether the post type can be edited or not. Default true.
	 * @param string $post_type         The post type being checked.
	 */
	public function disable_gutenberg_editor( $use_block_editor, $post_type ) {
		if ( ! $use_block_editor ) {
			return $use_block_editor;
		}
		if ( in_array( $post_type, apply_filters( 'bluedolphin/disable/block-editor', array( \BlueDolphin\Lms\BDLMS_QUESTION_CPT ) ), true ) ) {
			return false;
		}
		return $use_block_editor;
	}

	/**
	 * Enqueue scripts/styles for backend area.
	 */
	public function backend_scripts() {
		wp_register_script( \BlueDolphin\Lms\BDLMS_QUESTION_CPT, BDLMS_ASSETS . '/js/questions.js', array( 'jquery', 'jquery-ui-sortable' ), $this->version, true );
		wp_localize_script(
			\BlueDolphin\Lms\BDLMS_QUESTION_CPT,
			'questionObject',
			array(
				'alphabets' => \BlueDolphin\Lms\question_series(),
			)
		);
		wp_register_style( \BlueDolphin\Lms\BDLMS_QUESTION_CPT, BDLMS_ASSETS . '/css/questions.css', array(), $this->version );
	}

	/**
	 * Load JS based templates.
	 */
	public function js_templates() {
		require_once BDLMS_TEMPLATEPATH . '/admin/question/inline-show-answers.php';
	}
}
