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
		add_action( 'load-post.php', array( $this, 'handle_admin_screen' ) );
		add_action( 'load-post-new.php', array( $this, 'handle_admin_screen' ) );
		add_action( 'load-edit.php', array( $this, 'handle_admin_screen' ) );
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
}
