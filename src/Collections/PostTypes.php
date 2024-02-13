<?php
/**
 * The file that register the post types.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BlueDolphin\Lms
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
	 * Init hooks.
	 */
	public function init() {
		$this->register();
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
}
