<?php
/**
 * Declare the interface for `BlueDolphin\Lms\PostTypes` class.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BlueDolphin\Lms
 */

namespace BlueDolphin\Lms\Interfaces;

interface PostTypes {

	/**
	 * Init hooks.
	 */
	public function init();

	/**
	 * Register post types.
	 */
	public function register();

	/**
	 * Set metaboxes.
	 *
	 * @param array $metabox_list List of metaboxes.
	 * @return void
	 */
	public function set_metaboxes( $metabox_list );

	/**
	 * Get metaboxes list.
	 */
	public function get_metaboxes();

	/**
	 * Register meta boxes callback.
	 */
	public function register_boxes();
}
