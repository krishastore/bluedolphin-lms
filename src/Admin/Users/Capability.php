<?php
/**
 * The file that manage the user capabilities.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BlueDolphin\Lms\Admin\Users
 */

namespace BlueDolphin\Lms\Admin\Users;

/**
 * Capability manage class.
 */
class Capability extends \WP_List_Table {

	/**
	 * Pagination per page.
	 *
	 * @var $per_page.
	 */
	public $per_page;

	/**
	 * Public constructor.
	 */
	public function __construct() {
		// Set parent defaults.
		parent::__construct();

		$this->per_page = $this->get_items_per_page( 'mpg_projects_per_page', 20 );
	}

	/**
	 * Entry Data
	 *
	 * @param string $search_by_name Group search by name.
	 * @return Records
	 */
	public function get_groups( $search_by_name ) {
		$per_page = $this->per_page;
		$data     = array();
		return $data;
	}
}
