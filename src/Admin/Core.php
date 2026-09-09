<?php
/**
 * The file that defines the admin plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.skilltriks.com/
 * @since      1.0.0
 *
 * @package    ST\Lms\Admin
 *
 * phpcs:disable WordPress.NamingConventions.ValidHookName.UseUnderscores
 */

namespace ST\Lms\Admin;

use const ST\Lms\PARENT_MENU_SLUG;

/**
 * Admin class
 */
class Core implements \ST\Lms\Interfaces\AdminCore {

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
	 * @var \ST\Lms\Core|null Main class instance.
	 * @since 1.0.0
	 */
	public $instance = null;

	/**
	 * Calling class construct.
	 *
	 * @param int|string   $version Plugin version.
	 * @param \ST\Lms\Core $stlms_main Plugin main instance.
	 */
	public function __construct( $version, \ST\Lms\Core $stlms_main ) { // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint
		$this->version  = $version;
		$this->instance = $stlms_main;

		// Load modules.
		new \ST\Lms\Admin\Users\Users();
		new \ST\Lms\Shortcode\Login();
		new \ST\Lms\Shortcode\Courses();
		new \ST\Lms\Shortcode\UserProfile();
		new \ST\Lms\Shortcode\ForgotPassword();
		new \ST\Lms\Shortcode\Landing();
		new \ST\Lms\Shortcode\MyLearning();
		new \ST\Lms\Shortcode\AssignCourse();
		new \ST\Lms\Shortcode\AssignedCourse();
		new \ST\Lms\Shortcode\AssignNewCourse();
		new \ST\Lms\Shortcode\Notification();
		\ST\Lms\Helpers\SettingOptions::instance()->init();
		\ST\Lms\Notification\DueCourseNotification::instance()->init();
		\ST\Lms\Notification\OverDueCourseNotification::instance()->init();
		\ST\Lms\Notification\DueSoonCourseNotification::instance()->init();
		\ST\Lms\Notification\AdminActivityNotification::instance()->init();
		new \ST\Lms\Import\QuestionImport();
		new \ST\Lms\Import\LessonImport();
		new \ST\Lms\Import\CourseImport();

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
			__( 'SkillTriks LMS', 'skilltriks' ),
			__( 'SkillTriks LMS', 'skilltriks' ),
			apply_filters( 'stlms/menu/capability', 'edit_posts' ),
			PARENT_MENU_SLUG,
			'__return_empty_string',
			'dashicons-welcome-learn-more',
			apply_filters( 'stlms/menu/position', 4 )
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
		if ( in_array(
			$post_type,
			apply_filters(
				'stlms/disable/block-editor',
				array(
					\ST\Lms\STLMS_QUESTION_CPT,
					\ST\Lms\STLMS_QUIZ_CPT,
					\ST\Lms\STLMS_LESSON_CPT,
					\ST\Lms\STLMS_COURSE_CPT,
				)
			),
			true
		) ) {
			return false;
		}
		return $use_block_editor;
	}

	/**
	 * Enqueue scripts/styles for backend area.
	 */
	public function backend_scripts() {
		// Questions.
		wp_register_script(
			\ST\Lms\STLMS_QUESTION_CPT,
			STLMS_ASSETS . '/js/build/questions.js',
			array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-dialog' ),
			$this->version,
			true
		);
		$question_object = array(
			'alphabets'      => \ST\Lms\question_series(),
			'ajaxurl'        => admin_url( 'admin-ajax.php' ),
			'i18n'           => array(
				'PopupTitle'        => __( 'Assign to Quiz', 'skilltriks' ),
				'emptySearchResult' => __( 'No results found', 'skilltriks' ),
			),
			'nonce'          => wp_create_nonce( STLMS_BASEFILE ),
			'contentLoadUrl' => esc_url(
				add_query_arg(
					array(
						'action' => 'load_quiz_list',
						'_nonce' => wp_create_nonce( STLMS_BASEFILE ),
					),
					admin_url( 'admin.php' )
				)
			),
		);
		wp_localize_script(
			\ST\Lms\STLMS_QUESTION_CPT,
			'questionObject',
			$question_object
		);
		wp_register_style( \ST\Lms\STLMS_QUESTION_CPT, STLMS_ASSETS . '/css/questions.css', array( 'wp-jquery-ui-dialog' ), $this->version );

		// Quiz.
		wp_register_script(
			\ST\Lms\STLMS_QUIZ_CPT,
			STLMS_ASSETS . '/js/build/quiz.js',
			array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-dialog' ),
			$this->version,
			true
		);
		wp_localize_script(
			\ST\Lms\STLMS_QUIZ_CPT,
			'quizModules',
			array(
				'ajaxurl'        => admin_url( 'admin-ajax.php' ),
				'nonce'          => wp_create_nonce( STLMS_BASEFILE ),
				'addMoreButton'  => '<a href="javascript:;" class="add-new-question button button-primary">' . __( 'Add More Question', 'skilltriks' ) . '</a>',
				'i18n'           => array(
					'addNewPopupTitle'   => __( 'From where you want to add a new Question?', 'skilltriks' ),
					'existingPopupTitle' => __( 'Questions Bank', 'skilltriks' ),
				),
				'contentLoadUrl' => esc_url(
					add_query_arg(
						array(
							'action' => 'load_question_list',
							'_nonce' => wp_create_nonce( STLMS_BASEFILE ),
						),
						admin_url( 'admin.php' )
					)
				),
			)
		);
		wp_localize_script(
			\ST\Lms\STLMS_QUIZ_CPT,
			'questionObject',
			$question_object
		);
		wp_register_style( \ST\Lms\STLMS_QUIZ_CPT, STLMS_ASSETS . '/css/quiz.css', array( 'wp-jquery-ui-dialog' ), $this->version );

		// Lesson.
		wp_register_script( \ST\Lms\STLMS_LESSON_CPT, STLMS_ASSETS . '/js/build/lesson.js', array( 'jquery', 'jquery-ui-dialog' ), $this->version, true );
		wp_localize_script(
			\ST\Lms\STLMS_LESSON_CPT,
			'lessonObject',
			array(
				'ajaxurl'        => admin_url( 'admin-ajax.php' ),
				'nonce'          => wp_create_nonce( STLMS_BASEFILE ),
				'i18n'           => array(
					'PopupTitle'            => __( 'Select Course', 'skilltriks' ),
					'media_iframe_title'    => __( 'Select file', 'skilltriks' ),
					'media_iframe_button'   => __( 'Set default file', 'skilltriks' ),
					'emptyMediaButtonTitle' => __( 'Choose File', 'skilltriks' ),
					'MediaButtonTitle'      => __( 'Change File', 'skilltriks' ),
					'nullMediaMessage'      => __( 'No File Chosen', 'skilltriks' ),
					'emptySearchResult'     => __( 'No results found', 'skilltriks' ),
				),
				'contentLoadUrl' => esc_url(
					add_query_arg(
						array(
							'action' => 'load_course_list',
							'_nonce' => wp_create_nonce( STLMS_BASEFILE ),
						),
						admin_url( 'admin.php' )
					)
				),
			)
		);
		if ( wp_script_is( \ST\Lms\STLMS_LESSON_CPT ) ) {
			wp_enqueue_media();
			wp_enqueue_editor();
		}
		wp_register_style( \ST\Lms\STLMS_LESSON_CPT, STLMS_ASSETS . '/css/lesson.css', array( 'wp-jquery-ui-dialog' ), $this->version );

		// Course.
		wp_register_script(
			\ST\Lms\STLMS_COURSE_CPT,
			STLMS_ASSETS . '/js/build/course.js',
			array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-dialog' ),
			$this->version,
			true
		);
		wp_localize_script(
			\ST\Lms\STLMS_COURSE_CPT,
			'courseObject',
			array(
				'ajaxurl'        => admin_url( 'admin-ajax.php' ),
				'nonce'          => wp_create_nonce( STLMS_BASEFILE ),
				'HasGdLibrary'   => extension_loaded( 'gd' ),
				'i18n'           => array(
					'PopupTitle'            => __( 'Select Item', 'skilltriks' ),
					'media_iframe_title'    => __( 'Select file', 'skilltriks' ),
					'media_iframe_button'   => __( 'Set default file', 'skilltriks' ),
					'emptyMediaButtonTitle' => __( 'Choose File', 'skilltriks' ),
					'MediaButtonTitle'      => __( 'Change File', 'skilltriks' ),
					'nullMediaMessage'      => __( 'No File Chosen', 'skilltriks' ),
					'emptySearchResult'     => __( 'No results found', 'skilltriks' ),
					'errorMediaMessage'     => __( 'SkillTriks required PHP `zip` and `GD` extension for external library.', 'skilltriks' ),
					// Translators: %s to selected item type.
					'itemAddedMessage'      => __( '%s added', 'skilltriks' ),
				),
				'contentLoadUrl' => esc_url(
					add_query_arg(
						array(
							'action' => 'load_select_items',
							'_nonce' => wp_create_nonce( STLMS_BASEFILE ),
						),
						admin_url( 'admin.php' )
					)
				),
			)
		);
		if ( wp_script_is( \ST\Lms\STLMS_COURSE_CPT ) ) {
			wp_enqueue_media();
		}
		wp_register_style( \ST\Lms\STLMS_COURSE_CPT, STLMS_ASSETS . '/css/course.css', array( 'wp-jquery-ui-dialog' ), $this->version );

		// Settings.
		wp_register_script(
			\ST\Lms\STLMS_SETTING,
			STLMS_ASSETS . '/js/build/settings.js',
			array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-dialog' ),
			$this->version,
			true
		);
		wp_localize_script(
			\ST\Lms\STLMS_SETTING,
			'settingObject',
			array(
				'ajaxurl'         => admin_url( 'admin-ajax.php' ),
				'nonce'           => wp_create_nonce( STLMS_BASEFILE ),
				'HasOpenSpout'    => class_exists( 'OpenSpout\Reader\Common\Creator\ReaderEntityFactory' ),
				'HasGdLibrary'    => extension_loaded( 'gd' ),
				'QuestionCsvPath' => STLMS_ASSETS . '/csv/question.csv',
				'LessonCsvPath'   => STLMS_ASSETS . '/csv/lesson.csv',
				'CourseCsvPath'   => STLMS_ASSETS . '/csv/course.csv',
				'i18n'            => array(
					'PopupTitle'            => __( 'Import file', 'skilltriks' ),
					'CancelPopupTitle'      => __( 'Cancel Import', 'skilltriks' ),
					'ImportRows'            => __( 'Rows', 'skilltriks' ),
					'ImportColumns'         => __( 'Columns', 'skilltriks' ),
					'ImportQuestionMsgText' => __( 'Imported Questions to Question Bank', 'skilltriks' ),
					'ImportLessonMsgText'   => __( 'Imported Lessons', 'skilltriks' ),
					'ImportCourseMsgText'   => __( 'Imported Courses', 'skilltriks' ),
					'DemoFileTitle'         => __( 'Demo CSV', 'skilltriks' ),
					'SuccessTitle'          => __( 'Successful Import', 'skilltriks' ),
					'FailTitle'             => __( 'Failed Import', 'skilltriks' ),
					'CancelTitle'           => __( 'Cancelled Import', 'skilltriks' ),
					'UploadTitle'           => __( 'Upload in Progress', 'skilltriks' ),
					'emptyMediaButtonTitle' => __( 'Choose File', 'skilltriks' ),
					'MediaButtonTitle'      => __( 'Change File', 'skilltriks' ),
					'nullMediaMessage'      => __( 'No File Chosen', 'skilltriks' ),
					'errorMediaMessage'     => __( 'skilltriks required PHP `zip` and `GD` extension for external library.', 'skilltriks' ),
					'RoleTitle'             => __( 'User Role', 'skilltriks' ),
				),
			)
		);
		wp_register_style( \ST\Lms\STLMS_SETTING, STLMS_ASSETS . '/css/settings.css', array( 'wp-jquery-ui-dialog' ), $this->version );

		// Result css.
		wp_register_style( \ST\Lms\STLMS_RESULTS_CPT, STLMS_ASSETS . '/css/result.css', array( 'wp-jquery-ui-dialog' ), $this->version );
	}

	/**
	 * Load JS based templates.
	 */
	public function js_templates() {
		require_once STLMS_TEMPLATEPATH . '/admin/question/inline-show-answers.php';
	}

	/**
	 * Create rewrite rules.
	 */
	public static function create_rewrite_rules() {
		$courses_page_slug = \ST\Lms\get_page_url( 'courses', true );
		add_rewrite_rule( '^' . $courses_page_slug . '/page/?([0-9]{1,})/?$', 'index.php?pagename=' . $courses_page_slug . '&paged=$matches[1]', 'top' );
		add_rewrite_rule(
			'^' . $courses_page_slug . '/([^/]+)/([0-9]+)/lesson/([0-9]+)/?$',
			'index.php?post_type=' . \ST\Lms\STLMS_COURSE_CPT . '&section=$matches[2]&name=$matches[1]&item_id=$matches[3]&curriculum_type=lesson',
			'bottom'
		);
		add_rewrite_rule(
			'^' . $courses_page_slug . '/([^/]+)/([0-9]+)/quiz/([0-9]+)/?$',
			'index.php?post_type=' . \ST\Lms\STLMS_COURSE_CPT . '&section=$matches[2]&name=$matches[1]&item_id=$matches[3]&curriculum_type=quiz',
			'bottom'
		);
		$course_result = apply_filters( 'stlms_course_result_endpoint', 'course-result' );
		add_rewrite_rule( $course_result . '/([0-9]+)[/]?$', 'index.php?course_id=$matches[1]&show_result=1', 'top' );
		if ( ! get_option( 'stlms_permalinks_flushed', 0 ) ) {
			flush_rewrite_rules( false );
			update_option( 'stlms_permalinks_flushed', 1 );
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
		$query_vars[] = 'show_result';
		return $query_vars;
	}

	/**
	 * Show admin bar.
	 *
	 * @param bool $show Show admin bar.
	 */
	public function show_admin_bar( $show ) {
		return apply_filters( 'stlms_show_admin_bar', false );
	}
}
