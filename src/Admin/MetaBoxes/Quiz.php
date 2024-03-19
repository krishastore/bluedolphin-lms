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
	public $meta_key = '_bdlms_quiz';

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
		global $post;
		$post_id  = isset( $post->ID ) ? $post->ID : 0;
		$data     = get_post_meta( $post_id, $this->meta_key, true );
		$settings = isset( $data['settings'] ) ? $data['settings'] : array();
		$settings = wp_parse_args(
			$settings,
			array(
				'duration'            => 0,
				'duration_type'       => '',
				'passing_marks'       => 0,
				'negative_marking'    => 0,
				'show_correct_review' => 0,
				'review'              => 0,

			)
		);
		require_once BDLMS_TEMPLATEPATH . '/admin/quiz/metabox-quiz-settings.php';
	}

	/**
	 * Save post meta.
	 */
	public function save_metadata() {
		global $post;
		$post_id   = isset( $post->ID ) ? $post->ID : 0;
		$post_data = array();

		if ( ( isset( $_POST['action'] ) && 'inline-save' !== $_POST['action'] ) && ( isset( $_POST['bdlms_nonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bdlms_nonce'] ) ), BDLMS_BASEFILE ) ) ) {
			return;
		}

		// Quick edit action.
		if ( isset( $_POST['action'] ) && 'inline-save' === $_POST['action'] ) {
			$post_id   = isset( $_POST['post_ID'] ) ? (int) $_POST['post_ID'] : $post_id;
			$post_data = get_post_meta( $post_id, $this->meta_key, true );
		}

		if ( isset( $_POST[ $this->meta_key ]['questions'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$post_data['questions'] = map_deep( $_POST[ $this->meta_key ]['questions'], 'intval' );
		}

		if ( isset( $_POST[ $this->meta_key ]['settings']['duration'] ) ) {
			$post_data['settings']['duration'] = (int) $_POST[ $this->meta_key ]['settings']['duration'];
		}
		if ( isset( $_POST[ $this->meta_key ]['settings']['duration_type'] ) ) {
			$post_data['settings']['duration_type'] = sanitize_textarea_field( wp_unslash( $_POST[ $this->meta_key ]['settings']['duration_type'] ) );
		}
		if ( isset( $_POST[ $this->meta_key ]['settings']['passing_marks'] ) ) {
			$post_data['settings']['passing_marks'] = (int) $_POST[ $this->meta_key ]['settings']['passing_marks'];
		}
		if ( isset( $_POST[ $this->meta_key ]['settings']['negative_marking'] ) ) {
			$post_data['settings']['negative_marking'] = 1;
		}
		if ( isset( $_POST[ $this->meta_key ]['settings']['review'] ) ) {
			$post_data['settings']['review'] = 1;
		}
		if ( isset( $_POST[ $this->meta_key ]['settings']['show_correct_review'] ) ) {
			$post_data['settings']['show_correct_review'] = 1;
		}

		update_post_meta( $post_id, $this->meta_key, $post_data );
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
				echo isset( $data['questions'] ) && is_array( $data['questions'] ) ? count( $data['questions'] ) : 0;
				break;

			case 'total_marks':
				echo '—';
				break;

			case 'passing_marks':
				echo isset( $data['settings']['passing_marks'] ) ? (int) $data['settings']['passing_marks'] : 0;
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
		if ( BDLMS_QUIZ_CPT !== $post_type || 'total_questions' !== $column_name ) {
			return;
		}
		?>
		<fieldset class="inline-edit-col-right inline-edit-quiz">
			<div class="inline-edit-col inline-edit-<?php echo esc_attr( $column_name ); ?>">
				<div class="inline-edit-quiz">
					<div class="inline-edit-quiz-item bdlms-passing-marks">
						<label>
							<span class="title"><?php esc_html_e( 'Passing Marks', 'bluedolphin-lms' ); ?></span>
							<input type="text" name="<?php echo esc_attr( $this->meta_key ); ?>[settings][passing_marks]">
						</label>
					</div>
					<div class="inline-edit-quiz-item">
						<label>
							<span class="title"><?php esc_html_e( 'Status', 'bluedolphin-lms' ); ?></span>
							<select name="_status">
								<option value="publish"><?php esc_html_e( 'Published', 'bluedolphin-lms' ); ?></option>
								<option value="pending"><?php esc_html_e( 'Pending Review', 'bluedolphin-lms' ); ?></option>
								<option value="draft"><?php esc_html_e( 'Draft', 'bluedolphin-lms' ); ?></option>
							</select>
						</label>
					</div>
					<div class="inline-edit-quiz-item">
						<label>
							<?php
								$taxonomy = \BlueDolphin\Lms\BDLMS_QUIZ_TAXONOMY_LEVEL_1;
							?>
							<span class="title"><?php esc_html_e( 'Category (Level 1)', 'bluedolphin-lms' ); ?></span>
							<ul class="cat-checklist <?php echo esc_attr( $taxonomy ); ?>-checklist">
								<?php wp_terms_checklist( 0, array( 'taxonomy' => $taxonomy ) ); ?>
							</ul>
						</label>
					</div>
					<div class="inline-edit-quiz-item">
						<label>
							<?php
								$taxonomy = \BlueDolphin\Lms\BDLMS_QUIZ_TAXONOMY_LEVEL_2;
							?>
							<span class="title"><?php esc_html_e( 'Category (Level 2)', 'bluedolphin-lms' ); ?></span>
							<ul class="cat-checklist <?php echo esc_attr( $taxonomy ); ?>-checklist">
								<?php wp_terms_checklist( 0, array( 'taxonomy' => $taxonomy ) ); ?>
							</ul>
						</label>
					</div>
				</div>
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