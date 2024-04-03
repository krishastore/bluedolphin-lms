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

/**
 * Register metaboxes for lesson.
 */
class Lesson extends \BlueDolphin\Lms\Collections\PostTypes {

	/**
	 * Meta key name.
	 *
	 * @var string $meta_key
	 */
	public $meta_key = '_bdlms_lesson';

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
		require_once BDLMS_TEMPLATEPATH . '/admin/lesson/add-media.php';
	}

	/**
	 * Render lesson settings metabox.
	 */
	public function render_lesson_settings() {
		global $post;
		$post_id = isset( $post->ID ) ? $post->ID : 0;
		require_once BDLMS_TEMPLATEPATH . '/admin/lesson/lesson-settings.php';
	}

	/**
	 * Render assign to course metabox.
	 */
	public function render_assign_to_course() {
		global $post;
		?>
			<div class="bdlms-assign-quiz">
				<a href="javascript:;" class="button button-primary button-large" data-modal="assign_lesson"><?php esc_html_e( 'Click to assign lesson', 'bluedolphin-lms' ); ?></a>
			</div>
			<div class="bdlms-snackbar-notice"><p></p></div>
		<?php
		require_once BDLMS_TEMPLATEPATH . '/admin/lesson/modal-popup.php';
	}

	/**
	 * Save post meta.
	 */
	public function save_metadata() {
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
		$columns[ $topic_key ]  = __( 'Topic', 'bluedolphin-lms' );
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
		switch ( $column ) {
			case 'post_author':
				echo wp_kses_post( postAuthor( $post_id ) );
				break;
			default:
				break;
		}
	}
}
