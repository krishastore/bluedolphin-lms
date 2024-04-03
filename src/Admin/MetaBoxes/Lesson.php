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
}
