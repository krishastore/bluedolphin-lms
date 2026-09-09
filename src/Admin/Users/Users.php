<?php
/**
 * The file that defines the user management functionality.
 *
 * @link       https://www.skilltriks.com/
 * @since      1.0.0
 *
 * @package    ST\Lms\Admin\Users
 */

namespace ST\Lms\Admin\Users;

use const ST\Lms\STLMS_USER_DEPARTMENTS;
use const ST\Lms\STLMS_COURSE_TAXONOMY_DEP;

/**
 * Users manage class.
 */
class Users extends \ST\Lms\Admin\Core implements \ST\Lms\Interfaces\AdminCore {

	/**
	 * Store capability list class object.
	 *
	 * @var object|null $capability_list
	 * @since 1.0.0
	 */
	public $capability_list = null;

	/**
	 * Init hooks.
	 */
	public function __construct() {
		// Hooks.
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ), 20 );
		add_action( 'user_new_form', array( $this, 'stlms_user_departments_dropdown' ), 10, 1 );
		add_action( 'show_user_profile', array( $this, 'stlms_user_departments_dropdown' ), 10, 1 );
		add_action( 'edit_user_profile', array( $this, 'stlms_user_departments_dropdown' ), 10, 1 );
		add_action( 'user_register', array( $this, 'stlms_save_user_departments' ), 10, 1 );
		add_action( 'personal_options_update', array( $this, 'stlms_save_user_departments' ), 10, 1 );
		add_action( 'edit_user_profile_update', array( $this, 'stlms_save_user_departments' ), 10, 1 );
		add_action( 'manage_users_extra_tablenav', array( $this, 'stlms_users_department_filter' ), 10 );
		add_filter( 'pre_get_users', array( $this, 'stlms_filter_users_by_department' ), 10 );
	}

	/**
	 * Registration of admin submenu.
	 */
	public function register_admin_menu() {
		$hook = add_submenu_page(
			'skilltriks',
			__( 'User Role Editor', 'skilltriks' ),
			__( 'User Role Editor', 'skilltriks' ),
			apply_filters( 'skilltriks/menu/capability', 'manage_options' ),
			'stlms_manage_roles',
			array( $this, 'render_menu_page' )
		);
		add_action( "load-$hook", array( $this, 'load_menu_page' ) );
	}

	/**
	 * Load submenu page..
	 */
	public function load_menu_page() {
		// Include WP_List_Table class file.
		require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
		// Call the required model.
		$this->capability_list = new \ST\Lms\Admin\Users\CapabilityList();
	}

	/**
	 * Render admin menu page.
	 */
	public function render_menu_page() {
		require_once STLMS_TEMPLATEPATH . '/admin/users/capability-list.php';
	}

	/**
	 * Display department dropdown in user profile and add new user page.
	 *
	 * @param string|object $operation Current operation.
	 */
	public function stlms_user_departments_dropdown( $operation ) {
		// Add department only for pro version.
		if ( ! class_exists( \LFI\Stlms\LicenseIntegration::class ) ) :
			return;
		endif;

		if ( ! \LFI\Stlms\LicenseIntegration::instance()->is_pro() ) :
			return;
		endif;

		if ( is_string( $operation ) && 'add-new-user' !== $operation ) {
			return;
		}

		$user_department = is_object( $operation )
			? get_user_meta( $operation->ID, STLMS_USER_DEPARTMENTS, true )
			: '';

		$dep_list = get_terms(
			array(
				'taxonomy'   => STLMS_COURSE_TAXONOMY_DEP,
				'hide_empty' => false,
				'fields'     => 'id=>name',
			)
		);
		?>
		
		<h3><?php esc_html_e( 'Additional Information', 'skilltriks' ); ?></h3>
		<table class="form-table">
			<tr>
				<th><label for="<?php echo esc_attr( STLMS_USER_DEPARTMENTS ); ?>"><?php esc_html_e( 'Department', 'skilltriks' ); ?></label></th>
				<td>
					<select name="<?php echo esc_attr( STLMS_USER_DEPARTMENTS ); ?>" id="<?php echo esc_attr( STLMS_USER_DEPARTMENTS ); ?>">
						<option value=""><?php esc_html_e( 'Select Department', 'skilltriks' ); ?></option>
						<?php if ( ! is_wp_error( $dep_list ) && ! empty( $dep_list ) ) : ?>
							<?php foreach ( $dep_list as $dep_id => $dep_name ) : ?>
								<option value="<?php echo absint( $dep_id ); ?>" <?php selected( $user_department, $dep_id ); ?>>
									<?php echo esc_html( $dep_name ); ?>
								</option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
					<p class="description"><?php esc_html_e( 'Please select user department.', 'skilltriks' ); ?></p>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Save user department on profile update and new user creation.
	 *
	 * @param int $user_id User ID.
	 */
	public function stlms_save_user_departments( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}

		if ( isset( $_POST[ STLMS_USER_DEPARTMENTS ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			update_user_meta(
				$user_id,
				STLMS_USER_DEPARTMENTS,
				absint( $_POST[ STLMS_USER_DEPARTMENTS ] ) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			);
		}
	}

	/**
	 * Add Department filter dropdown on Users list page.
	 *
	 * @param string $which The location of the extra table nav markup: 'top' or 'bottom'.
	 */
	public function stlms_users_department_filter( $which ) {
		// Add department only for pro version.
		if ( ! class_exists( \LFI\Stlms\LicenseIntegration::class ) ) :
			return;
		endif;

		if ( ! \LFI\Stlms\LicenseIntegration::instance()->is_pro() ) :
			return;
		endif;

		if ( 'top' !== $which ) {
			return;
		}

		$terms = get_terms(
			array(
				'taxonomy'   => STLMS_COURSE_TAXONOMY_DEP,
				'hide_empty' => false,
			)
		);
		?>

		<select name="user_department" id="user_department" class="postform">
			<option value="0"><?php esc_html_e( 'All Departments', 'skilltriks' ); ?></option>
			<?php
			if ( ! empty( $terms ) ) :
				foreach ( $terms as $term ) :
					$selected = isset( $_GET['user_department'] ) ? absint( $_GET['user_department'] ) : 0; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
					?>
					<option value="<?php echo absint( $term->term_id ); ?>"
						<?php selected( $selected, $term->term_id ); ?>>
						<?php echo esc_html( $term->name ); ?>
					</option>
					<?php
				endforeach;
			endif;
			?>
		</select>
		<input type="submit" name="filter_action" id="filter_action" class="button" value="<?php esc_attr_e( 'Filter', 'skilltriks' ); ?>">
		<?php
	}

	/**
	 * Filter users list by department using the admin dropdown.
	 *
	 * @param \WP_User_Query $query Current WP_User_Query object.
	 *
	 * @return \WP_User_Query Modified query with department filter applied.
	 */
	public function stlms_filter_users_by_department( $query ) {

		if ( ! is_admin() || 'users' !== get_current_screen()->id ) {
			return $query;
		}

		if ( isset( $_GET['user_department'] ) && ! empty( $_GET['user_department'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$term_id = absint( $_GET['user_department'] ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$query->set( 'meta_key', STLMS_USER_DEPARTMENTS );
			$query->set( 'meta_value', $term_id );
		}

		return $query;
	}
}
