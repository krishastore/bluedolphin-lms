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
		new \BlueDolphin\Lms\Shortcode\Login();

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
			'__return_empty_string',
			'dashicons-welcome-learn-more',
			apply_filters( 'bluedolphin/menu/position', 4 )
		);
	}

	/**
	 * Render admin page.
	 */
	public function render_menu_page() {
		return '';
	}

	/**
	 * Filters whether a post is able to be edited in the block editor.
	 *
	 * @since 1.0.0
	 *
	 * @param bool   $use_block_editor  Whether the post type can be edited or not. Default true.
	 * @param string $post_type         The post type being checked.
	 */
	public function disable_gutenberg_editor( $use_block_editor, $post_type ) {
		if ( ! $use_block_editor ) {
			return $use_block_editor;
		}
		if ( in_array( $post_type, apply_filters( 'bluedolphin/disable/block-editor', array( \BlueDolphin\Lms\BDLMS_QUESTION_CPT, \BlueDolphin\Lms\BDLMS_QUIZ_CPT, \BlueDolphin\Lms\BDLMS_LESSON_CPT, \BlueDolphin\Lms\BDLMS_COURSE_CPT ) ), true ) ) {
			return false;
		}
		return $use_block_editor;
	}

	/**
	 * Enqueue scripts/styles for backend area.
	 */
	public function backend_scripts() {
		// Questions.
		wp_register_script( \BlueDolphin\Lms\BDLMS_QUESTION_CPT, BDLMS_ASSETS . '/js/build/questions.js', array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-dialog' ), $this->version, true );
		$question_object = array(
			'alphabets'      => \BlueDolphin\Lms\question_series(),
			'ajaxurl'        => admin_url( 'admin-ajax.php' ),
			'i18n'           => array(
				'PopupTitle'        => __( 'Assign to Quiz', 'bluedolphin-lms' ),
				'emptySearchResult' => __( 'No results found', 'bluedolphin-lms' ),
			),
			'nonce'          => wp_create_nonce( BDLMS_BASEFILE ),
			'contentLoadUrl' => esc_url(
				add_query_arg(
					array(
						'action' => 'load_quiz_list',
						'_nonce' => wp_create_nonce( BDLMS_BASEFILE ),
					),
					admin_url( 'admin.php' )
				)
			),
		);
		wp_localize_script(
			\BlueDolphin\Lms\BDLMS_QUESTION_CPT,
			'questionObject',
			$question_object
		);
		wp_register_style( \BlueDolphin\Lms\BDLMS_QUESTION_CPT, BDLMS_ASSETS . '/css/questions.css', array( 'wp-jquery-ui-dialog' ), $this->version );

		// Quiz.
		wp_register_script( \BlueDolphin\Lms\BDLMS_QUIZ_CPT, BDLMS_ASSETS . '/js/build/quiz.js', array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-dialog' ), $this->version, true );
		wp_localize_script(
			\BlueDolphin\Lms\BDLMS_QUIZ_CPT,
			'quizModules',
			array(
				'ajaxurl'        => admin_url( 'admin-ajax.php' ),
				'nonce'          => wp_create_nonce( BDLMS_BASEFILE ),
				'addMoreButton'  => '<a href="javascript:;" class="add-new-question button button-primary">' . __( 'Add More Question', 'bluedolphin-lms' ) . '</a>',
				'i18n'           => array(
					'addNewPopupTitle'   => __( 'From where you want to add a new Question?', 'bluedolphin-lms' ),
					'existingPopupTitle' => __( 'Questions Bank', 'bluedolphin-lms' ),
				),
				'contentLoadUrl' => esc_url(
					add_query_arg(
						array(
							'action' => 'load_question_list',
							'_nonce' => wp_create_nonce( BDLMS_BASEFILE ),
						),
						admin_url( 'admin.php' )
					)
				),
			)
		);
		wp_localize_script(
			\BlueDolphin\Lms\BDLMS_QUIZ_CPT,
			'questionObject',
			$question_object
		);
		wp_register_style( \BlueDolphin\Lms\BDLMS_QUIZ_CPT, BDLMS_ASSETS . '/css/quiz.css', array( 'wp-jquery-ui-dialog' ), $this->version );

		// Lesson.
		wp_register_script( \BlueDolphin\Lms\BDLMS_LESSON_CPT, BDLMS_ASSETS . '/js/build/lesson.js', array( 'jquery', 'jquery-ui-dialog' ), $this->version, true );
		wp_localize_script(
			\BlueDolphin\Lms\BDLMS_LESSON_CPT,
			'lessonObject',
			array(
				'ajaxurl'        => admin_url( 'admin-ajax.php' ),
				'nonce'          => wp_create_nonce( BDLMS_BASEFILE ),
				'i18n'           => array(
					'PopupTitle'            => __( 'Select Course', 'bluedolphin-lms' ),
					'media_iframe_title'    => __( 'Select file', 'bluedolphin-lms' ),
					'media_iframe_button'   => __( 'Set default file', 'bluedolphin-lms' ),
					'emptyMediaButtonTitle' => __( 'Choose File', 'bluedolphin-lms' ),
					'MediaButtonTitle'      => __( 'Change File', 'bluedolphin-lms' ),
					'nullMediaMessage'      => __( 'No File Chosen', 'bluedolphin-lms' ),
					'emptySearchResult'     => __( 'No results found', 'bluedolphin-lms' ),
				),
				'contentLoadUrl' => esc_url(
					add_query_arg(
						array(
							'action' => 'load_course_list',
							'_nonce' => wp_create_nonce( BDLMS_BASEFILE ),
						),
						admin_url( 'admin.php' )
					)
				),
			)
		);
		if ( wp_script_is( \BlueDolphin\Lms\BDLMS_LESSON_CPT ) ) {
			wp_enqueue_media();
			wp_enqueue_editor();
		}
		wp_register_style( \BlueDolphin\Lms\BDLMS_LESSON_CPT, BDLMS_ASSETS . '/css/lesson.css', array( 'wp-jquery-ui-dialog' ), $this->version );

		// Course.
		wp_register_script( \BlueDolphin\Lms\BDLMS_COURSE_CPT, BDLMS_ASSETS . '/js/build/course.js', array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-dialog' ), $this->version, true );
		wp_localize_script(
			\BlueDolphin\Lms\BDLMS_COURSE_CPT,
			'courseObject',
			array(
				'ajaxurl'        => admin_url( 'admin-ajax.php' ),
				'nonce'          => wp_create_nonce( BDLMS_BASEFILE ),
				'i18n'           => array(
					'PopupTitle'            => __( 'Select Item', 'bluedolphin-lms' ),
					'media_iframe_title'    => __( 'Select file', 'bluedolphin-lms' ),
					'media_iframe_button'   => __( 'Set default file', 'bluedolphin-lms' ),
					'emptyMediaButtonTitle' => __( 'Choose File', 'bluedolphin-lms' ),
					'MediaButtonTitle'      => __( 'Change File', 'bluedolphin-lms' ),
					'nullMediaMessage'      => __( 'No File Chosen', 'bluedolphin-lms' ),
					'emptySearchResult'     => __( 'No results found', 'bluedolphin-lms' ),
					// Translators: %s to selected item type.
					'itemAddedMessage'      => __( '%s added', 'bluedolphin-lms' ),
				),
				'contentLoadUrl' => esc_url(
					add_query_arg(
						array(
							'action' => 'load_select_items',
							'_nonce' => wp_create_nonce( BDLMS_BASEFILE ),
						),
						admin_url( 'admin.php' )
					)
				),
			)
		);
		if ( wp_script_is( \BlueDolphin\Lms\BDLMS_COURSE_CPT ) ) {
			wp_enqueue_media();
		}
		wp_register_style( \BlueDolphin\Lms\BDLMS_COURSE_CPT, BDLMS_ASSETS . '/css/course.css', array( 'wp-jquery-ui-dialog' ), $this->version );
	}

	/**
	 * Load JS based templates.
	 */
	public function js_templates() {
		require_once BDLMS_TEMPLATEPATH . '/admin/question/inline-show-answers.php';
	}
}
