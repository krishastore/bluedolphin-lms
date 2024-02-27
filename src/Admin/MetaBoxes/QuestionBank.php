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

use const BlueDolphin\Lms\BDLMS_QUESTION_CPT;

/**
 * Register metaboxes for question bank.
 */
class QuestionBank extends \BlueDolphin\Lms\Collections\PostTypes {

	/**
	 * Meta key name.
	 *
	 * @var string $meta_key
	 */
	public $meta_key = '_bdlms_questions';

	/**
	 * Class construct.
	 */
	public function __construct() {
		$this->set_metaboxes( $this->meta_boxes_list() );

		// Hooks.
		add_action( 'save_post_' . BDLMS_QUESTION_CPT, array( $this, 'save_metadata' ) );
		add_filter( 'manage_edit-' . BDLMS_QUESTION_CPT . '_sortable_columns', array( $this, 'sortable_columns' ) );
		add_filter( 'manage_' . BDLMS_QUESTION_CPT . '_posts_columns', array( $this, 'add_new_table_columns' ) );
		add_action( 'manage_' . BDLMS_QUESTION_CPT . '_posts_custom_column', array( $this, 'manage_custom_column' ), 10, 2 );
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
		$answers = get_post_meta( $post_id, $this->meta_key . '_answers', true );
		?>
		<?php wp_nonce_field( BDLMS_BASEFILE, 'bdlms_nonce', false ); ?>
		<label for="answers_field">
			<?php esc_html_e( 'Answers: ', 'bluedolphin-lms' ); ?>
		</label>
		<input type="radio" value="true" name="<?php echo esc_attr( $this->meta_key ); ?>[answers]"<?php checked( $answers, 'true' ); ?>> True
		<input type="radio" value="false" name="<?php echo esc_attr( $this->meta_key ); ?>[answers]" <?php checked( $answers, 'false' ); ?>> False
		<?php
	}

	/**
	 * Render question settings metabox.
	 */
	public function render_question_settings() {
		global $post;
		$post_id  = isset( $post->ID ) ? $post->ID : 0;
		$settings = get_post_meta( $post_id, $this->meta_key . '_settings', true );
		?>
		<label for="points_field">
			<?php esc_html_e( 'Points: ', 'bluedolphin-lms' ); ?>
		</label>
		<input type="number" value="1" name="<?php echo esc_attr( $this->meta_key ); ?>[settings][points]" value="<?php echo isset( $settings['points'] ) ? (int) $settings['points'] : 1; ?>" step="1">

		<br /><br />
		<label for="hint_field">
			<?php esc_html_e( 'Hint: ', 'bluedolphin-lms' ); ?>
		</label>
		<textarea name="<?php echo esc_attr( $this->meta_key ); ?>[settings][hint]"><?php echo isset( $settings['hint'] ) ? esc_textarea( $settings['hint'] ) : ''; ?></textarea>
		<p><?php esc_html_e( 'The instructions for the user to select the right answer. The text will be shown when users click the \'Hint\' button.', 'bluedolphin-lms' ); ?></p>

		<br /><br />
		<label for="explanation_field">
			<?php esc_html_e( 'Hint: ', 'bluedolphin-lms' ); ?>
		</label>
		<textarea name="<?php echo esc_attr( $this->meta_key ); ?>[settings][explanation]"><?php echo isset( $settings['explanation'] ) ? esc_textarea( $settings['explanation'] ) : ''; ?></textarea>
		<p><?php esc_html_e( 'The explanation will be displayed when students click the "Check Answer" button.', 'bluedolphin-lms' ); ?></p>
		<?php
	}

	/**
	 * Save post meta.
	 */
	public function save_metadata() {
		global $post;
		$post_id = isset( $post->ID ) ? $post->ID : 0;

		if ( isset( $_POST['bdlms_nonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bdlms_nonce'] ) ), BDLMS_BASEFILE ) ) {
			return;
		}
		$post_data = array();
		if ( isset( $_POST[ $this->meta_key ]['answers'] ) ) {
			$post_data['answers'] = sanitize_text_field( wp_unslash( $_POST[ $this->meta_key ]['answers'] ) );
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

		foreach ( $post_data as $key => $data ) {
			$key = $this->meta_key . '_' . $key;
			update_post_meta( $post_id, $key, $data );
		}
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

		$columns['author'] = __( 'Author', 'bluedolphin-lms' );
		$columns['quiz']   = __( 'Quiz', 'bluedolphin-lms' );
		$columns['type']   = __( 'Type', 'bluedolphin-lms' );
		$columns['date']   = $date;
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
			case 'quiz':
				echo esc_html( 'Quiz Name' );
				break;

			case 'type':
				echo esc_html( 'Quiz Type' );
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
		$columns['quiz']   = 'quiz';
		$columns['author'] = 'author';
		return $columns;
	}
}
