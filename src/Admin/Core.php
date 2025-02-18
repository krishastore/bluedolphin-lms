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
 * @package    BD\Lms\Admin
 *
 * phpcs:disable WordPress.NamingConventions.ValidHookName.UseUnderscores
 */

namespace BD\Lms\Admin;

use const BD\Lms\PARENT_MENU_SLUG;

/**
 * Admin class
 */
class Core implements \BD\Lms\Interfaces\AdminCore {

	/**
	 * Plugin version.
	 *
	 * @var int|string Plugin version.
	 * @since 1.0.0
	 */
	public $version;

	/**
	 * The main instance.
	 *
	 * @var \BD\Lms\Core|null Main class instance.
	 * @since 1.0.0
	 */
	public $instance = null;

	/**
	 * Calling class construct.
	 *
	 * @param int|string   $version Plugin version.
	 * @param \BD\Lms\Core $bdlms_main Plugin main instance.
	 */
	public function __construct( $version, \BD\Lms\Core $bdlms_main ) { // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint
		$this->version  = $version;
		$this->instance = $bdlms_main;

		// Load modules.
		new \BD\Lms\Admin\Users\Users();
		new \BD\Lms\Shortcode\Login();
		new \BD\Lms\Shortcode\Courses();
		new \BD\Lms\Shortcode\UserInfo();
		new \BD\Lms\Shortcode\MyLearning();
		\BD\Lms\Helpers\SettingOptions::instance()->init();
		new \BD\Lms\Import\QuestionImport();
		new \BD\Lms\Import\LessonImport();
		new \BD\Lms\Import\CourseImport();

		// Hooks.
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'backend_scripts' ) );
		add_action( 'admin_footer', array( $this, 'js_templates' ) );
		add_action( 'init', array( $this, 'create_rewrite_rules' ) );
		add_filter( 'use_block_editor_for_post_type', array( $this, 'disable_gutenberg_editor' ), 10, 2 );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
		add_filter( 'show_admin_bar', array( $this, 'show_admin_bar' ) );
	}

	/**
	 * Register admin menu.
	 */
	public function register_admin_menu() {
		$hook = add_menu_page(
			__( 'BlueDolphin LMS', 'bluedolphin-lms' ),
			__( 'BlueDolphin LMS', 'bluedolphin-lms' ),
			apply_filters( 'bdlms/menu/capability', 'manage_options' ),
			PARENT_MENU_SLUG,
			'__return_empty_string',
			'dashicons-welcome-learn-more',
			apply_filters( 'bdlms/menu/position', 4 )
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
		if ( in_array( $post_type, apply_filters( 'bdlms/disable/block-editor', array( \BD\Lms\BDLMS_QUESTION_CPT, \BD\Lms\BDLMS_QUIZ_CPT, \BD\Lms\BDLMS_LESSON_CPT, \BD\Lms\BDLMS_COURSE_CPT ) ), true ) ) {
			return false;
		}
		return $use_block_editor;
	}

	/**
	 * Enqueue scripts/styles for backend area.
	 */
	public function backend_scripts() {
		// Questions.
		wp_register_script( \BD\Lms\BDLMS_QUESTION_CPT, BDLMS_ASSETS . '/js/build/questions.js', array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-dialog' ), $this->version, true );
		$question_object = array(
			'alphabets'      => \BD\Lms\question_series(),
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
			\BD\Lms\BDLMS_QUESTION_CPT,
			'questionObject',
			$question_object
		);
		wp_register_style( \BD\Lms\BDLMS_QUESTION_CPT, BDLMS_ASSETS . '/css/questions.css', array( 'wp-jquery-ui-dialog' ), $this->version );

		// Quiz.
		wp_register_script( \BD\Lms\BDLMS_QUIZ_CPT, BDLMS_ASSETS . '/js/build/quiz.js', array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-dialog' ), $this->version, true );
		wp_localize_script(
			\BD\Lms\BDLMS_QUIZ_CPT,
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
			\BD\Lms\BDLMS_QUIZ_CPT,
			'questionObject',
			$question_object
		);
		wp_register_style( \BD\Lms\BDLMS_QUIZ_CPT, BDLMS_ASSETS . '/css/quiz.css', array( 'wp-jquery-ui-dialog' ), $this->version );

		// Lesson.
		wp_register_script( \BD\Lms\BDLMS_LESSON_CPT, BDLMS_ASSETS . '/js/build/lesson.js', array( 'jquery', 'jquery-ui-dialog' ), $this->version, true );
		wp_localize_script(
			\BD\Lms\BDLMS_LESSON_CPT,
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
		if ( wp_script_is( \BD\Lms\BDLMS_LESSON_CPT ) ) {
			wp_enqueue_media();
			wp_enqueue_editor();
		}
		wp_register_style( \BD\Lms\BDLMS_LESSON_CPT, BDLMS_ASSETS . '/css/lesson.css', array( 'wp-jquery-ui-dialog' ), $this->version );

		// Course.
		wp_register_script( \BD\Lms\BDLMS_COURSE_CPT, BDLMS_ASSETS . '/js/build/course.js', array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-dialog' ), $this->version, true );
		wp_localize_script(
			\BD\Lms\BDLMS_COURSE_CPT,
			'courseObject',
			array(
				'ajaxurl'        => admin_url( 'admin-ajax.php' ),
				'nonce'          => wp_create_nonce( BDLMS_BASEFILE ),
				'HasGdLibrary'   => extension_loaded( 'gd' ),
				'i18n'           => array(
					'PopupTitle'            => __( 'Select Item', 'bluedolphin-lms' ),
					'media_iframe_title'    => __( 'Select file', 'bluedolphin-lms' ),
					'media_iframe_button'   => __( 'Set default file', 'bluedolphin-lms' ),
					'emptyMediaButtonTitle' => __( 'Choose File', 'bluedolphin-lms' ),
					'MediaButtonTitle'      => __( 'Change File', 'bluedolphin-lms' ),
					'nullMediaMessage'      => __( 'No File Chosen', 'bluedolphin-lms' ),
					'emptySearchResult'     => __( 'No results found', 'bluedolphin-lms' ),
					'errorMediaMessage'     => __( 'Bluedolphin required PHP `zip` and `GD` extension for external library.', 'bluedolphin-lms' ),
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
		if ( wp_script_is( \BD\Lms\BDLMS_COURSE_CPT ) ) {
			wp_enqueue_media();
		}
		wp_register_style( \BD\Lms\BDLMS_COURSE_CPT, BDLMS_ASSETS . '/css/course.css', array( 'wp-jquery-ui-dialog' ), $this->version );

		// Settings.
		wp_register_script( \BD\Lms\BDLMS_SETTING, BDLMS_ASSETS . '/js/build/settings.js', array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-dialog' ), $this->version, true );
		wp_localize_script(
			\BD\Lms\BDLMS_SETTING,
			'settingObject',
			array(
				'ajaxurl'         => admin_url( 'admin-ajax.php' ),
				'nonce'           => wp_create_nonce( BDLMS_BASEFILE ),
				'HasOpenSpout'    => class_exists( 'OpenSpout\Reader\Common\Creator\ReaderEntityFactory' ),
				'HasGdLibrary'    => extension_loaded( 'gd' ),
				'QuestionCsvPath' => BDLMS_ASSETS . '/csv/question.csv',
				'LessonCsvPath'   => BDLMS_ASSETS . '/csv/lesson.csv',
				'CourseCsvPath'   => BDLMS_ASSETS . '/csv/course.csv',
				'i18n'            => array(
					'PopupTitle'            => __( 'Import file', 'bluedolphin-lms' ),
					'CancelPopupTitle'      => __( 'Cancel Import', 'bluedolphin-lms' ),
					'ImportRows'            => __( 'Rows', 'bluedolphin-lms' ),
					'ImportColumns'         => __( 'Columns', 'bluedolphin-lms' ),
					'ImportQuestionMsgText' => __( 'Imported Questions to Question Bank', 'bluedolphin-lms' ),
					'ImportLessonMsgText'   => __( 'Imported Lessons', 'bluedolphin-lms' ),
					'ImportCourseMsgText'   => __( 'Imported Courses', 'bluedolphin-lms' ),
					'DemoFileTitle'         => __( 'Demo CSV', 'bluedolphin-lms' ),
					'SuccessTitle'          => __( 'Successful Import', 'bluedolphin-lms' ),
					'FailTitle'             => __( 'Failed Import', 'bluedolphin-lms' ),
					'CancelTitle'           => __( 'Cancelled Import', 'bluedolphin-lms' ),
					'UploadTitle'           => __( 'Upload in Progress', 'bluedolphin-lms' ),
					'emptyMediaButtonTitle' => __( 'Choose File', 'bluedolphin-lms' ),
					'MediaButtonTitle'      => __( 'Change File', 'bluedolphin-lms' ),
					'nullMediaMessage'      => __( 'No File Chosen', 'bluedolphin-lms' ),
					'errorMediaMessage'     => __( 'Bluedolphin required PHP `zip` and `GD` extension for external library.', 'bluedolphin-lms' ),
				),
			)
		);
		wp_register_style( \BD\Lms\BDLMS_SETTING, BDLMS_ASSETS . '/css/settings.css', array( 'wp-jquery-ui-dialog' ), $this->version );

		// Result css.
		wp_register_style( \BD\Lms\BDLMS_RESULTS_CPT, BDLMS_ASSETS . '/css/result.css', array( 'wp-jquery-ui-dialog' ), $this->version );
	}

	/**
	 * Load JS based templates.
	 */
	public function js_templates() {
		require_once BDLMS_TEMPLATEPATH . '/admin/question/inline-show-answers.php';
	}

	/**
	 * Create rewrite rules.
	 */
	public static function create_rewrite_rules() {
		$courses_page_slug = \BD\Lms\get_page_url( 'courses', true );
		add_rewrite_rule( '^' . $courses_page_slug . '/page/?([0-9]{1,})/?$', 'index.php?pagename=' . $courses_page_slug . '&paged=$matches[1]', 'top' );
		add_rewrite_rule( '^' . $courses_page_slug . '/([^/]+)/([0-9]+)/lesson/([0-9]+)/?$', 'index.php?post_type=' . \BD\Lms\BDLMS_COURSE_CPT . '&section=$matches[2]&name=$matches[1]&item_id=$matches[3]&curriculum_type=lesson', 'bottom' );
		add_rewrite_rule( '^' . $courses_page_slug . '/([^/]+)/([0-9]+)/quiz/([0-9]+)/?$', 'index.php?post_type=' . \BD\Lms\BDLMS_COURSE_CPT . '&section=$matches[2]&name=$matches[1]&item_id=$matches[3]&curriculum_type=quiz', 'bottom' );
		$course_result = apply_filters( 'bdlms_course_result_endpoint', 'course-result' );
		add_rewrite_rule( $course_result . '/([0-9]+)[/]?$', 'index.php?course_id=$matches[1]', 'top' );
		if ( ! get_option( 'bdlms_permalinks_flushed', 0 ) ) {
			flush_rewrite_rules( false );
			update_option( 'bdlms_permalinks_flushed', 1 );
		}
	}

	/**
	 * Add query vars.
	 *
	 * @param array $query_vars Query vars.
	 */
	public function add_query_vars( $query_vars ) {
		$query_vars[] = 'item_id';
		$query_vars[] = 'curriculum_type';
		$query_vars[] = 'section';
		$query_vars[] = 'course_id';
		return $query_vars;
	}

	/**
	 * Show admin bar.
	 *
	 * @param bool $show Show admin bar.
	 */
	public function show_admin_bar( $show ) {
		return apply_filters( 'bdlms_show_admin_bar', false );
	}
}
