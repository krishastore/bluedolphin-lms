<?php
/**
 * The file that register metabox for results.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BlueDolphin\Lms
 *
 * phpcs:disable WordPress.NamingConventions.ValidHookName.UseUnderscores
 */

namespace BlueDolphin\Lms\Admin\MetaBoxes;

use BlueDolphin\Lms\ErrorLog as EL;
use function BlueDolphin\Lms\column_post_author as postAuthor;
use const BlueDolphin\Lms\BDLMS_RESULTS_CPT;

/**
 * Register metaboxes for results.
 */
class Results extends \BlueDolphin\Lms\Collections\PostTypes {
	/**
	 * Class construct.
	 */
	public function __construct() {
		$this->set_metaboxes( $this->meta_boxes_list() );
		add_filter( 'manage_' . BDLMS_RESULTS_CPT . '_posts_columns', array( $this, 'add_new_table_columns' ) );
		add_filter( 'post_row_actions', array( $this, 'quick_actions' ), 10, 2 );
		add_action( 'manage_' . BDLMS_RESULTS_CPT . '_posts_custom_column', array( $this, 'manage_custom_column' ), 10, 2 );
		add_action( 'admin_footer', array( $this, 'add_footer_style' ) );
	}

	/**
	 * Meta boxes list.
	 *
	 * @return array
	 */
	private function meta_boxes_list() {
		$list = apply_filters(
			'bluedolphin/results/meta_boxes',
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
		$columns['grade']       = __( 'Grade', 'bluedolphin-lms' );
		$columns['accuracy']    = __( 'Accuracy', 'bluedolphin-lms' );
		$columns['time']        = __( 'Time', 'bluedolphin-lms' );
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
			case 'grade':
				$grade_percentage = get_post_meta( $post_id, 'grade_percentage', true );
				echo ! empty( $grade_percentage ) ? esc_html( $grade_percentage ) : '';
				break;
			case 'accuracy':
				$accuracy = get_post_meta( $post_id, 'accuracy', true );
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
	 * Add admin footer style.
	 */
	public function add_footer_style() {
		global $post;
		if ( $post && BDLMS_RESULTS_CPT === $post->post_type ) {
			?>
			<style>
				#misc-publishing-actions .misc-pub-post-status,
				#misc-publishing-actions .misc-pub-visibility,
				#misc-publishing-actions .misc-pub-curtime a,
				#major-publishing-actions #publishing-action
				{
					display: none !important;
				}
				.column-post_author .post-author {
					display: flex;
					align-items: center;
					gap: 8px;
				}
				.column-post_author .post-author img {
					border-radius: 100%;
					flex-shrink: 0;
				}
			</style>
			<?php
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
			$newtext         = __( 'View More Details', 'bluedolphin-lms' );
			$actions['edit'] = preg_replace( '/(<a.*?>).*?(<\/a>)/', '$1' . $newtext . '$2', $actions['edit'] );
		}
		return $actions;
	}
}
