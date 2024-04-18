<?php
/**
 * The file that register metabox for course.
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
use const BlueDolphin\Lms\BDLMS_COURSE_CPT;
use const BlueDolphin\Lms\BDLMS_COURSE_CATEGORY_TAX;
use const BlueDolphin\Lms\BDLMS_COURSE_TAXONOMY_TAG;

/**
 * Register metaboxes for course.
 */
class Course extends \BlueDolphin\Lms\Collections\PostTypes {

	/**
	 * Meta key prefix.
	 *
	 * @var string $meta_key_prefix
	 */
	public $meta_key_prefix = \BlueDolphin\Lms\META_KEY_COURSE_PREFIX;

	/**
	 * Class construct.
	 */
	public function __construct() {
		$this->set_metaboxes( $this->meta_boxes_list() );

		// Hooks.
		add_action( 'save_post_' . BDLMS_COURSE_CPT, array( $this, 'save_metadata' ) );
		add_filter( 'manage_edit-' . BDLMS_COURSE_CPT . '_sortable_columns', array( $this, 'sortable_columns' ) );
		add_filter( 'manage_' . BDLMS_COURSE_CPT . '_posts_columns', array( $this, 'add_new_table_columns' ) );
		add_action( 'manage_' . BDLMS_COURSE_CPT . '_posts_custom_column', array( $this, 'manage_custom_column' ), 10, 2 );
		add_action( 'all_admin_notices', array( $this, 'add_header_tab' ) );
	}

	/**
	 * Meta boxes list.
	 *
	 * @return array
	 */
	private function meta_boxes_list() {
		$list = apply_filters(
			'bluedolphin/course/meta_boxes',
			array(
				array(
					'id'       => 'curriculum',
					'title'    => __( 'Curriculum', 'bluedolphin-lms' ),
					'callback' => array( $this, 'render_curriculum' ),
				),
				array(
					'id'       => 'course-settings',
					'title'    => __( 'Course Settings', 'bluedolphin-lms' ),
					'callback' => array( $this, 'render_course_settings' ),
				),
			)
		);
		return $list;
	}

	/**
	 * Render curriculum metabox.
	 */
	public function render_curriculum() {
		global $post;
		$post_id = isset( $post->ID ) ? $post->ID : 0;
		require_once BDLMS_TEMPLATEPATH . '/admin/course/curriculum.php';
	}

	/**
	 * Render course settings metabox.
	 */
	public function render_course_settings() {
		global $post;
		$post_id = isset( $post->ID ) ? $post->ID : 0;
		require_once BDLMS_TEMPLATEPATH . '/admin/course/course-settings.php';
	}

	/**
	 * Save post meta.
	 */
	public function save_metadata() {
		// Save meta data here...
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

		$category_key = 'taxonomy-' . BDLMS_COURSE_CATEGORY_TAX;
		$tag_key      = 'taxonomy-' . BDLMS_COURSE_TAXONOMY_TAG;
		$category     = $columns[ $category_key ];
		$checkbox     = $columns['cb'];
		unset( $columns[ $category_key ] );
		unset( $columns[ $tag_key ] );
		unset( $columns['author'] );
		unset( $columns['comments'] );
		unset( $columns['cb'] );
		$columns['post_author']   = __( 'Author', 'bluedolphin-lms' );
		$columns['content']       = __( 'Content', 'bluedolphin-lms' );
		$columns['employees']     = __( 'Employees', 'bluedolphin-lms' );
		$columns['category_list'] = __( 'Categories', 'bluedolphin-lms' );
		$columns['comments_list'] = __( 'Comments', 'bluedolphin-lms' );
		$columns['date']          = $date;

		$columns = array_merge(
			array(
				'cb'        => $checkbox,
				'thumbnail' => __( 'Thumbnail', 'bluedolphin-lms' ),
			),
			$columns
		);
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
			case 'thumbnail':
				echo '<div class="column-thumb-img">';
				if ( has_post_thumbnail( $post_id ) ) {
					echo '<img src="' . esc_url( get_the_post_thumbnail_url( $post_id ) ) . '" class="course-img" width="80" height="80">';
				}
				echo '</div>';
				break;
			case 'post_author':
				echo wp_kses_post( postAuthor( $post_id ) );
				break;
			case 'content':
				echo '—';
				break;
			case 'employees':
				echo '<span>0</span>';
				break;
			case 'category_list':
				$categories = get_the_terms( $post_id, BDLMS_COURSE_CATEGORY_TAX );
				$categories = is_array( $categories ) ? $categories : array();
				$categories = array_map(
					function ( $category ) {
						$filter_url = add_query_arg(
							array(
								'post_type'               => BDLMS_COURSE_CPT,
								BDLMS_COURSE_CATEGORY_TAX => $category->slug,
							),
							admin_url( 'edit.php' )
						);
						return '<a href="' . esc_url( $filter_url ) . '">' . $category->name . '</a>';
					},
					$categories
				);
				if ( ! empty( $categories ) ) {
					echo wp_kses(
						implode( ', ', array_filter( $categories ) ),
						array(
							'a' => array(
								'href'           => array(),
								'title'          => array(),
								'class'          => array(),
								'target'         => array(),
								'data-course_id' => array(),
							),
						)
					);
				} else {
					echo '—';
				}
				break;
			case 'comments_list':
				echo '<a href="' . esc_url( add_query_arg( 'p', $post_id ), admin_url( 'edit-comments.php' ) ) . '">' . esc_html__( 'View', 'bluedolphin-lms' ) . '</a>';
				break;
			default:
				break;
		}
	}

	/**
	 * Add header tab.
	 */
	public function add_header_tab() {
		global $current_screen;
		if ( ! $current_screen ) {
			return;
		}
		$id = $current_screen->id;
		if ( str_contains( $id, 'edit-bdlms_course' ) ) {
			$id = str_replace( 'edit-', '', $id );
			?>
			<style>
				.bdlms-course-wrap .nav-tab-wrapper .nav-tab.active {background: #fff;}
			</style>
			<div class="bdlms-course-wrap">
				<nav class="nav-tab-wrapper">
					<a href="<?php echo esc_url( add_query_arg( 'post_type', BDLMS_COURSE_CPT, admin_url( 'edit.php' ) ) ); ?>" class="nav-tab <?php echo BDLMS_COURSE_CPT === $id ? esc_attr( 'active' ) : ''; ?>"><?php esc_html_e( 'Courses', 'bluedolphin-lms' ); ?></a>
					<a href="<?php echo esc_url( add_query_arg( 'taxonomy', BDLMS_COURSE_CATEGORY_TAX, admin_url( 'edit-tags.php' ) ) ); ?>" class="nav-tab <?php echo BDLMS_COURSE_CATEGORY_TAX === $id ? esc_attr( 'active' ) : ''; ?>"><?php esc_html_e( 'Categories', 'bluedolphin-lms' ); ?></a>
					<a href="<?php echo esc_url( add_query_arg( 'taxonomy', BDLMS_COURSE_TAXONOMY_TAG, admin_url( 'edit-tags.php' ) ) ); ?>" class="nav-tab <?php echo BDLMS_COURSE_TAXONOMY_TAG === $id ? esc_attr( 'active' ) : ''; ?>"><?php esc_html_e( 'Tags', 'bluedolphin-lms' ); ?></a>
				</nav>
			</div>
			<?php
		}
	}
}
