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
class Quiz extends \BlueDolphin\Lms\Admin\MetaBoxes\QuestionBank {

	/**
	 * Meta key name.
	 *
	 * @var string $meta_key
	 */
	public $meta_key = '_bdlms_quiz';

	/**
	 * Question module meta key name.
	 *
	 * @var string $question_meta_key
	 */
	public $question_meta_key = '_bdlms_question';

	/**
	 * Class construct.
	 */
	public function __construct() {
		$this->set_metaboxes( $this->meta_boxes_list() );
		$this->alphabets = \BlueDolphin\Lms\question_series();

		// Hooks.
		add_action( 'save_post_' . BDLMS_QUIZ_CPT, array( $this, 'save_metadata' ) );
		add_filter( 'manage_' . BDLMS_QUIZ_CPT . '_posts_columns', array( $this, 'add_new_table_columns' ) );
		add_filter( 'post_row_actions', array( $this, 'quick_actions' ), 10, 2 );
		add_action( 'manage_' . BDLMS_QUIZ_CPT . '_posts_custom_column', array( $this, 'manage_custom_column' ), 10, 2 );
		add_action( 'quick_edit_custom_box', array( $this, 'quick_edit_custom_box' ), 10, 2 );
		add_action( 'admin_action_search_question', array( $this, 'search_question' ) );
		add_action( 'wp_ajax_bdlms_quiz_question', array( $this, 'handle_quiz_question' ) );
		add_action( 'wp_ajax_bdlms_inline_duplicate_question', array( $this, 'inline_duplicate_question' ) );
		add_action( 'wp_ajax_bdlms_add_new_question', array( $this, 'add_new_question' ) );
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
		global $post;
		$post_id   = isset( $post->ID ) ? $post->ID : 0;
		$questions = get_post_meta( $post_id, $this->meta_key . '_question_ids', true );
		$questions = ! empty( $questions ) ? $questions : array();
		$questions = array_filter(
			$questions,
			function ( $id ) {
				$status = get_post_status( $id );
				return in_array( $status, array( 'publish', 'draft' ), true );
			}
		);
		require_once BDLMS_TEMPLATEPATH . '/admin/quiz/metabox-questions.php';
	}

	/**
	 * Render quiz settings metabox.
	 */
	public function render_quiz_settings() {
		global $post;
		$post_id  = isset( $post->ID ) ? $post->ID : 0;
		$settings = get_post_meta( $post_id, $this->meta_key . '_settings', true );
		$settings = ! empty( $settings ) ? $settings : array();
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
			$post_id     = isset( $_POST['post_ID'] ) ? (int) $_POST['post_ID'] : $post_id;
			$meta_groups = get_post_meta( $post_id, $this->meta_key . '_groups', true );
			if ( ! empty( $meta_groups ) ) {
				foreach ( $meta_groups as $meta_group ) {
					$index_key               = str_replace( $this->meta_key . '_', '', $meta_group );
					$post_data[ $index_key ] = get_post_meta( $post_id, $meta_group, true );
				}
			}
		}

		if ( isset( $_POST[ $this->meta_key ]['question_id'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$question_ids = map_deep( $_POST[ $this->meta_key ]['question_id'], 'intval' );
			foreach ( $question_ids as $question_id ) {
				if ( 'publish' === get_post_status( $question_id ) ) {
					continue;
				}
				wp_update_post(
					array(
						'ID'          => $question_id,
						'post_status' => 'publish',
					),
					false,
					false
				);
			}
			$post_data['question_ids'] = $question_ids;
		}

		do_action( 'bdlms_save_quiz_before', $post_id, $_POST );

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
		$post_data = apply_filters( 'bdlms_quiz_post_data', $post_data );

		$meta_groups = array();
		foreach ( $post_data as $key => $data ) {
			$key           = $this->meta_key . '_' . $key;
			$meta_groups[] = $key;
			update_post_meta( $post_id, $key, $data );
		}
		update_post_meta( $post_id, $this->meta_key . '_groups', $meta_groups );
		do_action( 'bdlms_save_quiz_after', $post_id, $post_data );
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
		$settings     = get_post_meta( $post_id, $this->meta_key . '_settings', true );
		$question_ids = get_post_meta( $post_id, $this->meta_key . '_question_ids', true );
		$total_marks  = array_map(
			function ( $question_id ) {
				$question_settings = get_post_meta( $question_id, $this->question_meta_key . '_settings', true );
				return isset( $question_settings['points'] ) ? (int) $question_settings['points'] : 0;
			},
			$question_ids
		);
		switch ( $column ) {
			case 'total_questions':
				echo ! empty( $question_ids ) ? count( $question_ids ) : 0;
				break;

			case 'total_marks':
				echo (int) array_sum( $total_marks );
				break;

			case 'passing_marks':
				echo isset( $settings['passing_marks'] ) ? (int) $settings['passing_marks'] : 0;
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
		<?php do_action( 'bdlms_inline_quiz_edit_field', $column_name, $post_type, $this ); ?>
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

	/**
	 * Save/Edit Quiz Question.
	 */
	public function handle_quiz_question() {
		check_ajax_referer( BDLMS_BASEFILE, '_nonce' );
		$post_id          = isset( $_POST['post_id'] ) ? (int) $_POST['post_id'] : 0;
		$post_title       = isset( $_POST[ $this->question_meta_key ]['post_title'] ) ? sanitize_text_field( wp_unslash( $_POST[ $this->question_meta_key ]['post_title'] ) ) : '';
		$_POST['action']  = 'inline-save';
		$_POST['post_ID'] = $post_id;

		$post_id = wp_insert_post(
			array(
				'ID'          => $post_id,
				'post_title'  => $post_title,
				'post_type'   => \BlueDolphin\Lms\BDLMS_QUESTION_CPT,
				'post_status' => 'publish',
			)
		);
		if ( is_wp_error( $post_id ) ) {
			wp_send_json(
				array(
					'post_id' => $post_id,
					'status'  => false,
					'message' => __( 'Error', 'bluedolphin-lms' ),
				)
			);
		}
		wp_send_json(
			array(
				'post_id' => $post_id,
				'status'  => true,
				'message' => __( 'Question updated.', 'bluedolphin-lms' ),
			)
		);
		exit;
	}

	/**
	 * Inline duplicate question process.
	 */
	public function inline_duplicate_question() {
		$clone_post = $this->clone_post( true );

		if ( empty( $clone_post['post_id'] ) ) {
			wp_send_json(
				array(
					'post_id' => $post_id,
					'status'  => false,
					'message' => __( 'Error', 'bluedolphin-lms' ),
				)
			);
		}
		wp_send_json(
			array(
				'post_id' => $clone_post['post_id'],
				'status'  => true,
				'message' => __( 'Successfully duplicated.', 'bluedolphin-lms' ),
			)
		);
		exit;
	}

	/**
	 * Ajax add new question.
	 */
	public function add_new_question() {
		check_ajax_referer( BDLMS_BASEFILE, 'bdlms_nonce' );
		$questions = isset( $_POST['selected'] ) ? array_map( 'intval', $_POST['selected'] ) : array();
		$action    = isset( $_POST['_action'] ) ? sanitize_text_field( wp_unslash( $_POST['_action'] ) ) : '';
		$message   = __( 'Question Added.', 'bluedolphin-lms' );

		if ( 'create_new' === $action ) {
			$post_id = wp_insert_post(
				array(
					'post_title'  => '',
					'post_type'   => \BlueDolphin\Lms\BDLMS_QUESTION_CPT,
					'post_status' => 'auto-draft',
				)
			);
			if ( ! is_wp_error( $post_id ) ) {
				$questions = array( $post_id );
			}
		}
		ob_start();
		require BDLMS_TEMPLATEPATH . '/admin/quiz/question-list.php';
		$content = ob_get_clean();
		wp_send_json(
			array(
				'status'  => true,
				'html'    => $content,
				'message' => $message,
			)
		);
		exit;
	}

	/**
	 * Search question by keywords.
	 */
	public function search_question() {
		$nonce = isset( $_REQUEST['_nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_nonce'] ) ) : '';
		$s     = isset( $_REQUEST['s'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ) : '';
		if ( wp_verify_nonce( $nonce, BDLMS_BASEFILE ) ) {
			require_once BDLMS_TEMPLATEPATH . '/admin/quiz/modal-popup.php';
			exit;
		}
	}
}