<?php
/**
 * The file that register metabox for results.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BD\Lms
 *
 * phpcs:disable WordPress.NamingConventions.ValidHookName.UseUnderscores
 */

namespace BD\Lms\Admin\MetaBoxes;

use BD\Lms\ErrorLog as EL;
use function BD\Lms\column_post_author as postAuthor;
use const BD\Lms\BDLMS_RESULTS_CPT;

/**
 * Register metaboxes for results.
 */
class Results extends \BD\Lms\Collections\PostTypes {
	/**
	 * Class construct.
	 */
	public function __construct() {
		$this->set_metaboxes( $this->meta_boxes_list() );
		add_filter( 'manage_' . BDLMS_RESULTS_CPT . '_posts_columns', array( $this, 'add_new_table_columns' ) );
		add_filter( 'post_row_actions', array( $this, 'quick_actions' ), 10, 2 );
		add_action( 'manage_' . BDLMS_RESULTS_CPT . '_posts_custom_column', array( $this, 'manage_custom_column' ), 10, 2 );
	}

	/**
	 * Meta boxes list.
	 *
	 * @return array
	 */
	private function meta_boxes_list() {
		$list = apply_filters(
			'bdlms/results/meta_boxes',
			array(
				array(
					'id'       => 'result-view',
					'title'    => __( 'Quiz result', 'bluedolphin-lms' ),
					'callback' => array( $this, 'render_results' ),
				),
			)
		);
		return $list;
	}

	/**
	 * Render view results metabox.
	 */
	public function render_results() {
		global $post;
		$post_id          = isset( $post->ID ) ? $post->ID : 0;
		$grade_percentage = get_post_meta( $post_id, 'grade_percentage', true );
		$accuracy         = get_post_meta( $post_id, 'accuracy', true );
		$time_str         = get_post_meta( $post_id, 'time_str', true );
		$quiz_id          = get_post_meta( $post_id, 'quiz_id', true );
		$course_id        = get_post_meta( $post_id, 'course_id', true );
		?>
		<table class="wp-list-table widefat fixed striped posts" style="margin-top: 15px;">
			<tbody>
				<tr>
					<th><?php esc_html_e( 'Course', 'bluedolphin-lms' ); ?></th>
					<td><a href="<?php echo esc_url( get_edit_post_link( $course_id ) ); ?>"><?php echo esc_html( get_the_title( $course_id ) ); ?></a></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Quiz', 'bluedolphin-lms' ); ?></th>
					<td><a href="<?php echo esc_url( get_edit_post_link( $quiz_id ) ); ?>"><?php echo esc_html( get_the_title( $quiz_id ) ); ?></a></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Grade', 'bluedolphin-lms' ); ?></th>
					<td><?php echo esc_html( $grade_percentage ); ?></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Accuracy', 'bluedolphin-lms' ); ?></th>
					<td><?php echo esc_html( $accuracy ); ?></td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Time', 'bluedolphin-lms' ); ?></th>
					<td><?php echo esc_html( $time_str ); ?></td>
				</tr>
			</tbody>
		</table>
		<?php
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
		$columns['post_author'] = __( 'Employee', 'bluedolphin-lms' );
		$columns['grade']       = __( 'Corect answers', 'bluedolphin-lms' );
		$columns['accuracy']    = __( 'Attempted Questions', 'bluedolphin-lms' );
		$columns['time']        = __( 'Time taken', 'bluedolphin-lms' );
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
				echo wp_kses_post( (string) postAuthor( $post_id ) );
				break;
			case 'grade':
				$grade_percentage = get_post_meta( $post_id, 'correct_answers', true );
				echo ! empty( $grade_percentage ) ? count( $grade_percentage ) : 0;
				break;
			case 'accuracy':
				$accuracy = get_post_meta( $post_id, 'attempted_questions', true );
				echo ! empty( $accuracy ) ? esc_html( $accuracy ) : '';
				break;
			case 'time':
				$time_str = get_post_meta( $post_id, 'time_str', true );
				echo ! empty( $time_str ) ? esc_html( $time_str ) : '';
				break;

			default:
				break;
		}
	}

	/**
	 * Filters the array of row action links on the Posts list table.
	 *
	 * @param array  $actions Row action.
	 * @param object $post Post object.
	 * @return array
	 */
	public function quick_actions( $actions, $post ) {
		if ( BDLMS_RESULTS_CPT === $post->post_type ) {
			unset( $actions['inline hide-if-no-js'] );
			$newtext = __( 'View More Details', 'bluedolphin-lms' );
			if ( isset( $actions['edit'] ) ) {
				$actions['edit'] = preg_replace( '/(<a.*?>).*?(<\/a>)/', '$1' . $newtext . '$2', $actions['edit'] );
			}
		}
		return $actions;
	}
}
