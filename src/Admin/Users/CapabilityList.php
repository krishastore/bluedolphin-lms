<?php
/**
 * The file that manage the user capabilities list page.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BlueDolphin\Lms\Admin\Users
 */

namespace BlueDolphin\Lms\Admin\Users;

/**
 * Declare Class `CapabilityList`
 */
class CapabilityList extends \BlueDolphin\Lms\Admin\Users\Capability {

	/**
	 * Prepare items
	 */
	public function prepare_items() {
		$search_by_name = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : ''; // phpcs:ignore
		$this->items    = $this->get_groups( $search_by_name );
		$per_page       = $this->per_page;
		$total_item     = 0;
		$this->set_pagination_args(
			array(
				'total_items' => $total_item,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_item / $per_page ),
			)
		);

		$sortable              = $this->get_sortable_columns();
		$columns               = $this->get_columns();
		$this->_column_headers = array( $columns, array(), $sortable );
		$this->views();
	}

	/**
	 * Columns
	 *
	 * @return string Column.
	 */
	public function get_columns() {
		$columns = array(
			'cb'         => '<input type="checkbox" />',
			'name'       => __( 'Group Name', 'bluedolphin-lms' ),
			'count'      => __( 'Count', 'bluedolphin-lms' ),
			'created_at' => __( 'Date', 'bluedolphin-lms' ),
		);
		return $columns;
	}

	/**
	 * Default Column
	 *
	 * @param mix $item Item.
	 * @param int $column_name Column name.
	 * @return mix
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'name':
				return $item->$column_name;
			case 'created_at':
				return date_i18n( 'd M, Y, H:i a', $item->$column_name );
			case 'count':
				return 0;
			default:
				return __( 'No Data Found', 'bluedolphin-lms' );
		}
	}

	/**
	 * View Record button.
	 *
	 * @param string $item email.
	 * @return sttring Field and action
	 */
	public function column_name( $item ) {
		$action = array(
			'edit'   => sprintf(
				'<a href="%s">%s</a>',
				esc_url(
					add_query_arg(
						array(
							'page'   => 'bdlms_manage_caps',
							'action' => 'edit_group',
							'id'     => $item->id,
						),
						admin_url( 'admin.php' )
					)
				),
				esc_html__( 'Edit', 'bluedolphin-lms' )
			),
			'delete' => sprintf(
				'<a href="%s" onclick="return confirm(\'%s\');">%s</a>',
				esc_url(
					add_query_arg(
						array(
							'page'   => 'bdlms_manage_caps',
							'action' => 'delete_group',
							'id'     => $item->id,
						),
						admin_url( 'admin.php' )
					)
				),
				esc_html__( 'Are you sure you want to delete this group?', 'bluedolphin-lms' ),
				esc_html__( 'Delete', 'bluedolphin-lms' )
			),
		);
		return sprintf( '%1$s %2$s', $item->name, $this->row_actions( $action ) );
	}

	/**
	 * Bulk delete actions.
	 */
	public function get_bulk_actions() {
		$delete_action = array(
			'bulk-delete' => __( 'Delete', 'bluedolphin-lms' ),
		);
		return $delete_action;
	}

	/**
	 * Checkbox in row.
	 *
	 * @param int $item id.
	 * @return id;
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="group_ids[]" value="%d" />', $item->id );
	}

	/**
	 * No groups found.
	 */
	public function no_items() {
		esc_html_e( 'No groups found.', 'bluedolphin-lms' );
	}

	/**
	 * Sortable columans.
	 */
	protected function get_sortable_columns() {
		$sortable_columns = array(
			'name'       => array( 'name', false ),
			'created_at' => array( 'created_at', false ),
		);
		return $sortable_columns;
	}

	/**
	 * Prints column headers, accounting for hidden and sortable columns.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $with_id Whether to set the ID attribute or not.
	 */
	public function print_column_headers( $with_id = true ) {
		global $wp;
		list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();

		$current_url  = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$current_url  = remove_query_arg( 'paged', $current_url );
		$_bdlms_nonce = wp_create_nonce( BDLMS_BASENAME );

		$current_orderby = '';
		$current_order   = 'asc';
		if ( isset( $_GET['_bdlms_user_caps_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_bdlms_user_caps_nonce'] ) ), BDLMS_BASENAME ) ) {
			if ( isset( $_GET['orderby'] ) ) {
				$current_orderby = sanitize_text_field( wp_unslash( $_GET['orderby'] ) );
			}

			if ( ( isset( $_GET['order'] ) && 'desc' === $_GET['order'] ) ) {
				$current_order = 'desc';
			}
		}

		if ( ! empty( $columns['cb'] ) ) {
			static $cb_counter = 1;
			$columns['cb']     = '<label class="screen-reader-text" for="cb-select-all-' . $cb_counter . '">' .
				/* translators: Hidden accessibility text. */
				__( 'Select All', 'bluedolphin-lms' ) .
			'</label>' .
			'<input id="cb-select-all-' . $cb_counter . '" type="checkbox" />';
			++$cb_counter;
		}

		foreach ( $columns as $column_key => $column_display_name ) {
			$class = array( 'manage-column', "column-$column_key" );

			if ( in_array( $column_key, $hidden, true ) ) {
				$class[] = 'hidden';
			}

			if ( 'cb' === $column_key ) {
				$class[] = 'check-column';
			} elseif ( in_array( $column_key, array( 'posts', 'comments', 'links' ), true ) ) {
				$class[] = 'num';
			}

			if ( $column_key === $primary ) {
				$class[] = 'column-primary';
			}

			if ( isset( $sortable[ $column_key ] ) ) {
				list( $orderby, $desc_first ) = $sortable[ $column_key ];

				if ( $current_orderby === $orderby ) {
					$order = 'asc' === $current_order ? 'desc' : 'asc';

					$class[] = 'sorted';
					$class[] = $current_order;
				} else {
					$order = strtolower( $desc_first );

					if ( ! in_array( $order, array( 'desc', 'asc' ), true ) ) {
						$order = $desc_first ? 'desc' : 'asc';
					}

					$class[] = 'sortable';
					$class[] = 'desc' === $order ? 'asc' : 'desc';
				}

				$column_display_name = sprintf(
					'<a href="%s"><span>%s</span><span class="sorting-indicator"></span></a>',
					esc_url( add_query_arg( compact( 'orderby', 'order', '_bdlms_nonce' ), $current_url ) ),
					$column_display_name
				);
			}

			$tag   = ( 'cb' === $column_key ) ? 'td' : 'th';
			$scope = ( 'th' === $tag ) ? 'scope="col"' : '';
			$id    = $with_id ? "id='$column_key'" : '';

			if ( ! empty( $class ) ) {
				$class = "class='" . implode( ' ', $class ) . "'";
			}
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo "<$tag $scope $id $class>$column_display_name</$tag>";
		}
	}
}
