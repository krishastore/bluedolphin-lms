<?php
/**
 * The file that register metabox for course.
 *
 * @link       https://www.skilltriks.com/
 * @since      1.0.0
 *
 * @package    ST\Lms
 *
 * phpcs:disable WordPress.NamingConventions.ValidHookName.UseUnderscores
 */

namespace ST\Lms\Admin\MetaBoxes;

use ST\Lms\ErrorLog as EL;
use function ST\Lms\column_post_author as postAuthor;
use const ST\Lms\STLMS_COURSE_CPT;
use const ST\Lms\STLMS_COURSE_CATEGORY_TAX;
use const ST\Lms\STLMS_COURSE_TAXONOMY_TAG;
use const ST\Lms\META_KEY_COURSE_INFORMATION;
use const ST\Lms\META_KEY_COURSE_ASSESSMENT;
use const ST\Lms\META_KEY_COURSE_MATERIAL;
use const ST\Lms\META_KEY_COURSE_CURRICULUM;
use const ST\Lms\META_KEY_COURSE_SIGNATURE;

/**
 * Register metaboxes for course.
 */
class Course extends \ST\Lms\Collections\PostTypes {

	/**
	 * Curriculums list.
	 *
	 * @var array $curriculums Curriculums list.
	 */
	private $curriculums = array();

	/**
	 * Meta key prefix.
	 *
	 * @var string $meta_key_prefix
	 */
	public $meta_key_prefix = \ST\Lms\META_KEY_COURSE_PREFIX;

	/**
	 * Class construct.
	 */
	public function __construct() {
		$this->set_metaboxes( $this->meta_boxes_list() );

		// Hooks.
		add_action( 'save_post_' . STLMS_COURSE_CPT, array( $this, 'save_metadata' ) );
		add_filter( 'manage_edit-' . STLMS_COURSE_CPT . '_sortable_columns', array( $this, 'sortable_columns' ) );
		add_filter( 'manage_' . STLMS_COURSE_CPT . '_posts_columns', array( $this, 'add_new_table_columns' ) );
		add_action( 'manage_' . STLMS_COURSE_CPT . '_posts_custom_column', array( $this, 'manage_custom_column' ), 10, 2 );
		add_action( 'all_admin_notices', array( $this, 'add_header_tab' ) );
		add_action( 'wp_ajax_stlms_create_course_curriculum', array( $this, 'create_course_curriculum' ) );
		add_action( 'admin_action_load_select_items', array( $this, 'load_select_items' ) );
	}

	/**
	 * Meta boxes list.
	 *
	 * @return array
	 */
	private function meta_boxes_list() {
		$list = apply_filters(
			'stlms/course/meta_boxes',
			array(
				array(
					'id'       => 'curriculum',
					'title'    => __( 'Curriculum', 'skilltriks' ),
					'callback' => array( $this, 'render_curriculum' ),
				),
				array(
					'id'       => 'course-settings',
					'title'    => __( 'Course Settings', 'skilltriks' ),
					'callback' => array( $this, 'render_course_settings' ),
				),
			)
		);
		return $list;
	}

	/**
	 * Render curriculum metabox.
	 */
	public function render_curriculum() {
		global $post;
		$post_id = isset( $post->ID ) ? $post->ID : 0;
		// Get curriculum items.
		$this->curriculums = get_post_meta( $post_id, META_KEY_COURSE_CURRICULUM, true );
		$this->curriculums = ! empty( $this->curriculums ) ? $this->curriculums : array(
			array(
				'section_name' => '',
				'section_desc' => '',
				'items_type'   => array(),
				'items'        => array(),
			),
		);
		require_once STLMS_TEMPLATEPATH . '/admin/course/curriculum.php';
	}

	/**
	 * Render course settings metabox.
	 */
	public function render_course_settings() {
		global $post, $user_ID;
		$post_id          = isset( $post->ID ) ? $post->ID : 0;
		$post_type_object = get_post_type_object( $post->post_type );
		// Get max upload size.
		$max_upload_size = wp_max_upload_size();
		if ( ! $max_upload_size ) {
			$max_upload_size = 0;
		}

		// Get course information.
		$information         = get_post_meta( $post_id, META_KEY_COURSE_INFORMATION, true );
		$information         = ! empty( $information ) ? array_filter( $information ) : array();
		$default_information = array(
			'requirement'     => array( '' ),
			'what_you_learn'  => array( '' ),
			'skills_you_gain' => array( '' ),
			'course_includes' => array( '' ),
			'faq_question'    => array( '' ),
			'faq_answer'      => array( '' ),
		);
		$information         = wp_parse_args( $information, $default_information );
		// Get course assessment.
		$assessment         = get_post_meta( $post_id, META_KEY_COURSE_ASSESSMENT, true );
		$default_assessment = array(
			'evaluation'    => 0,
			'passing_grade' => '',
		);
		$assessment         = wp_parse_args( $assessment, $default_assessment );
		// Get course author signature.
		$signature         = get_post_meta( $post_id, META_KEY_COURSE_SIGNATURE, true );
		$signature         = ! empty( $signature ) ? array_filter( $signature ) : array();
		$default_signature = array(
			'text'     => '',
			'image_id' => 0,
		);
		$signature         = wp_parse_args( $signature, $default_signature );
		// Get course materials.
		$materials   = get_post_meta( $post_id, META_KEY_COURSE_MATERIAL, true );
		$materials   = ! empty( $materials ) ? $materials : array();
		$curriculums = \ST\Lms\get_curriculums( $this->curriculums, \ST\Lms\STLMS_QUIZ_CPT );
		$last_quiz   = end( $curriculums );
		require_once STLMS_TEMPLATEPATH . '/admin/course/course-settings.php';
	}

	/**
	 * Save post meta.
	 */
	public function save_metadata() {
		global $post;
		$post_id   = isset( $post->ID ) ? $post->ID : 0;
		$post_data = array(
			'material' => array(),
		);
		if ( ! isset( $_POST['stlms_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['stlms_nonce'] ) ), STLMS_BASEFILE ) ) {
			EL::add( 'Failed nonce verification', 'error', __FILE__, __LINE__ );
			return;
		}
		do_action( 'stlms_save_course_before', $post_id, $post_data );

		/**
		 * Sanitize all string in array.
		 */
		if ( ! empty( $_POST[ $this->meta_key_prefix ]['information']['requirement'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$post_data['information']['requirement'] = array_map( 'sanitize_text_field', $_POST[ $this->meta_key_prefix ]['information']['requirement'] );
			$post_data['information']['requirement'] = array_filter( $post_data['information']['requirement'] );
		}
		if ( ! empty( $_POST[ $this->meta_key_prefix ]['information']['what_you_learn'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$post_data['information']['what_you_learn'] = array_map( 'sanitize_text_field', $_POST[ $this->meta_key_prefix ]['information']['what_you_learn'] );
			$post_data['information']['what_you_learn'] = array_filter( $post_data['information']['what_you_learn'] );
		}
		if ( ! empty( $_POST[ $this->meta_key_prefix ]['information']['skills_you_gain'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$post_data['information']['skills_you_gain'] = array_map( 'sanitize_text_field', $_POST[ $this->meta_key_prefix ]['information']['skills_you_gain'] );
			$post_data['information']['skills_you_gain'] = array_filter( $post_data['information']['skills_you_gain'] );
		}
		if ( ! empty( $_POST[ $this->meta_key_prefix ]['information']['course_includes'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$post_data['information']['course_includes'] = array_map( 'sanitize_text_field', $_POST[ $this->meta_key_prefix ]['information']['course_includes'] );
			$post_data['information']['course_includes'] = array_filter( $post_data['information']['course_includes'] );
		}
		if ( ! empty( $_POST[ $this->meta_key_prefix ]['information']['faq_question'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$post_data['information']['faq_question'] = array_map( 'sanitize_text_field', $_POST[ $this->meta_key_prefix ]['information']['faq_question'] );
			$post_data['information']['faq_question'] = array_filter( $post_data['information']['faq_question'] );
		}
		if ( ! empty( $_POST[ $this->meta_key_prefix ]['information']['faq_answer'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$post_data['information']['faq_answer'] = array_map( 'sanitize_text_field', $_POST[ $this->meta_key_prefix ]['information']['faq_answer'] );
			$post_data['information']['faq_answer'] = array_filter( $post_data['information']['faq_answer'] );
		}
		if ( isset( $_POST[ $this->meta_key_prefix ]['assessment']['evaluation'] ) ) {
			$post_data['assessment']['evaluation'] = (int) $_POST[ $this->meta_key_prefix ]['assessment']['evaluation'];
		}
		if ( isset( $_POST[ $this->meta_key_prefix ]['assessment']['passing_grade'] ) ) {
			$post_data['assessment']['passing_grade'] = (int) $_POST[ $this->meta_key_prefix ]['assessment']['passing_grade'];
		}
		if ( isset( $_POST[ $this->meta_key_prefix ]['material'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$materials             = map_deep( $_POST[ $this->meta_key_prefix ]['material'], 'sanitize_text_field' );
			$materials             = array_map( 'array_filter', $materials );
			$post_data['material'] = $materials;
		}

		if ( isset( $_POST[ $this->meta_key_prefix ]['curriculum'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$curriculum              = map_deep( $_POST[ $this->meta_key_prefix ]['curriculum'], 'sanitize_text_field' );
			$curriculum              = array_map(
				function ( $c ) {
					if ( isset( $c['section_name'] ) ) {
						$c['section_name'] = sanitize_text_field( $c['section_name'] );
					}
					if ( isset( $c['section_desc'] ) ) {
						$c['section_desc'] = sanitize_textarea_field( $c['section_desc'] );
					}
					if ( isset( $c['items'] ) ) {
						$c['items'] = array_filter( array_map( 'intval', $c['items'] ) );
					}
					return $c;
				},
				$curriculum
			);
			$post_data['curriculum'] = $curriculum;
		}

		if ( isset( $_POST[ $this->meta_key_prefix ]['signature']['image_id'] ) ) {
			$post_data['signature']['image_id'] = (int) $_POST[ $this->meta_key_prefix ]['signature']['image_id'];
		}
		if ( isset( $_POST[ $this->meta_key_prefix ]['signature']['text'] ) ) {
			$post_data['signature']['text'] = sanitize_text_field( wp_unslash( $_POST[ $this->meta_key_prefix ]['signature']['text'] ) );
		}
		$post_data['signature']['certificate'] = 0;
		if ( isset( $_POST[ $this->meta_key_prefix ]['signature']['certificate'] ) ) {
			$post_data['signature']['certificate'] = 1;
		}

		$post_data = apply_filters( 'stlms_course_post_data', $post_data, $post_id );

		do_action( 'stlms_save_course_meta_before', $post_id, $post_data );

		foreach ( $post_data as $key => $data ) {
			$key = $this->meta_key_prefix . '_' . $key;
			if ( empty( $data ) ) {
				delete_post_meta( $post_id, $key );
				continue;
			}
			update_post_meta( $post_id, $key, $data );
		}
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
		EL::add( sprintf( 'Course updated: %s, Post ID: %d', print_r( $post_data, true ), $post_id ), 'info', __FILE__, __LINE__ );

		do_action( 'stlms_save_course_after', $post_id, $post_data );
	}

	/**
	 * Sortable columns list.
	 *
	 * @param array $columns Sortable columns.
	 *
	 * @return array
	 */
	public function sortable_columns( $columns ) {
		$columns['post_author'] = 'author';
		return $columns;
	}

	/**
	 * Add new table columns.
	 *
	 * @param array $columns Columns list.
	 * @return array
	 */
	public function add_new_table_columns( $columns ) {
		$date = $columns['date'];
		unset( $columns['date'] );

		$category_key = 'taxonomy-' . STLMS_COURSE_CATEGORY_TAX;
		$tag_key      = 'taxonomy-' . STLMS_COURSE_TAXONOMY_TAG;
		$category     = $columns[ $category_key ];
		$checkbox     = $columns['cb'];
		unset( $columns[ $category_key ] );
		unset( $columns[ $tag_key ] );
		unset( $columns['author'] );
		unset( $columns['comments'] );
		unset( $columns['cb'] );
		$columns['post_author']   = __( 'Author', 'skilltriks' );
		$columns['content']       = __( 'Content', 'skilltriks' );
		$columns['category_list'] = __( 'Categories', 'skilltriks' );
		$columns['comments_list'] = __( 'Comments', 'skilltriks' );
		$columns['date']          = $date;

		$columns = array_merge(
			array(
				'cb'        => $checkbox,
				'thumbnail' => __( 'Thumbnail', 'skilltriks' ),
			),
			$columns
		);
		return $columns;
	}

	/**
	 * Manage custom column.
	 *
	 * @param string $column Column name.
	 * @param int    $post_id Post ID.
	 *
	 * @return void
	 */
	public function manage_custom_column( $column, $post_id ) {
		switch ( $column ) {
			case 'thumbnail':
				echo '<div class="column-thumb-img">';
				if ( has_post_thumbnail( $post_id ) ) {
					// phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
					echo '<img src="' . esc_url( get_the_post_thumbnail_url( $post_id ) ) . '" class="course-img" width="80" height="80">';
				}
				echo '</div>';
				break;
			case 'post_author':
				echo wp_kses_post( (string) postAuthor( $post_id ) );
				break;
			case 'content':
				$curriculums = get_post_meta( $post_id, \ST\Lms\META_KEY_COURSE_CURRICULUM, true );
				if ( ! empty( $curriculums ) ) {
					$total_lessons = count( \ST\Lms\get_curriculums( $curriculums, \ST\Lms\STLMS_LESSON_CPT ) );
					$total_quizzes = count( \ST\Lms\get_curriculums( $curriculums, \ST\Lms\STLMS_QUIZ_CPT ) );

					$content = sprintf(
						// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
						_n( '%s Lesson', '%s Lessons', (int) $total_lessons, 'skilltriks' ),
						number_format_i18n( $total_lessons )
					);
					$content .= sprintf(
						// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
						_n( ' | %s Quiz', ' | %s Quizzes', (int) $total_quizzes, 'skilltriks' ),
						number_format_i18n( $total_quizzes )
					);
					echo esc_html( $content );
				} else {
					echo esc_html__( 'No Content', 'skilltriks' );
				}
				break;
			case 'category_list':
				$categories = get_the_terms( $post_id, STLMS_COURSE_CATEGORY_TAX );
				$categories = is_array( $categories ) ? $categories : array();
				$categories = array_map(
					function ( $category ) {
						$filter_url = add_query_arg(
							array(
								'post_type'               => STLMS_COURSE_CPT,
								STLMS_COURSE_CATEGORY_TAX => $category->slug,
							),
							admin_url( 'edit.php' )
						);
						return '<a href="' . esc_url( $filter_url ) . '">' . $category->name . '</a>';
					},
					$categories
				);
				if ( ! empty( $categories ) ) {
					echo wp_kses(
						implode( ', ', array_filter( $categories ) ),
						array(
							'a' => array(
								'href'           => array(),
								'title'          => array(),
								'class'          => array(),
								'target'         => array(),
								'data-course_id' => array(),
							),
						)
					);
				} else {
					echo '—';
				}
				break;
			case 'comments_list':
				echo '<a href="' . esc_url( add_query_arg( 'p', $post_id, admin_url( 'edit-comments.php' ) ) ) . '">' . esc_html__( 'View', 'skilltriks' ) . '</a>';
				break;
			default:
				break;
		}
	}

	/**
	 * Add header tab.
	 */
	public function add_header_tab() {
		global $current_screen;
		if ( ! $current_screen ) {
			return;
		}
		$id = $current_screen->id;
		if ( function_exists( 'str_contains' ) && str_contains( $id, 'edit-stlms_course' ) && ! str_contains( $id, 'edit-stlms_course_department' ) ) {
			$id = str_replace( 'edit-', '', $id );
			?>
			<div class="stlms-course-wrap">
				<nav class="nav-tab-wrapper">
					<a
						href="<?php echo esc_url( add_query_arg( 'post_type', STLMS_COURSE_CPT, admin_url( 'edit.php' ) ) ); ?>"
						class="nav-tab <?php echo STLMS_COURSE_CPT === $id ? esc_attr( 'active' ) : ''; ?>"
					><?php esc_html_e( 'Courses', 'skilltriks' ); ?></a>
					<?php
					if ( current_user_can( 'manage_options' ) ||
						( current_user_can( 'edit_published_courses' ) && current_user_can( 'edit_others_courses' ) ) //phpcs:ignore WordPress.WP.Capabilities.Unknown
					) :
						?>
					<a
						href="<?php echo esc_url( add_query_arg( 'taxonomy', STLMS_COURSE_CATEGORY_TAX, admin_url( 'edit-tags.php' ) ) ); ?>"
						class="nav-tab <?php echo STLMS_COURSE_CATEGORY_TAX === $id ? esc_attr( 'active' ) : ''; ?>"
					><?php esc_html_e( 'Categories', 'skilltriks' ); ?></a>
					<a
						href="<?php echo esc_url( add_query_arg( 'taxonomy', STLMS_COURSE_TAXONOMY_TAG, admin_url( 'edit-tags.php' ) ) ); ?>"
						class="nav-tab <?php echo STLMS_COURSE_TAXONOMY_TAG === $id ? esc_attr( 'active' ) : ''; ?>"
					><?php esc_html_e( 'Tags', 'skilltriks' ); ?></a>
					<?php endif; ?>
				</nav>
			</div>
			<?php
		}
	}

	/**
	 * Create course curriculum.
	 */
	public function create_course_curriculum() {
		check_ajax_referer( STLMS_BASEFILE, '_nonce' );
		$title = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
		$type  = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';

		$post_type = '';
		if ( 'lesson' === $type ) {
			$post_type = \ST\Lms\STLMS_LESSON_CPT;
		}
		if ( 'quiz' === $type ) {
			$post_type = \ST\Lms\STLMS_QUIZ_CPT;
		}
		if ( empty( $post_type ) ) {
			EL::add( 'Invalid type selected', 'error', __FILE__, __LINE__ );
			wp_send_json(
				array(
					'post_id' => 0,
					'message' => '',
				)
			);
		}
		$post_id = wp_insert_post(
			array(
				'post_title'  => $title,
				'post_status' => 'publish',
				'post_type'   => $post_type,
			)
		);
		if ( ! is_int( $post_id ) ) {
			EL::add( $post_id->get_error_message(), 'error', __FILE__, __LINE__ );
			wp_send_json(
				array(
					'post_id' => 0,
					'message' => '',
				)
			);
		}
		EL::add( sprintf( 'New curriculum item created, ID %d', $post_id ), 'error', __FILE__, __LINE__ );
		wp_send_json(
			array(
				'post_id'   => $post_id,
				'edit_link' => get_edit_post_link( $post_id ),
				'view_link' => get_the_permalink( $post_id ),
				'message'   => sprintf( __( '%s added', 'skilltriks' ), ucfirst( $type ) ), // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
			)
		);
	}

	/**
	 * Load select items.
	 */
	public function load_select_items() {
		$nonce          = isset( $_REQUEST['_nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_nonce'] ) ) : '';
		$type           = isset( $_REQUEST['type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['type'] ) ) : \ST\Lms\STLMS_LESSON_CPT;
		$fetch_request  = isset( $_REQUEST['fetch_items'] ) ? (int) $_REQUEST['fetch_items'] : 0;
		$question_id    = isset( $_REQUEST['post_id'] ) ? (int) $_REQUEST['post_id'] : 0;
		$existing_items = isset( $_REQUEST['existing_items'] ) ? array_map( 'intval', $_REQUEST['existing_items'] ) : array();
		if ( ! wp_verify_nonce( $nonce, STLMS_BASEFILE ) ) {
			EL::add( 'Failed nonce verification', 'error', __FILE__, __LINE__ );
		}
		require_once STLMS_TEMPLATEPATH . '/admin/course/modal-popup.php';
		exit;
	}
}
