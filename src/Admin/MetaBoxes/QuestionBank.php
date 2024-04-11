<?php
/**
 * The file that register metabox for question bank.
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
use const BlueDolphin\Lms\BDLMS_QUESTION_CPT;
use const BlueDolphin\Lms\BDLMS_QUESTION_TAXONOMY_TAG;
use const BlueDolphin\Lms\META_KEY_QUESTION_TYPE;
use const BlueDolphin\Lms\META_KEY_QUESTION_SETTINGS;
use const BlueDolphin\Lms\META_KEY_QUESTION_GROUPS;
use const BlueDolphin\Lms\META_KEY_RIGHT_ANSWERS;
use const BlueDolphin\Lms\META_KEY_ANSWERS_LIST;
use const BlueDolphin\Lms\META_KEY_MANDATORY_ANSWERS;
use const BlueDolphin\Lms\META_KEY_OPTIONAL_ANSWERS;
use const BlueDolphin\Lms\META_KEY_QUIZ_QUESTION_IDS;

/**
 * Register metaboxes for question bank.
 */
class QuestionBank extends \BlueDolphin\Lms\Collections\PostTypes {

	/**
	 * Meta key prefix.
	 *
	 * @var string $meta_key_prefix
	 */
	public $meta_key_prefix = \BlueDolphin\Lms\META_KEY_QUESTION_PREFIX;

	/**
	 * Question alphabets.
	 *
	 * @var array $alphabets
	 */
	public $alphabets = array();

	/**
	 * Class construct.
	 */
	public function __construct() {
		$this->set_metaboxes( $this->meta_boxes_list() );
		$this->alphabets = \BlueDolphin\Lms\question_series();

		// Hooks.
		add_action( 'save_post_' . BDLMS_QUESTION_CPT, array( $this, 'save_metadata' ) );
		add_filter( 'manage_edit-' . BDLMS_QUESTION_CPT . '_sortable_columns', array( $this, 'sortable_columns' ) );
		add_filter( 'manage_' . BDLMS_QUESTION_CPT . '_posts_columns', array( $this, 'add_new_table_columns' ) );
		add_filter( 'post_row_actions', array( $this, 'quick_actions' ), 10, 2 );
		add_action( 'manage_' . BDLMS_QUESTION_CPT . '_posts_custom_column', array( $this, 'manage_custom_column' ), 10, 2 );
		add_action( 'quick_edit_custom_box', array( $this, 'quick_edit_custom_box' ), 10, 2 );
		add_action( 'bulk_edit_custom_box', array( $this, 'bulk_edit_custom_box' ), 10, 2 );
		add_action( 'bulk_edit_posts', array( $this, 'bulk_edit_posts' ), 10, 2 );
		add_action( 'wp_ajax_bdlms_assign_to_quiz', array( $this, 'assign_to_quiz' ) );
		add_action( 'admin_action_load_quiz_list', array( $this, 'load_quiz_list' ) );
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
					'id'       => 'answer-options',
					'title'    => __( 'Answer Options', 'bluedolphin-lms' ),
					'callback' => array( $this, 'render_answer_options' ),
				),
				array(
					'id'       => 'question-settings',
					'title'    => __( 'Question Settings', 'bluedolphin-lms' ),
					'callback' => array( $this, 'render_question_settings' ),
				),
				array(
					'id'       => 'assign-to-quiz',
					'title'    => __( 'Assign to Quiz', 'bluedolphin-lms' ),
					'callback' => array( $this, 'render_assign_to_quiz' ),
					'screen'   => null,
					'context'  => 'side',
				),
			)
		);
		return $list;
	}

	/**
	 * Render answer options metabox.
	 */
	public function render_answer_options() {
		global $post;
		$post_id = isset( $post->ID ) ? $post->ID : 0;
		$type    = get_post_meta( $post_id, META_KEY_QUESTION_TYPE, true );
		$type    = ! empty( $type ) ? $type : 'true_or_false';
		$data    = \BlueDolphin\Lms\get_question_by_type( $post_id, $type );
		require_once BDLMS_TEMPLATEPATH . '/admin/question/metabox-answer-options.php';
	}

	/**
	 * Render question settings metabox.
	 */
	public function render_question_settings() {
		global $post;
		$post_id  = isset( $post->ID ) ? $post->ID : 0;
		$settings = get_post_meta( $post_id, META_KEY_QUESTION_SETTINGS, true );
		$settings = ! empty( $settings ) ? $settings : array();
		$levels   = isset( $settings['levels'] ) ? $settings['levels'] : '';
		$status   = isset( $settings['status'] ) ? $settings['status'] : 0;
		require_once BDLMS_TEMPLATEPATH . '/admin/question/metabox-question-settings.php';
	}

	/**
	 * Render assign to quiz metabox.
	 */
	public function render_assign_to_quiz() {
		global $post;
		?>
			<div class="bdlms-assign-quiz">
				<a href="javascript:;" class="button button-primary button-large" data-modal="assign_quiz"><?php esc_html_e( 'Click to assign quiz', 'bluedolphin-lms' ); ?></a>
			</div>
			<div class="bdlms-snackbar-notice"><p></p></div>
		<?php
		require_once BDLMS_TEMPLATEPATH . '/admin/question/modal-popup.php';
	}

	/**
	 * Save post meta.
	 */
	public function save_metadata() {
		global $post;
		$post_id   = isset( $post->ID ) ? $post->ID : 0;
		$post_data = array(
			'settings' => array(),
			'type'     => '',
		);

		if ( ! isset( $_POST['bdlms_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bdlms_nonce'] ) ), BDLMS_BASEFILE ) ) {
			return;
		}

		$type = isset( $_POST[ $this->meta_key_prefix ]['type'] ) ? sanitize_text_field( wp_unslash( $_POST[ $this->meta_key_prefix ]['type'] ) ) : '';
		// Quick edit action.
		if ( isset( $_POST['action'] ) && 'inline-save' === $_POST['action'] ) {
			$post_id     = isset( $_POST['post_ID'] ) ? (int) $_POST['post_ID'] : $post_id;
			$meta_groups = get_post_meta( $post_id, META_KEY_QUESTION_GROUPS, true );
			if ( ! empty( $meta_groups ) ) {
				foreach ( $meta_groups as $meta_group ) {
					$index_key               = str_replace( $this->meta_key_prefix . '_', '', $meta_group );
					$post_data[ $index_key ] = get_post_meta( $post_id, $meta_group, true );
				}
			}
		}

		do_action( 'bdlms_save_question_before', $post_id, $post_data, $_POST );

		if ( isset( $_POST[ $this->meta_key_prefix ][ $type ] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$post_data[ $type ] = map_deep( $_POST[ $this->meta_key_prefix ][ $type ], 'sanitize_text_field' );
		}
		if ( isset( $_POST[ $this->meta_key_prefix ][ $type . '_answers' ] ) ) {
			if ( is_array( $_POST[ $this->meta_key_prefix ][ $type . '_answers' ] ) ) {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
				$current_answer = map_deep( $_POST[ $this->meta_key_prefix ][ $type . '_answers' ], 'intval' );
				$current_answer = array_map(
					function ( $i ) use ( $post_data, $type ) {
						if ( isset( $post_data[ $type ][ $i ] ) ) {
							return wp_hash( $post_data[ $type ][ $i ] );
						}
						return wp_hash( $i );
					},
					$current_answer
				);

				$post_data[ $type . '_answers' ] = $current_answer;
			} else {
				$current_answer                  = (int) $_POST[ $this->meta_key_prefix ][ $type . '_answers' ];
				$post_data[ $type . '_answers' ] = isset( $post_data[ $type ][ $current_answer ] ) ? wp_hash( $post_data[ $type ][ $current_answer ] ) : wp_hash( $current_answer );
			}
		}

		$post_data['settings']['status'] = 0;
		if ( isset( $_POST[ $this->meta_key_prefix ]['settings']['status'] ) ) {
			$post_data['settings']['status'] = 1;
		}

		if ( isset( $_POST[ $this->meta_key_prefix ]['mandatory_answers'] ) ) {
			$post_data['mandatory_answers'] = sanitize_text_field( wp_unslash( $_POST[ $this->meta_key_prefix ]['mandatory_answers'] ) );
		}

		if ( isset( $_POST[ $this->meta_key_prefix ]['optional_answers'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$post_data['optional_answers'] = map_deep( $_POST[ $this->meta_key_prefix ]['optional_answers'], 'sanitize_text_field' );
		}

		if ( isset( $_POST[ $this->meta_key_prefix ]['settings']['points'] ) ) {
			$post_data['settings']['points'] = (int) $_POST[ $this->meta_key_prefix ]['settings']['points'];
		}
		if ( isset( $_POST[ $this->meta_key_prefix ]['settings']['hint'] ) ) {
			$post_data['settings']['hint'] = sanitize_textarea_field( wp_unslash( $_POST[ $this->meta_key_prefix ]['settings']['hint'] ) );
		}
		if ( isset( $_POST[ $this->meta_key_prefix ]['settings']['explanation'] ) ) {
			$post_data['settings']['explanation'] = sanitize_textarea_field( wp_unslash( $_POST[ $this->meta_key_prefix ]['settings']['explanation'] ) );
		}

		if ( isset( $_POST[ $this->meta_key_prefix ]['settings']['levels'] ) ) {
			$post_data['settings']['levels'] = sanitize_textarea_field( wp_unslash( $_POST[ $this->meta_key_prefix ]['settings']['levels'] ) );
		}

		if ( ! empty( $type ) ) {
			$post_data['type'] = $type;
		}
		$post_data = apply_filters( 'bdlms_question_post_data', $post_data, $_POST, $post_id );

		$meta_groups = array();
		foreach ( $post_data as $key => $data ) {
			$key           = $this->meta_key_prefix . '_' . $key;
			$meta_groups[] = $key;
			update_post_meta( $post_id, $key, $data );
		}
		update_post_meta( $post_id, META_KEY_QUESTION_GROUPS, $meta_groups );

		do_action( 'bdlms_save_question_after', $post_id, $post_data, $_POST );
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

		$topic_key = 'taxonomy-' . BDLMS_QUESTION_TAXONOMY_TAG;
		$topic     = $columns[ $topic_key ];
		unset( $columns[ $topic_key ] );
		unset( $columns['author'] );
		$columns['post_author'] = __( 'Author', 'bluedolphin-lms' );
		$columns['quiz']        = __( 'Quiz', 'bluedolphin-lms' );
		$columns['levels']      = __( 'Levels', 'bluedolphin-lms' );
		$columns['type']        = __( 'Type', 'bluedolphin-lms' );
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
		$type     = get_post_meta( $post_id, META_KEY_QUESTION_TYPE, true );
		$settings = get_post_meta( $post_id, META_KEY_QUESTION_SETTINGS, true );
		switch ( $column ) {
			case 'quiz':
				$connected = get_posts(
					array(
						'post_type'    => \BlueDolphin\Lms\BDLMS_QUIZ_CPT,
						// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
						'meta_key'     => META_KEY_QUIZ_QUESTION_IDS,
						// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
						'meta_value'   => array( $post_id ),
						'meta_compare' => 'REGEXP',
						'fields'       => 'ids',
					)
				);
				if ( empty( $connected ) ) {
					echo '—';
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

			case 'type':
				echo ! empty( $type ) ? esc_html( ucwords( str_replace( '_', ' ', $type ) ) ) : '—';
				break;

			case 'levels':
				echo ! empty( $settings['levels'] ) ? esc_html( ucwords( str_replace( '_', ' ', $settings['levels'] ) ) ) : '—';
				break;

			case 'post_author':
				echo wp_kses_post( postAuthor( $post_id ) );
				break;

			default:
				break;
		}
	}

	/**
	 * Sortable columns list.
	 *
	 * @param array $columns Sortable columns.
	 *
	 * @return array
	 */
	public function sortable_columns( $columns ) {
		$columns['quiz']        = 'quiz';
		$columns['post_author'] = 'author';
		return $columns;
	}

	/**
	 * Quick edit custom box.
	 *
	 * @param string $column_name Column name.
	 * @param string $post_type Post Type.
	 */
	public function quick_edit_custom_box( $column_name, $post_type ) {
		if ( BDLMS_QUESTION_CPT !== $post_type ) {
			return;
		}
		switch ( $column_name ) {
			case 'levels':
				?>
			<fieldset class="inline-edit-col-right inline-edit-levels">
				<?php wp_nonce_field( BDLMS_BASEFILE, 'bdlms_nonce', false ); ?>
				<div class="inline-edit-col inline-edit-<?php echo esc_attr( $column_name ); ?>">
					<label class="inline-edit-group">
						<span class="title"><?php esc_html_e( 'Difficulty Level', 'bluedolphin-lms' ); ?></span>
							<select name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[settings][levels]">
								<?php
								foreach ( \BlueDolphin\Lms\question_levels() as $key => $level ) {
									?>
										<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $level ); ?></option>
									<?php
								}
								?>
							</select>
					</label>
				</div>
			</fieldset>
				<?php
				break;
		}
		do_action( 'bdlms_inline_question_edit_field', $column_name, $post_type, $this );
	}

	/**
	 * Filters the array of row action links on the Posts list table.
	 *
	 * @param array  $actions Row action.
	 * @param object $post Post object.
	 * @return array
	 */
	public function quick_actions( $actions, $post ) {
		if ( BDLMS_QUESTION_CPT === $post->post_type ) {
			$settings = get_post_meta( $post->ID, META_KEY_QUESTION_SETTINGS, true );
			$type     = get_post_meta( $post->ID, META_KEY_QUESTION_TYPE, true );
			$answers  = get_post_meta( $post->ID, sprintf( META_KEY_RIGHT_ANSWERS, $type ), true );
			$type     = ! empty( $type ) ? $type : 'true_or_false';

			$data = array(
				'title' => $post->post_title,
				'type'  => $type,
				'marks' => isset( $settings['points'] ) ? $settings['points'] : '',
			);
			if ( 'fill_blank' !== $type ) {
				$answers_list  = (array) get_post_meta( $post->ID, sprintf( META_KEY_ANSWERS_LIST, $type ), true );
				$answers_list  = array_map(
					function ( $answer_list ) use ( $answers ) {
						$checked = is_array( $answers ) ? in_array( wp_hash( $answer_list ), $answers, true ) : wp_hash( $answer_list ) === $answers;
						return array(
							'option'  => $answer_list,
							'checked' => $checked,
						);
					},
					$answers_list
				);
				$data[ $type ] = $answers_list;
			}
			if ( 'fill_blank' === $type ) {
				$mandatory         = get_post_meta( $post->ID, META_KEY_MANDATORY_ANSWERS, true );
				$optional          = get_post_meta( $post->ID, META_KEY_OPTIONAL_ANSWERS, true );
				$data['mandatory'] = ! empty( $mandatory ) ? $mandatory : '';
				$data['optional']  = ! empty( $optional ) ? $optional : '';
			}
			$data['status']         = $post->post_status;
			$actions['show_answer'] = '<a href="javascript:;" data-inline_edit="' . esc_attr( wp_json_encode( $data ) ) . '" aria-expanded="false">' . __( 'Show Answer', 'bluedolphin-lms' ) . '<a>';
		}

		// Clone action.
		if ( in_array( $post->post_type, array( \BlueDolphin\Lms\BDLMS_QUESTION_CPT ), true ) ) {
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
	 * Bulk edit custom box.
	 *
	 * @param string $column_name Column name.
	 * @param string $post_type Post Type.
	 */
	public function bulk_edit_custom_box( $column_name, $post_type ) {
		if ( BDLMS_QUESTION_CPT !== $post_type ) {
			return;
		}
		?>
		<?php
		switch ( $column_name ) {
			case 'post_author':
				?>
			<fieldset class="inline-edit-col-right bulk-inline-edit-levels">
				<div class="inline-edit-col inline-edit-<?php echo esc_attr( $column_name ); ?>">
					<label class="inline-edit-group">
						<span class="title"><?php esc_html_e( 'Level', 'bluedolphin-lms' ); ?></span>
							<select name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[settings][levels]">
								<?php
								foreach ( \BlueDolphin\Lms\question_levels() as $key => $level ) {
									?>
										<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $level ); ?></option>
									<?php
								}
								?>
							</select>
					</label>
					<label class="inline-edit-group">
						<span class="title"><?php esc_html_e( 'Marks', 'bluedolphin-lms' ); ?></span>
						<input type="number" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[settings][points]" step="1" min="1">
					</label>
					<label class="inline-edit-group"><span class="title"><?php esc_html_e( 'Hide Question? ', 'bluedolphin-lms' ); ?></span><input type="checkbox" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[settings][status]" value="1"></label>
				</div>
			</fieldset>
				<?php
				break;
		}
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
		$post_data = isset( $post_data[ $this->meta_key_prefix ] ) ? $post_data[ $this->meta_key_prefix ] : array();
		if ( ! isset( $current_screen->post_type ) || BDLMS_QUESTION_CPT !== $current_screen->post_type ) {
			return;
		}
		foreach ( $updated as $qid ) {
			foreach ( $post_data as $key => $data ) {
				if ( isset( $data['status'] ) ) {
					$data['status'] = (int) $data['status'];
				}
				$key   = $this->meta_key_prefix . '_' . $key;
				$_data = get_post_meta( $qid, $key, true );
				$_data = ! empty( $_data ) ? $_data : array();
				$_data = array_merge( $_data, $data );
				update_post_meta( $qid, $key, $_data );
			}
		}
	}

	/**
	 * Assign to quiz.
	 */
	public function assign_to_quiz() {
		check_ajax_referer( BDLMS_BASEFILE, 'bdlms_nonce' );
		$post_id  = isset( $_POST['post_id'] ) ? (int) $_POST['post_id'] : 0;
		$selected = isset( $_POST['selected'] ) ? map_deep( $_POST['selected'], 'intval' ) : array();
		// Question unassigned.
		$quiz_ids     = get_posts(
			array(
				'post_type'    => \BlueDolphin\Lms\BDLMS_QUIZ_CPT,
				// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_key'     => \BlueDolphin\Lms\META_KEY_QUIZ_QUESTION_IDS,
				// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
				'meta_value'   => array( $post_id ),
				'meta_compare' => 'REGEXP',
				'fields'       => 'ids',
			)
		);
		$quiz_ids     = ! empty( $quiz_ids ) ? $quiz_ids : array();
		$unassign_ids = array_diff( $quiz_ids, $selected );
		if ( ! empty( $unassign_ids ) ) {
			foreach ( $unassign_ids as $unassign_id ) {
				$question_ids   = get_post_meta( $unassign_id, META_KEY_QUIZ_QUESTION_IDS, true );
				$question_ids   = ! empty( $question_ids ) ? array_unique( $question_ids ) : array();
				$unassign_index = array_search( $post_id, $question_ids, true );
				if ( false !== $unassign_index ) {
					unset( $question_ids[ $unassign_index ] );
				}
				update_post_meta( $unassign_id, META_KEY_QUIZ_QUESTION_IDS, array_unique( $question_ids ) );
			}
		}
		// Question assigned.
		foreach ( $selected as $quiz_id ) {
			$question_ids   = get_post_meta( $quiz_id, META_KEY_QUIZ_QUESTION_IDS, true );
			$question_ids   = ! empty( $question_ids ) ? $question_ids : array();
			$question_ids[] = $post_id;
			update_post_meta( $quiz_id, META_KEY_QUIZ_QUESTION_IDS, array_unique( $question_ids ) );
		}
		wp_send_json(
			array(
				'status'  => true,
				'message' => __( 'Saved.', 'bluedolphin-lms' ),
			)
		);
		exit;
	}

	/**
	 * Load quiz list.
	 */
	public function load_quiz_list() {
		$nonce         = isset( $_REQUEST['_nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_nonce'] ) ) : '';
		$type          = isset( $_REQUEST['type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['type'] ) ) : 'all';
		$fetch_request = isset( $_REQUEST['fetch_quizzes'] ) ? (int) $_REQUEST['fetch_quizzes'] : 0;
		$question_id   = isset( $_REQUEST['post_id'] ) ? (int) $_REQUEST['post_id'] : 0;
		if ( wp_verify_nonce( $nonce, BDLMS_BASEFILE ) ) {
			require_once BDLMS_TEMPLATEPATH . '/admin/question/modal-popup.php';
			exit;
		}
	}
}
