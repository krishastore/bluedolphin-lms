<?php
/**
 * The file that register metabox for quiz.
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
use const BlueDolphin\Lms\BDLMS_QUIZ_CPT;
use const BlueDolphin\Lms\BDLMS_QUESTION_TAXONOMY_TAG;

/**
 * Register metaboxes for quiz.
 */
class Quiz extends \BlueDolphin\Lms\Collections\PostTypes {

	/**
	 * Meta key name.
	 *
	 * @var string $meta_key
	 */
	public $meta_key = '_quiz_options';

	/**
	 * Class construct.
	 */
	public function __construct() {
		$this->set_metaboxes( $this->meta_boxes_list() );

		// Hooks.
		add_action( 'save_post_' . BDLMS_QUIZ_CPT, array( $this, 'save_metadata' ) );
		add_filter( 'manage_' . BDLMS_QUIZ_CPT . '_posts_columns', array( $this, 'add_new_table_columns' ) );
		add_filter( 'post_row_actions', array( $this, 'quick_actions' ), 10, 2 );
		add_action( 'manage_' . BDLMS_QUIZ_CPT . '_posts_custom_column', array( $this, 'manage_custom_column' ), 10, 2 );
		add_action( 'quick_edit_custom_box', array( $this, 'quick_edit_custom_box' ), 10, 2 );
	}

	/**
	 * Meta boxes list.
	 *
	 * @return array
	 */
	private function meta_boxes_list() {
		$list = apply_filters(
			'bluedolphin/questions/meta_boxes',
			array(
				array(
					'id'       => 'quiz-questions',
					'title'    => __( 'Questions', 'bluedolphin-lms' ),
					'callback' => array( $this, 'render_questions' ),
				),
				array(
					'id'       => 'quiz-settings',
					'title'    => __( 'Quiz Settings', 'bluedolphin-lms' ),
					'callback' => array( $this, 'render_quiz_settings' ),
				),
			)
		);
		return $list;
	}

	/**
	 * Render questions metabox.
	 */
	public function render_questions() {
		require_once BDLMS_TEMPLATEPATH . '/admin/quiz/metabox-questions.php';
	}

	/**
	 * Render quiz settings metabox.
	 */
	public function render_quiz_settings() {
		require_once BDLMS_TEMPLATEPATH . '/admin/quiz/metabox-quiz-settings.php';
	}

	/**
	 * Save post meta.
	 */
	public function save_metadata() {
		// Save process here...
	}

	/**
	 * Add new table columns.
	 *
	 * @param array $columns Columns list.
	 * @return array
	 */
	public function add_new_table_columns( $columns ) {
		unset( $columns['date'] );
		unset( $columns['author'] );
		$columns['total_questions'] = __( 'Total Questions', 'bluedolphin-lms' );
		$columns['total_marks']     = __( 'Total Marks', 'bluedolphin-lms' );
		$columns['passing_marks']   = __( 'Passing Marks', 'bluedolphin-lms' );
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
		$data = get_post_meta( $post_id, $this->meta_key, true );
		switch ( $column ) {
			case 'total_questions':
				echo '—';
				break;

			case 'total_marks':
				echo '—';
				break;

			case 'passing_marks':
				echo '—';
				break;

			default:
				break;
		}
	}

	/**
	 * Quick edit custom box.
	 *
	 * @param string $column_name Column name.
	 * @param string $post_type Post Type.
	 */
	public function quick_edit_custom_box( $column_name, $post_type ) {
		if ( BDLMS_QUIZ_CPT !== $post_type ) {
			return;
		}
		?>
		<fieldset class="inline-edit-col-right inline-edit-levels">
		<div class="inline-edit-col inline-edit-<?php echo esc_attr( $column_name ); ?>">
		<label class="inline-edit-group">
			Inline edit box here...
		</label>
		</div>
	</fieldset>
		<?php
	}

	/**
	 * Filters the array of row action links on the Posts list table.
	 *
	 * @param array  $actions Row action.
	 * @param object $post Post object.
	 * @return array
	 */
	public function quick_actions( $actions, $post ) {
		// Clone action.
		if ( in_array( $post->post_type, array( \BlueDolphin\Lms\BDLMS_QUIZ_CPT ), true ) ) {
			$url                   = wp_nonce_url(
				add_query_arg(
					array(
						'action' => 'bdlms_clone',
						'post'   => $post->ID,
					),
					'admin.php'
				),
				BDLMS_BASEFILE,
				'bdlms_nonce'
			);
			$actions['clone_post'] = '<a href="' . esc_url( $url ) . '">' . esc_attr__( 'Clone', 'bluedolphin-lms' ) . ' </a>';
		}
		return $actions;
	}
}
