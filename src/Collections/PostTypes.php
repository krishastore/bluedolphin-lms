<?php
/**
 * The file that register the post types.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BlueDolphin\Lms
 *
 * phpcs:disable WordPress.NamingConventions.ValidHookName.UseUnderscores
 */

namespace BlueDolphin\Lms\Collections;

/**
 * Register post types.
 */
class PostTypes implements \BlueDolphin\Lms\Interfaces\PostTypes {

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
		add_filter( 'disable_months_dropdown', array( $this, 'disable_months_dropdown' ), 10, 2 );
		add_action( 'load-post.php', array( $this, 'handle_admin_screen' ) );
		add_action( 'load-post-new.php', array( $this, 'handle_admin_screen' ) );
		add_action( 'load-edit.php', array( $this, 'handle_admin_screen' ) );
		add_action( 'restrict_manage_posts', array( $this, 'custom_filter_dropdown' ) );
		add_action( 'post_submitbox_start', array( $this, 'post_submitbox_start' ) );
		add_action( 'admin_action_bdlms_clone', array( $this, 'clone_post' ) );
	}

	/**
	 * Register post types.
	 */
	public function register() {
		$this->post_type = apply_filters(
			'bluedolphin/collections/post-types',
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
		if ( $current_screen && isset( $current_screen->id ) ) {
			$screen_id = str_replace( 'edit-', '', $current_screen->id );
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
		if ( $screen && in_array( $screen->post_type, array( \BlueDolphin\Lms\BDLMS_QUESTION_CPT ), true ) ) {
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

			$taxonomy = \BlueDolphin\Lms\BDLMS_QUESTION_TAXONOMY_TAG;
			$args     = array(
				'show_option_none'  => __( 'All Question', 'textdomain' ),
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
		if ( in_array( $post_type, array( \BlueDolphin\Lms\BDLMS_QUESTION_CPT ), true ) ) {
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
		if ( ! in_array( $post->post_type, array( \BlueDolphin\Lms\BDLMS_QUESTION_CPT ), true ) ) {
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
	 */
	public function clone_post() {
		global $wpdb;
		if ( ! isset( $_GET['bdlms_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['bdlms_nonce'] ) ), BDLMS_BASEFILE ) ) {
			return;
		}
		$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
		$post    = get_post( $post_id );

		if ( ! $post ) {
			return;
		}
		// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
		$new_title = wp_sprintf( esc_html__( 'Copy of %1$s', 'profile-maker' ), $post->post_title );
		$args      = array(
			'comment_status' => $post->comment_status,
			'ping_status'    => $post->ping_status,
			'post_author'    => $post->post_author,
			'post_content'   => $post->post_content,
			'post_excerpt'   => $post->post_excerpt,
			'post_name'      => sanitize_title( $new_title ),
			'post_parent'    => $post->post_parent,
			'post_password'  => $post->post_password,
			'post_status'    => 'publish',
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
}
