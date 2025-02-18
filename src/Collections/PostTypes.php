<?php
/**
 * The file that register the post types.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BD\Lms
 *
 * phpcs:disable WordPress.NamingConventions.ValidHookName.UseUnderscores
 */

namespace BD\Lms\Collections;

/**
 * Register post types.
 */
class PostTypes implements \BD\Lms\Interfaces\PostTypes {

	/**
	 * Post type list.
	 *
	 * @var array $post_type
	 */
	private $post_type = array();

	/**
	 * Meta boxes list.
	 *
	 * @var array $meta_boxes
	 */
	public $meta_boxes = array();

	/**
	 * Init hooks.
	 */
	public function init() {
		$this->register();
		// Hooks.
		add_filter( 'post_row_actions', array( $this, 'quick_actions' ), 10, 2 );
		add_filter( 'disable_months_dropdown', array( $this, 'disable_months_dropdown' ), 10, 2 );
		add_filter( 'quick_edit_show_taxonomy', array( $this, 'quick_edit_show_taxonomy' ), 10, 2 );
		add_action( 'load-post.php', array( $this, 'handle_admin_screen' ) );
		add_action( 'load-post-new.php', array( $this, 'handle_admin_screen' ) );
		add_action( 'load-edit.php', array( $this, 'handle_admin_screen' ) );
		add_action( 'load-edit-tags.php', array( $this, 'handle_admin_screen' ) );
		add_action( 'restrict_manage_posts', array( $this, 'custom_filter_dropdown' ) );
		add_action( 'post_submitbox_start', array( $this, 'post_submitbox_start' ) );
		add_action( 'admin_action_bdlms_clone', array( $this, 'clone_post' ) );
	}

	/**
	 * Register post types.
	 */
	public function register() {
		$this->post_type = apply_filters(
			'bdlms/collections/post-types',
			glob( plugin_dir_path( __FILE__ ) . '/post-types/*.php' )
		);
		if ( ! empty( $this->post_type ) ) {
			foreach ( $this->post_type as $path ) {
				if ( is_readable( $path ) ) {
					require $path;
				}
			}
		}
	}

	/**
	 * Set metaboxes.
	 *
	 * @param array $metabox_list List of metaboxes.
	 * @return void
	 */
	public function set_metaboxes( $metabox_list ) {
		$this->meta_boxes = array_merge( $this->meta_boxes, $metabox_list );
	}

	/**
	 * Get metaboxes list.
	 *
	 * @return array
	 */
	public function get_metaboxes() {
		return $this->meta_boxes;
	}

	/**
	 * Register meta boxes callback.
	 */
	public function register_boxes() {
		$metaboxes = $this->get_metaboxes();
		if ( empty( $metaboxes ) ) {
			return;
		}
		foreach ( $metaboxes as $metabox ) {
			$metabox = wp_parse_args(
				$metabox,
				array(
					'id'            => '',
					'title'         => '',
					'callback'      => null,
					'screen'        => null,
					'context'       => 'advanced',
					'priority'      => 'default',
					'callback_args' => null,
				)
			);
			list( $id, $title, $callback, $screen, $context, $priority, $callback_args ) = array_values( $metabox );
			\add_meta_box( $id, $title, $callback, $screen, $context, $priority, $callback_args );
		}
	}

	/**
	 * Handle admin screen.
	 */
	public function handle_admin_screen() {
		global $current_screen;
		$category_screen = array( 'bdlms_course_category', 'bdlms_course_tag' );

		if ( $current_screen && isset( $current_screen->id ) ) {
			$screen_id = str_replace( 'edit-', '', $current_screen->id );
			$screen_id = in_array( $screen_id, $category_screen, true ) ? 'bdlms_course' : $screen_id;
			wp_enqueue_script( $screen_id );
			wp_enqueue_style( $screen_id );
		}
	}

	/**
	 * Add custom filter dropdown.
	 */
	public function custom_filter_dropdown() {
		global $post_type;
		$screen = get_current_screen();
		if ( $screen && in_array( $screen->post_type, array( \BD\Lms\BDLMS_QUESTION_CPT, \BD\Lms\BDLMS_COURSE_CPT ), true ) ) {
			$query_args = array(
				'show_option_all'  => __( 'Search by user', 'bluedolphin-lms' ),
				'orderby'          => 'display_name',
				'order'            => 'ASC',
				'name'             => 'author',
				'include_selected' => true,
			);
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( isset( $_GET['author'] ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$query_args['selected'] = (int) $_GET['author'];
			}
			wp_dropdown_users( $query_args );

			if ( \BD\Lms\BDLMS_QUESTION_CPT === $screen->post_type ) {
				$taxonomy = \BD\Lms\BDLMS_QUESTION_TAXONOMY_TAG;
				$args     = array(
					'show_option_none'  => __( 'All Question', 'bluedolphin-lms' ),
					'show_count'        => 0,
					'orderby'           => 'name',
					'taxonomy'          => $taxonomy,
					'name'              => $taxonomy,
					'value_field'       => 'slug',
					'option_none_value' => '',
				);
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				if ( isset( $_GET[ $taxonomy ] ) ) {
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$args['selected'] = sanitize_text_field( wp_unslash( $_GET[ $taxonomy ] ) );
				}
				wp_dropdown_categories( $args );
			}
		}

		if ( $screen && in_array( $screen->post_type, array( \BD\Lms\BDLMS_QUIZ_CPT ), true ) ) {
			$taxonomy = \BD\Lms\BDLMS_QUIZ_TAXONOMY_LEVEL_1;
			$args     = array(
				'show_option_none'  => __( 'All Quiz', 'bluedolphin-lms' ),
				'show_count'        => 0,
				'orderby'           => 'name',
				'taxonomy'          => $taxonomy,
				'name'              => $taxonomy,
				'value_field'       => 'slug',
				'option_none_value' => '',
			);
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( isset( $_GET[ $taxonomy ] ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$args['selected'] = sanitize_text_field( wp_unslash( $_GET[ $taxonomy ] ) );
			}
			wp_dropdown_categories( $args );
		}
	}

	/**
	 * Filters whether to remove the 'Months' drop-down from the post list table.
	 *
	 * @since 1.0.0
	 *
	 * @param bool   $disable   Whether to disable the drop-down. Default false.
	 * @param string $post_type The post type.
	 */
	public function disable_months_dropdown( $disable, $post_type ) {
		if ( in_array( $post_type, array( \BD\Lms\BDLMS_QUESTION_CPT, \BD\Lms\BDLMS_QUIZ_CPT ), true ) ) {
			return true;
		}
		return $disable;
	}

	/**
	 * Start submit action box.
	 *
	 * @param object $post Post object.
	 */
	public function post_submitbox_start( $post ) {
		if ( ! in_array( $post->post_type, array( \BD\Lms\BDLMS_QUESTION_CPT, \BD\Lms\BDLMS_QUIZ_CPT, \BD\Lms\BDLMS_LESSON_CPT, \BD\Lms\BDLMS_COURSE_CPT ), true ) ) {
			return;
		}
		?>
		<div id="clone-action">
		<?php
		if ( current_user_can( 'edit_posts', $post->ID ) ) {
			$url = wp_nonce_url(
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
			?>
			<a class="button" href="<?php echo esc_url( $url ); ?>"><?php esc_attr_e( 'Clone', 'bluedolphin-lms' ); ?></a>
			<?php
		}
		?>
		</div>
		<?php
	}

	/**
	 * Clone post.
	 *
	 * @param bool $duplicate_only Duplicate only.
	 */
	public function clone_post( $duplicate_only = false ) {
		global $wpdb;
		if ( ! isset( $_REQUEST['bdlms_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['bdlms_nonce'] ) ), BDLMS_BASEFILE ) ) {
			return;
		}
		$post_id     = isset( $_REQUEST['post'] ) ? absint( $_REQUEST['post'] ) : 0;
		$post_status = isset( $_REQUEST['post_status'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['post_status'] ) ) : 'publish';
		$post        = get_post( $post_id );

		if ( ! $post ) {
			return;
		}
		// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
		$new_title = $duplicate_only ? $post->post_title : wp_sprintf( esc_html__( 'Copy of %1$s', 'bluedolphin-lms' ), $post->post_title );
		$args      = array(
			'comment_status' => $post->comment_status,
			'ping_status'    => $post->ping_status,
			'post_author'    => (int) $post->post_author,
			'post_content'   => $post->post_content,
			'post_excerpt'   => $post->post_excerpt,
			'post_name'      => sanitize_title( $new_title ),
			'post_parent'    => $post->post_parent,
			'post_password'  => $post->post_password,
			'post_status'    => $post_status,
			'post_title'     => $new_title,
			'post_type'      => $post->post_type,
			'to_ping'        => $post->to_ping,
			'menu_order'     => $post->menu_order,
		);
		// Insert the post by wp_insert_post() function.
		$new_post_id = wp_insert_post( $args );

		/*
		 * Get all current post terms ad set them to the new post draft
		 */
		$taxonomies = get_object_taxonomies( get_post_type( $post ) );
		if ( $taxonomies ) {
			foreach ( $taxonomies as $taxonomy ) {
				$post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
				wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
			}
		}

		// Duplicate all post meta.
		$post_meta = get_post_meta( $post_id );
		if ( $post_meta ) {
			foreach ( $post_meta as $meta_key => $meta_values ) {
				foreach ( $meta_values as $meta_value ) {
					if ( is_serialized( $meta_value ) ) {
						$meta_value = maybe_unserialize( $meta_value );
					}
					add_post_meta( $new_post_id, $meta_key, $meta_value );
				}
			}
		}
		if ( $duplicate_only ) {
			return array(
				'post_id' => $new_post_id,
				'action'  => 'duplicate',
			);
		}
		wp_safe_redirect(
			add_query_arg(
				array(
					'post'   => $new_post_id,
					'action' => 'edit',
				),
				admin_url( 'post.php' )
			)
		);
		exit;
	}

	/**
	 * Filters whether the current taxonomy should be shown in the Quick Edit panel.
	 *
	 * @since 1.0.0
	 *
	 * @param bool   $show Whether to show the current taxonomy in Quick Edit.
	 * @param string $taxonomy_name      Taxonomy name.
	 *
	 * @return bool
	 */
	public function quick_edit_show_taxonomy( $show, $taxonomy_name ) {
		if ( ! wp_doing_ajax() && in_array( $taxonomy_name, array( \BD\Lms\BDLMS_QUIZ_TAXONOMY_LEVEL_1, \BD\Lms\BDLMS_QUIZ_TAXONOMY_LEVEL_2 ), true ) ) {
			return false;
		}
		return $show;
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
		if ( in_array( $post->post_type, array( \BD\Lms\BDLMS_QUIZ_CPT, \BD\Lms\BDLMS_LESSON_CPT, \BD\Lms\BDLMS_COURSE_CPT ), true ) ) {
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
			$actions['clone_post'] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Clone', 'bluedolphin-lms' ) . ' </a>';
		}
		return $actions;
	}
}
