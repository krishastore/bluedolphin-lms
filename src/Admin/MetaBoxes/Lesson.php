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
			return;
		}
		do_action( 'bdlms_save_lesson_before', $post_id, $post_data, $_POST );

		if ( isset( $_POST[ $this->meta_key_prefix ]['course_id'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$course_ids              = map_deep( $_POST[ $this->meta_key_prefix ]['course_id'], 'intval' );
			$post_data['course_ids'] = $course_ids;
		}

		if ( isset( $_POST[ $this->meta_key_prefix ]['media']['media_type'] ) ) {
			$post_data['media']['media_type'] = sanitize_text_field( wp_unslash( $_POST[ $this->meta_key_prefix ]['media']['media_type'] ) );
		}
		if ( isset( $_POST[ $this->meta_key_prefix ]['media']['video_id'] ) ) {
			$post_data['media']['video_id'] = (int) $_POST[ $this->meta_key_prefix ]['media']['video_id'];
		}
		if ( isset( $_POST[ $this->meta_key_prefix ]['media']['embed_video_url'] ) ) {
			$post_data['media']['embed_video_url'] = sanitize_text_field( wp_unslash( $_POST[ $this->meta_key_prefix ]['media']['embed_video_url'] ) );
		}
		if ( isset( $_POST[ $this->meta_key_prefix ]['media']['text'] ) ) {
			$post_data['media']['text'] = sanitize_textarea_field( wp_unslash( $_POST[ $this->meta_key_prefix ]['media']['text'] ) );
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
				$connected = get_post_meta( $post_id, META_KEY_LESSON_COURSE_IDS, true );
				if ( empty( $connected ) ) {
					esc_html_e( 'Not Assigned Yet', 'bluedolphin-lms' );
					break;
				}
				$connected = array_map(
					function ( $q ) {
						$url   = get_edit_post_link( $q );
						$title = get_the_title( $q );
						return '<a class="" href="' . esc_url( $url ) . '" target="_blank">' . $title . '</a>';
					},
					$connected
				);
				echo wp_kses(
					implode( ', ', $connected ),
					array(
						'a' => array(
							'href'   => array(),
							'title'  => array(),
							'class'  => array(),
							'target' => array(),
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
				$duration_type .= $duration > 1 ? 's' : '';
				printf( '%d %s', (int) $duration, esc_html( ucfirst( $duration_type ) ) );
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
		if ( wp_verify_nonce( $nonce, BDLMS_BASEFILE ) ) {
			require_once BDLMS_TEMPLATEPATH . '/admin/lesson/modal-popup.php';
			exit;
		}
	}

	/**
	 * Assign to course.
	 */
	public function assign_to_course() {
		check_ajax_referer( BDLMS_BASEFILE, 'bdlms_nonce' );
		$post_id  = isset( $_POST['post_id'] ) ? (int) $_POST['post_id'] : 0;
		$selected = isset( $_POST['selected'] ) ? map_deep( $_POST['selected'], 'intval' ) : array();
		update_post_meta( $post_id, META_KEY_LESSON_COURSE_IDS, array_unique( $selected ) );
		wp_send_json(
			array(
				'status'  => true,
				'message' => __( 'Saved.', 'bluedolphin-lms' ),
			)
		);
		exit;
	}
}
