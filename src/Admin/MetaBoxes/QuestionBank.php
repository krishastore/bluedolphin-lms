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

/**
 * Register metaboxes for question bank.
 */
class QuestionBank extends \BlueDolphin\Lms\Collections\PostTypes {

	/**
	 * Meta key name.
	 *
	 * @var string $meta_key
	 */
	public $meta_key = '_question_options';

	/**
	 * Question alphabets.
	 *
	 * @var string $meta_key
	 */
	public $alphabets = '';

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
		$data    = get_post_meta( $post_id, $this->meta_key, true );
		$type    = isset( $data['type'] ) ? $data['type'] : 'true_or_false';
		require_once BDLMS_TEMPLATEPATH . '/admin/question/metabox-answer-options.php';
	}

	/**
	 * Render question settings metabox.
	 */
	public function render_question_settings() {
		global $post;
		$post_id  = isset( $post->ID ) ? $post->ID : 0;
		$settings = get_post_meta( $post_id, $this->meta_key, true );
		$settings = isset( $settings['settings'] ) ? $settings['settings'] : array();
		$levels   = isset( $settings['levels'] ) ? $settings['levels'] : '';
		require_once BDLMS_TEMPLATEPATH . '/admin/question/metabox-question-settings.php';
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

		$type = isset( $_POST[ $this->meta_key ]['type'] ) ? sanitize_text_field( wp_unslash( $_POST[ $this->meta_key ]['type'] ) ) : '';
		// Quick edit action.
		if ( isset( $_POST['action'] ) && 'inline-save' === $_POST['action'] ) {
			$post_id   = isset( $_POST['post_ID'] ) ? (int) $_POST['post_ID'] : $post_id;
			$post_data = get_post_meta( $post_id, $this->meta_key, true );
		}

		if ( isset( $_POST[ $this->meta_key ][ $type ] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$post_data[ $type ] = map_deep( $_POST[ $this->meta_key ][ $type ], 'sanitize_text_field' );
		}
		if ( isset( $_POST[ $this->meta_key ][ $type . '_answers' ] ) ) {
			if ( is_array( $_POST[ $this->meta_key ][ $type . '_answers' ] ) ) {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
				$current_answer = map_deep( $_POST[ $this->meta_key ][ $type . '_answers' ], 'intval' );
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
				$current_answer                  = (int) $_POST[ $this->meta_key ][ $type . '_answers' ];
				$post_data[ $type . '_answers' ] = isset( $post_data[ $type ][ $current_answer ] ) ? wp_hash( $post_data[ $type ][ $current_answer ] ) : wp_hash( $current_answer );
			}
		}

		if ( isset( $_POST[ $this->meta_key ]['status'] ) ) {
			$post_data['status'] = true;
		}

		if ( isset( $_POST[ $this->meta_key ]['mandatory_answers'] ) ) {
			$post_data['mandatory_answers'] = sanitize_text_field( wp_unslash( $_POST[ $this->meta_key ]['mandatory_answers'] ) );
		}

		if ( isset( $_POST[ $this->meta_key ]['optional_answers'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$post_data['optional_answers'] = map_deep( $_POST[ $this->meta_key ]['optional_answers'], 'sanitize_text_field' );
		}

		if ( isset( $_POST[ $this->meta_key ]['settings']['points'] ) ) {
			$post_data['settings']['points'] = (int) $_POST[ $this->meta_key ]['settings']['points'];
		}
		if ( isset( $_POST[ $this->meta_key ]['settings']['hint'] ) ) {
			$post_data['settings']['hint'] = sanitize_textarea_field( wp_unslash( $_POST[ $this->meta_key ]['settings']['hint'] ) );
		}
		if ( isset( $_POST[ $this->meta_key ]['settings']['explanation'] ) ) {
			$post_data['settings']['explanation'] = sanitize_textarea_field( wp_unslash( $_POST[ $this->meta_key ]['settings']['explanation'] ) );
		}

		if ( isset( $_POST[ $this->meta_key ]['settings']['levels'] ) ) {
			$post_data['settings']['levels'] = sanitize_textarea_field( wp_unslash( $_POST[ $this->meta_key ]['settings']['levels'] ) );
		}

		if ( ! empty( $type ) ) {
			$post_data['type'] = $type;
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
		$date = $columns['date'];
		unset( $columns['date'] );

		$topic_key = 'taxonomy-bdlms_quesion_topics';
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
		$data = get_post_meta( $post_id, $this->meta_key, true );
		switch ( $column ) {
			case 'quiz':
				echo ! empty( $data['quiz'] ) ? esc_html( 'Quiz Name' ) : '—';
				break;

			case 'type':
				echo ! empty( $data['type'] ) ? esc_html( ucwords( str_replace( '_', ' ', $data['type'] ) ) ) : '—';
				break;

			case 'levels':
				echo ! empty( $data['settings']['levels'] ) ? esc_html( ucwords( str_replace( '_', ' ', $data['settings']['levels'] ) ) ) : '—';
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
		?>
		<fieldset class="inline-edit-col-right inline-edit-levels">
		<div class="inline-edit-col inline-edit-<?php echo esc_attr( $column_name ); ?>">
		<label class="inline-edit-group">
		<?php
		switch ( $column_name ) {
			case 'levels':
				?>
			<span class="title"><?php esc_html_e( 'Difficulty Level', 'bluedolphin-lms' ); ?></span>
				<select name="<?php echo esc_attr( $this->meta_key ); ?>[settings][levels]">
					<?php
					foreach ( \BlueDolphin\Lms\question_levels() as $key => $level ) {
						?>
							<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $level ); ?></option>
						<?php
					}
					?>
				</select>
				<?php
				break;
		}
		?>
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
		if ( BDLMS_QUESTION_CPT === $post->post_type ) {
			$question_data = get_post_meta( $post->ID, $this->meta_key, true );
			$type          = isset( $question_data['type'] ) ? $question_data['type'] : 'true_or_false';
			$answers       = isset( $question_data[ $type . '_answers' ] ) ? $question_data[ $type . '_answers' ] : '';
			$answers_list  = isset( $question_data[ $type ] ) ? $question_data[ $type ] : array();
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

			$data = array(
				'title' => $post->post_title,
				'type'  => isset( $question_data['type'] ) ? $question_data['type'] : '',
				'marks' => isset( $question_data['settings']['points'] ) ? $question_data['settings']['points'] : '',
			);
			if ( 'fill_blank' !== $type ) {
				$data[ $type ] = $answers_list;
			}
			if ( 'fill_blank' === $type ) {
				$data['mandatory'] = isset( $question_data['mandatory_answers'] ) ? $question_data['mandatory_answers'] : '';
				$data['optional']  = isset( $question_data['optional_answers'] ) ? $question_data['optional_answers'] : '';
			}
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
}
