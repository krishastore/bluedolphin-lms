<?php
/**
 * The file that register metabox for lesson.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BlueDolphin\Lms
 *
 * phpcs:disable WordPress.NamingConventions.ValidHookName.UseUnderscores
 */

namespace BlueDolphin\Lms\Admin\MetaBoxes;

use BlueDolphin\Lms\ErrorLog as EL;
use function BlueDolphin\Lms\column_post_author as postAuthor;
use const BlueDolphin\Lms\BDLMS_LESSON_CPT;
use const BlueDolphin\Lms\BDLMS_LESSON_TAXONOMY_TAG;
use const BlueDolphin\Lms\META_KEY_LESSON_SETTINGS;
use const BlueDolphin\Lms\META_KEY_LESSON_MEDIA;
use const BlueDolphin\Lms\META_KEY_LESSON_MATERIAL;
use const BlueDolphin\Lms\META_KEY_LESSON_COURSE_IDS;

/**
 * Register metaboxes for lesson.
 */
class Lesson extends \BlueDolphin\Lms\Collections\PostTypes {

	/**
	 * Meta key prefix.
	 *
	 * @var string $meta_key_prefix
	 */
	public $meta_key_prefix = \BlueDolphin\Lms\META_KEY_LESSON_PREFIX;

	/**
	 * Class construct.
	 */
	public function __construct() {
		$this->set_metaboxes( $this->meta_boxes_list() );

		// Hooks.
		add_action( 'save_post_' . BDLMS_LESSON_CPT, array( $this, 'save_metadata' ) );
		add_filter( 'manage_edit-' . BDLMS_LESSON_CPT . '_sortable_columns', array( $this, 'sortable_columns' ) );
		add_filter( 'manage_' . BDLMS_LESSON_CPT . '_posts_columns', array( $this, 'add_new_table_columns' ) );
		add_action( 'manage_' . BDLMS_LESSON_CPT . '_posts_custom_column', array( $this, 'manage_custom_column' ), 10, 2 );
		add_action( 'admin_action_load_course_list', array( $this, 'load_course_list' ) );
		add_action( 'quick_edit_custom_box', array( $this, 'quick_edit_custom_box' ), 10, 2 );
		add_action( 'bulk_edit_custom_box', array( $this, 'bulk_edit_custom_box' ), 10, 2 );
		add_action( 'bulk_edit_posts', array( $this, 'bulk_edit_posts' ), 10, 2 );
		add_action( 'wp_ajax_bdlms_assign_to_course', array( $this, 'assign_to_course' ) );
	}

	/**
	 * Meta boxes list.
	 *
	 * @return array
	 */
	private function meta_boxes_list() {
		$list = apply_filters(
			'bluedolphin/lessons/meta_boxes',
			array(
				array(
					'id'       => 'add-media',
					'title'    => __( 'Add Media', 'bluedolphin-lms' ),
					'callback' => array( $this, 'render_add_media' ),
				),
				array(
					'id'       => 'lessons-settings',
					'title'    => __( 'Lesson Settings', 'bluedolphin-lms' ),
					'callback' => array( $this, 'render_lesson_settings' ),
				),
				array(
					'id'       => 'assign-to-course',
					'title'    => __( 'Assign to course', 'bluedolphin-lms' ),
					'callback' => array( $this, 'render_assign_to_course' ),
					'screen'   => null,
					'context'  => 'side',
				),
			)
		);
		return $list;
	}

	/**
	 * Render add media metabox.
	 */
	public function render_add_media() {
		global $post;
		$post_id = isset( $post->ID ) ? $post->ID : 0;
		$media   = get_post_meta( $post_id, META_KEY_LESSON_MEDIA, true );
		$media   = wp_parse_args(
			$media,
			array(
				'media_type'      => 'video',
				'video_id'        => 0,
				'embed_video_url' => '',
				'text'            => '',
				'file_id'         => 0,
			)
		);
		require_once BDLMS_TEMPLATEPATH . '/admin/lesson/add-media.php';
	}

	/**
	 * Render lesson settings metabox.
	 */
	public function render_lesson_settings() {
		global $post;
		$post_id   = isset( $post->ID ) ? $post->ID : 0;
		$settings  = get_post_meta( $post_id, META_KEY_LESSON_SETTINGS, true );
		$materials = get_post_meta( $post_id, META_KEY_LESSON_MATERIAL, true );
		$settings  = wp_parse_args(
			$settings,
			array(
				'duration'      => 0,
				'duration_type' => '',
			)
		);

		$materials = ! empty( $materials ) ? $materials : array();
		// Get max upload size.
		$max_upload_size = wp_max_upload_size();
		if ( ! $max_upload_size ) {
			$max_upload_size = 0;
		}
		require_once BDLMS_TEMPLATEPATH . '/admin/lesson/lesson-settings.php';
	}

	/**
	 * Render assign to course metabox.
	 */
	public function render_assign_to_course() {
		global $post;
		?>
			<div class="bdlms-assign-quiz">
				<a href="javascript:;" class="button button-primary button-large" data-modal="assign_lesson"><?php esc_html_e( 'Click to assign course', 'bluedolphin-lms' ); ?></a>
			</div>
			<div class="bdlms-snackbar-notice"><p></p></div>
		<?php
		require_once BDLMS_TEMPLATEPATH . '/admin/lesson/modal-popup.php';
	}

	/**
	 * Save post meta.
	 */
	public function save_metadata() {
		global $post;
		$post_id   = isset( $post->ID ) ? $post->ID : 0;
		$post_data = array(
			'media'    => array(),
			'settings' => array(),
			'material' => array(),
		);

		if ( ! isset( $_POST['bdlms_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bdlms_nonce'] ) ), BDLMS_BASEFILE ) ) {
			EL::add( 'Failed nonce verification', 'error', __FILE__, __LINE__ );
			return;
		}
		do_action( 'bdlms_save_lesson_before', $post_id, $post_data, $_POST );
		// Quick edit action.
		if ( isset( $_POST['action'] ) && 'inline-save' === $_POST['action'] ) {
			$post_id   = isset( $_POST['post_ID'] ) ? (int) $_POST['post_ID'] : $post_id;
			$post_data = array(
				'settings' => get_post_meta( $post_id, META_KEY_LESSON_SETTINGS, true ),
			);
			if ( ! empty( $_POST['courses'] ) ) {
				$courses = isset( $_POST['courses'] ) ? map_deep( $_POST['courses'], 'intval' ) : array();
				update_post_meta( $post_id, META_KEY_LESSON_COURSE_IDS, array_unique( $courses ) );
			}
		}

		if ( isset( $_POST[ $this->meta_key_prefix ]['media']['media_type'] ) ) {
			$post_data['media']['media_type'] = sanitize_text_field( wp_unslash( $_POST[ $this->meta_key_prefix ]['media']['media_type'] ) );
		}
		if ( isset( $_POST[ $this->meta_key_prefix ]['media']['video_id'] ) ) {
			$post_data['media']['video_id'] = (int) $_POST[ $this->meta_key_prefix ]['media']['video_id'];
		}
		if ( isset( $_POST[ $this->meta_key_prefix ]['media']['file_id'] ) ) {
			$post_data['media']['file_id'] = (int) $_POST[ $this->meta_key_prefix ]['media']['file_id'];
		}
		if ( isset( $_POST[ $this->meta_key_prefix ]['media']['embed_video_url'] ) ) {
			$post_data['media']['embed_video_url'] = sanitize_text_field( wp_unslash( $_POST[ $this->meta_key_prefix ]['media']['embed_video_url'] ) );
		}
		if ( isset( $_POST[ $this->meta_key_prefix ]['media']['text'] ) ) {
			$post_data['media']['text'] = wp_kses_post( wp_unslash( $_POST[ $this->meta_key_prefix ]['media']['text'] ) );
		}

		if ( isset( $_POST[ $this->meta_key_prefix ]['settings']['duration'] ) ) {
			$post_data['settings']['duration'] = (int) $_POST[ $this->meta_key_prefix ]['settings']['duration'];
		}
		if ( isset( $_POST[ $this->meta_key_prefix ]['settings']['duration_type'] ) ) {
			$post_data['settings']['duration_type'] = sanitize_textarea_field( wp_unslash( $_POST[ $this->meta_key_prefix ]['settings']['duration_type'] ) );
		}
		if ( isset( $_POST[ $this->meta_key_prefix ]['material'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$materials             = map_deep( $_POST[ $this->meta_key_prefix ]['material'], 'sanitize_text_field' );
			$materials             = array_map( 'array_filter', $materials );
			$post_data['material'] = $materials;
		}
		$post_data = apply_filters( 'bdlms_lesson_post_data', $post_data, $_POST, $post_id );

		foreach ( $post_data as $key => $data ) {
			$key = $this->meta_key_prefix . '_' . $key;
			if ( empty( $data ) ) {
				delete_post_meta( $post_id, $key );
				continue;
			}
			update_post_meta( $post_id, $key, $data );
		}
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
		EL::add( sprintf( 'Lesson updated: %s, Post ID: %d', print_r( $post_data, true ), $post_id ), 'info', __FILE__, __LINE__ );

		do_action( 'bdlms_save_lesson_after', $post_id, $post_data, $_POST );
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

		$topic_key = 'taxonomy-' . BDLMS_LESSON_TAXONOMY_TAG;
		$topic     = $columns[ $topic_key ];
		unset( $columns[ $topic_key ] );
		unset( $columns['author'] );
		$columns['post_author'] = __( 'Author', 'bluedolphin-lms' );
		$columns['course']      = __( 'Course', 'bluedolphin-lms' );
		$columns['lesson_type'] = __( 'Lesson Type', 'bluedolphin-lms' );
		$columns['duration']    = __( 'Duration', 'bluedolphin-lms' );
		$columns['date']        = $date;
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
		$media    = get_post_meta( $post_id, META_KEY_LESSON_MEDIA, true );
		$settings = get_post_meta( $post_id, META_KEY_LESSON_SETTINGS, true );
		switch ( $column ) {
			case 'post_author':
				echo wp_kses_post( postAuthor( $post_id ) );
				break;
			case 'course':
				$connected = get_posts(
					array(
						'post_type'      => \BlueDolphin\Lms\BDLMS_COURSE_CPT,
						'posts_per_page' => -1,
						'fields'         => 'ids',
						'meta_key'       => \BlueDolphin\Lms\META_KEY_COURSE_CURRICULUM, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
						'meta_value'     => array( 'items' => $post_id ), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
						'meta_compare'   => 'REGEXP',
					)
				);
				if ( empty( $connected ) ) {
					esc_html_e( 'Not Assigned Yet', 'bluedolphin-lms' );
					break;
				}
				$connected = array_map(
					function ( $q ) {
						$url   = get_edit_post_link( $q );
						$title = get_the_title( $q );
						if ( empty( $title ) ) {
							return '';
						}
						return '<a href="' . esc_url( $url ) . '" data-course_id="' . $q . '" target="_blank">' . $title . '</a>';
					},
					$connected
				);
				echo wp_kses(
					implode( ', ', array_filter( $connected ) ),
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
				break;
			case 'lesson_type':
				if ( isset( $media['media_type'] ) && 'video' === $media['media_type'] ) {
					esc_html_e( 'Video', 'bluedolphin-lms' );
				} elseif ( isset( $media['media_type'] ) && 'text' === $media['media_type'] ) {
					esc_html_e( 'Text', 'bluedolphin-lms' );
				} else {
					echo '—';
				}
				break;
			case 'duration':
				$duration      = isset( $settings['duration'] ) ? (int) $settings['duration'] : '';
				$duration_type = isset( $settings['duration_type'] ) ? $settings['duration_type'] : '';
				if ( empty( $duration ) ) {
					echo '—';
					break;
				}
				echo '<span class="hidden duration-type">' . esc_html( $duration_type ) . '</span>';
				$duration_type .= $duration > 1 ? 's' : '';
				printf( '<span class="duration-val">%d %s</span>', (int) $duration, esc_html( ucfirst( $duration_type ) ) );
				break;
			default:
				break;
		}
	}

	/**
	 * Load course list.
	 */
	public function load_course_list() {
		$nonce         = isset( $_REQUEST['_nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_nonce'] ) ) : '';
		$fetch_request = isset( $_REQUEST['fetch_courses'] ) ? (int) $_REQUEST['fetch_courses'] : 0;
		$lesson_id     = isset( $_REQUEST['post_id'] ) ? (int) $_REQUEST['post_id'] : 0;
		if ( ! wp_verify_nonce( $nonce, BDLMS_BASEFILE ) ) {
			EL::add( 'Failed nonce verification', 'error', __FILE__, __LINE__ );
			exit;
		}
		require_once BDLMS_TEMPLATEPATH . '/admin/lesson/modal-popup.php';
		exit;
	}

	/**
	 * Assign to course.
	 */
	public function assign_to_course() {
		check_ajax_referer( BDLMS_BASEFILE, 'bdlms_nonce' );
		$post_id = isset( $_POST['post_id'] ) ? (int) $_POST['post_id'] : 0;
		$courses = isset( $_POST['selected'] ) ? map_deep( $_POST['selected'], 'intval' ) : array();

		foreach ( $courses as $course ) {
			$curriculums = get_post_meta( $course, \BlueDolphin\Lms\META_KEY_COURSE_CURRICULUM, true );
			$curriculums = ! empty( $curriculums ) ? $curriculums : array(
				array(
					'section_name' => '',
					'section_desc' => '',
					'items'        => array(),
				),
			);
			$last_index  = ! empty( $curriculums ) ? array_key_last( $curriculums ) : 0;
			if ( isset( $curriculums[ $last_index ]['items'] ) && ! in_array( $post_id, $curriculums[ $last_index ]['items'], true ) ) {
				$curriculums[ $last_index ]['items'][] = $post_id;
			}
			update_post_meta( $course, \BlueDolphin\Lms\META_KEY_COURSE_CURRICULUM, $curriculums );
		}
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
		EL::add( sprintf( 'Assigned to course: %s, Post ID: %d', print_r( array_unique( $courses ), true ), $post_id ), 'info', __FILE__, __LINE__ );

		wp_send_json(
			array(
				'status'  => true,
				'message' => __( 'Saved.', 'bluedolphin-lms' ),
			)
		);
		exit;
	}

	/**
	 * Quick edit custom box.
	 *
	 * @param string $column_name Column name.
	 * @param string $post_type Post Type.
	 */
	public function quick_edit_custom_box( $column_name, $post_type ) {
		if ( BDLMS_LESSON_CPT !== $post_type || 'duration' !== $column_name ) {
			return;
		}
		?>
		<fieldset class="inline-edit-col-right inline-edit-lesson">
			<?php wp_nonce_field( BDLMS_BASEFILE, 'bdlms_nonce', false ); ?>
			<div class="inline-edit-col inline-edit-duration">
				<span class="title"><?php esc_html_e( 'Duration', 'bluedolphin-lms' ); ?></span>
				<div class="inline-edit-lesson">
					<div class="inline-edit-lesson-item">
						<label>
							<input type="number" step="1" min="0" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[settings][duration]">
							<select name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[settings][duration_type]">
								<option value="minute"><?php esc_html_e( 'Minute(s)', 'bluedolphin-lms' ); ?></option>
								<option value="hour"><?php esc_html_e( 'Hour(s)', 'bluedolphin-lms' ); ?></option>
								<option value="day"><?php esc_html_e( 'Day(s)', 'bluedolphin-lms' ); ?></option>
								<option value="week"><?php esc_html_e( 'Week(s)', 'bluedolphin-lms' ); ?></option>
							</select>
						</label>
					</div>
				</div>
			</div>
			<div class="inline-edit-col inline-edit-courses">
				<span class="title"><?php esc_html_e( 'Courses', 'bluedolphin-lms' ); ?></span>
				<div class="inline-edit-lesson">
					<div class="inline-edit-lesson-item">
						<label>
							<?php
								$courses = get_posts(
									array(
										'post_type'      => \BlueDolphin\Lms\BDLMS_COURSE_CPT,
										'posts_per_page' => -1,
										'fields'         => 'ids',
									)
								);
							if ( ! empty( $courses ) ) :
								?>
							<ul class="cat-checklist <?php echo esc_attr( \BlueDolphin\Lms\BDLMS_COURSE_CPT ); ?>-checklist">
								<?php foreach ( $courses as $course ) : ?>
									<li class="popular-category">
										<label class="selectit">
											<input value="<?php echo (int) $course; ?>" type="checkbox" name="courses[]"> <?php echo esc_html( get_the_title( $course ) ); ?>
										</label>
									</li>
								<?php endforeach; ?>
							</ul>
							<?php else : ?>
								<p><?php esc_html_e( 'No course found.', 'bluedolphin-lms' ); ?></p>
							<?php endif; ?>
						</label>
					</div>
				</div>
			</div>
		</fieldset>
		<?php do_action( 'bdlms_inline_lessons_edit_field', $column_name, $post_type, $this ); ?>
		<?php
	}

	/**
	 * Bluk edit custom box.
	 *
	 * @param string $column_name Column name.
	 * @param string $post_type Post Type.
	 */
	public function bulk_edit_custom_box( $column_name, $post_type ) {
		if ( BDLMS_LESSON_CPT !== $post_type || 'duration' !== $column_name ) {
			return;
		}
		?>
		<fieldset class="inline-edit-col-right inline-edit-lesson">
			<div class="inline-edit-col inline-edit-courses">
				<span class="title"><?php esc_html_e( 'Courses', 'bluedolphin-lms' ); ?></span>
				<div class="inline-edit-lesson">
					<div class="inline-edit-lesson-item">
						<label>
							<?php
								$courses = get_posts(
									array(
										'post_type'      => \BlueDolphin\Lms\BDLMS_COURSE_CPT,
										'posts_per_page' => -1,
										'fields'         => 'ids',
									)
								);
							if ( ! empty( $courses ) ) :
								?>
							<ul class="cat-checklist <?php echo esc_attr( \BlueDolphin\Lms\BDLMS_COURSE_CPT ); ?>-checklist">
								<?php foreach ( $courses as $course ) : ?>
									<li class="popular-category">
										<label class="selectit">
											<input value="<?php echo (int) $course; ?>" type="checkbox" name="bulk_courses[]"> <?php echo esc_html( get_the_title( $course ) ); ?>
										</label>
									</li>
								<?php endforeach; ?>
							</ul>
							<?php else : ?>
								<p><?php esc_html_e( 'No course found.', 'bluedolphin-lms' ); ?></p>
							<?php endif; ?>
						</label>
					</div>
				</div>
			</div>
		</fieldset>
		<?php
	}

	/**
	 * Save bulk edit data.
	 *
	 * @since 1.0.0
	 *
	 * @param int[] $updated   An array of updated post IDs.
	 * @param array $post_data Associative array containing the post data.
	 */
	public function bulk_edit_posts( $updated, $post_data ) {
		global $current_screen;
		if ( ! isset( $current_screen->post_type ) || BDLMS_LESSON_CPT !== $current_screen->post_type ) {
			return;
		}
		foreach ( $updated as $lesson_id ) {
			if ( ! empty( $post_data['bulk_courses'] ) ) {
				$courses      = get_post_meta( $lesson_id, META_KEY_LESSON_COURSE_IDS, true );
				$courses      = ! empty( $courses ) ? $courses : array();
				$bulk_courses = isset( $post_data['bulk_courses'] ) ? map_deep( $post_data['bulk_courses'], 'intval' ) : array();
				$courses      = array_merge( $courses, $bulk_courses );
				update_post_meta( $lesson_id, META_KEY_LESSON_COURSE_IDS, array_unique( $courses ) );
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
				EL::add( sprintf( 'Bulk course assigned: %s, Post ID: %d', print_r( array_unique( $courses ), true ), $lesson_id ), 'info', __FILE__, __LINE__ );
			}
		}
	}
}
