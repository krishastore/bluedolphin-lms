<?php
/**
 * The file that manage the setting options.
 *
 * @link       https://www.skilltriks.com/
 * @since      1.0.0
 *
 * @package    ST\Lms
 */

namespace ST\Lms\Helpers;

use ST\Lms\ErrorLog as EL;

/**
 * Helpers utility class.
 */
class SettingOptions {

	/**
	 * Global options.
	 *
	 * @var array $options
	 */
	public $options;

	/**
	 * Setting group
	 *
	 * @var string $option_group
	 */
	private $option_group = 'stlms_settings';

	/**
	 * Option section
	 *
	 * @var string $option_section
	 */
	private $option_section = 'stlms_section';

	/**
	 * Option name
	 *
	 * @var string $option_name
	 */
	private $option_name = 'stlms_settings';

	/**
	 * Setting fields
	 *
	 * @var array $fields
	 */
	private $fields = array();

	/**
	 * The main instance var.
	 *
	 * @var SettingOptions|null $instance The one SettingOptions instance.
	 * @since 1.0.0
	 */
	private static $instance = null;

	/**
	 * Init the main singleton instance class.
	 *
	 * @return SettingOptions Return the instance class
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new SettingOptions();
		}
		return self::$instance;
	}

	/**
	 * Init function.
	 */
	public function init() {
		// Get options.
		$this->options = array_filter( get_option( $this->option_name ) ? get_option( $this->option_name ) : array() );
		// Add admin menu.
		add_action( 'init', array( $this, 'set_fields' ) );
		add_action( 'admin_menu', array( $this, 'register_settings' ), 30 );
		add_filter( 'set-screen-option', array( $this, 'set_screen_option' ), 10, 3 );
	}

	/**
	 * Set fields.
	 */
	public function set_fields() {
		$this->fields = array(
			'client_id'             => array(
				'title' => esc_html__( 'Client ID', 'skilltriks' ),
				'desc'  => sprintf(
					// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
					__( 'Google application <a href="%s" target="_blank">Client ID</a>', 'skilltriks' ),
					'https://github.com/googleapis/google-api-php-client/blob/main/docs/oauth-web.md#create-authorization-credentials'
				),
				'type'  => 'password',
				'value' => '',
			),
			'client_secret'         => array(
				'title' => esc_html__( 'Client Secret', 'skilltriks' ),
				'desc'  => sprintf(
					// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
					__( 'Google application <a href="%s" target="_blank">Client Secret</a>', 'skilltriks' ),
					'https://github.com/googleapis/google-api-php-client/blob/main/docs/oauth-web.md#create-authorization-credentials'
				),
				'type'  => 'password',
				'value' => '',
			),
			'redirect_uri'          => array(
				'title'    => esc_html__( 'Redirect URL', 'skilltriks' ),
				'desc'     => sprintf(
					// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
					__( 'Google application <a href="%s" target="_blank">redirect URL</a>, Please copy the URL and add it to your application.', 'skilltriks' ),
					'https://github.com/googleapis/google-api-php-client/blob/main/docs/oauth-web.md#redirect_uri'
				),
				'type'     => 'url',
				'value'    => home_url( \ST\Lms\get_page_url( 'login', true ) ),
				'readonly' => true,
			),
			'company_logo'          => array(
				'title' => esc_html__( 'Company Logo', 'skilltriks' ),
				'desc'  => __( 'Add an image of size 240 x 100 pixels', 'skilltriks' ),
				'type'  => 'file',
				'value' => isset( $this->options['company_logo'] ) ? esc_url( $this->options['company_logo'] ) : '',
			),
			'certificate_signature' => array(
				'title' => esc_html__( 'Certificate Signature', 'skilltriks' ),
				'desc'  => __( 'Add an image of size 220 x 80 pixels', 'skilltriks' ),
				'type'  => 'file',
				'value' => isset( $this->options['certificate_signature'] ) ? esc_url( $this->options['certificate_signature'] ) : '',
			),
			'due_soon'              => array(
				'title' => esc_html__( 'Due Soon', 'skilltriks' ),
				'desc'  => sprintf(
					// translators: %d is the number of days before the course is due.
					__( 'Course to be due soon in %d days', 'skilltriks' ),
					isset( $this->options['due_soon'] ) ? absint( $this->options['due_soon'] ) : 7
				),
				'type'  => 'number',
				'value' => isset( $this->options['due_soon'] ) ? absint( $this->options['due_soon'] ) : '',
			),
			'override_login'        => array(
				'title' => esc_html__( 'Override Login', 'skilltriks' ),
				'desc'  => sprintf( __( 'Allow all users to login into skilltriks platform', 'skilltriks' ) ),
				'type'  => 'checkbox',
				'value' => isset( $this->options['override_login'] ) ? absint( $this->options['override_login'] ) : 0,
			),
		);
		add_action( 'admin_post_customize_theme', array( $this, 'customize_theme_options' ) );
		add_action( 'admin_post_user_role', array( $this, 'stlms_new_user_role' ) );
		add_action( 'admin_post_user_caps', array( $this, 'stlms_user_capabilities' ) );
		add_action( 'admin_action_activate_layout', array( $this, 'handle_layout_activation' ) );
		add_action( 'admin_post_stlms_setting', array( $this, 'stlms_setting_options' ) );
	}

	/**
	 * Save setting options.
	 */
	public function stlms_setting_options() {
		if ( isset( $_POST['stlms-setting-nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['stlms-setting-nonce'] ) ), 'stlms_setting' ) ) :
			$setting_options = array();
			foreach ( $this->fields as $key => $field ) :
				if ( 'checkbox' === $field['type'] ) :
					$setting_options[ $key ] = isset( $_POST[ $this->option_name ][ $key ] ) ? 1 : 0;
				elseif ( isset( $_POST[ $this->option_name ][ $key ] ) ) :
					$setting_options[ $key ] = sanitize_text_field( wp_unslash( $_POST[ $this->option_name ][ $key ] ) );
				endif;
			endforeach;
			$setting = wp_parse_args( $setting_options, $this->options );
			update_option( $this->option_name, $setting );
		endif;

		// phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
		wp_redirect( wp_get_referer() );
		exit;
	}

	/**
	 * Option Page.
	 */
	public function register_settings() {
		$setting_name = esc_html__( 'Settings', 'skilltriks' );
		// Add option page.
		$hook = add_submenu_page( \ST\Lms\PARENT_MENU_SLUG, $setting_name, $setting_name, 'manage_options', 'stlms-settings', array( $this, 'view_admin_settings' ) );
		// Add setting section.
		add_settings_section( $this->option_section, '', '__return_false', $this->option_group );
		// Add field.
		foreach ( $this->fields as $key => $field ) {
			add_settings_field(
				$key,
				$field['title'],
				array( $this, 'settings_field_input' ),
				$this->option_group,
				$this->option_section,
				array(
					'id'        => $key,
					'label_for' => $key,
					'desc'      => $field['desc'],
					'type'      => $field['type'],
					'value'     => $field['value'],
					'readonly'  => ! empty( $field['readonly'] ),
				)
			);
		}

		add_action( "load-$hook", array( $this, 'load_setting_page' ) );
	}

	/**
	 * Load setting page.
	 */
	public function load_setting_page() {
		$this->setting_enqueue_scripts();
		$this->add_options();
	}

	/**
	 * Enqueue setting scripts and styles.
	 */
	public function setting_enqueue_scripts() {
		wp_enqueue_media();
		wp_enqueue_style( \ST\Lms\STLMS_SETTING );
		wp_enqueue_script( \ST\Lms\STLMS_SETTING );
	}

	/**
	 * Add screen option.
	 */
	public function add_options() {
		//phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['tab'] ) && 'bulk-import' !== $_GET['tab'] ) {
			return;
		}
		add_screen_option(
			'per_page',
			array(
				'label'   => __( 'Number of items per page:', 'skilltriks' ),
				'default' => get_option( 'posts_per_page', 10 ),
				'option'  => 'imports_per_page',
			)
		);
	}

	/**
	 * Set screen option.
	 *
	 * @param mixed  $status Value to save instead of option value.
	 * @param string $option Option name.
	 * @param int    $value Option value.
	 *
	 * @return int Option value.
	 */
	public function set_screen_option( $status, $option, $value ) {
		if ( 'imports_per_page' === $option ) {
			return $value;
		}
		return $status;
	}

	/**
	 * Array deep sanitize text.
	 *
	 * @param array $args The arguments.
	 *
	 * @return array Post data.
	 */
	public function sanitize_settings( $args ) {
		return array_map(
			function ( $item ) {
				if ( is_array( $item ) ) {
						return $this->sanitize_settings( $item );
				} else {
					return sanitize_text_field( $item );
				}
			},
			$args
		);
	}

	/**
	 * Setting option input fields.
	 *
	 * @param array $args The arguments.
	 */
	public function settings_field_input( $args ) {
		$id          = isset( $args['id'] ) ? $args['id'] : '';
		$type        = isset( $args['type'] ) ? $args['type'] : '';
		$desc        = isset( $args['desc'] ) ? $args['desc'] : '';
		$default_val = isset( $args['value'] ) ? $args['value'] : '';
		$value       = $this->get_option( $id );
		$value       = ! empty( $value ) ? $value : $default_val;

		if ( 'file' === $type ) {
			$button_text = $value ? esc_html__( 'Change Image', 'skilltriks' ) : esc_html__( 'Upload Image', 'skilltriks' );
			echo '<input type="hidden" id="' . esc_attr( $id ) . '" name=' . esc_html( $this->option_name ) .
				'[' . esc_attr( $id ) . ']" value="' . esc_attr( $value ) . '" />';
			//phpcs:ignore
			echo '<button type="button" id="upload_logo" class="button upload_image_button" data-target="#' . esc_attr( $id ) . '">' . $button_text . '</button>';
			if ( $value ) {
				$width  = 'company_logo' === $id ? '240px' : '220px';
				$height = 'company_logo' === $id ? '100px' : '80px';
				// phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
				echo '<br /><img src="' . esc_url( wp_get_attachment_image_url( $value ) ) .
					'" alt="" style="max-width:' . esc_attr( $width ) .
					'; max-height:' . esc_attr( $height ) . '; margin-top:10px;" />';
			}
		} elseif ( ! empty( $args['readonly'] ) ) {
			echo '<input id="' . esc_attr( $id ) . '" name=' . esc_html( $this->option_name ) .
				'[' . esc_attr( $id ) . ']" size="40" type="' . esc_attr( $type ) .
				'" value="' . esc_attr( $value ) . '" readonly/>';
		} elseif ( 'number' === $type ) {
			echo '<input id="' . esc_attr( $id ) . '" name=' . esc_html( $this->option_name ) .
				'[' . esc_attr( $id ) . ']" type="' . esc_attr( $type ) .
				'" value="' . esc_attr( $value ) . '" min="1" max="30"/>';
		} elseif ( 'checkbox' === $type ) {
			$checked = $value ? 'checked' : '';
			echo '<input id="' . esc_attr( $id ) . '" name="' . esc_attr( $this->option_name ) .
				'[' . esc_attr( $id ) . ']" type="checkbox" value="1" ' . esc_attr( $checked ) . ' />';
		} else {
			echo '<input id="' . esc_attr( $id ) . '" name=' . esc_html( $this->option_name ) .
				'[' . esc_attr( $id ) . ']" size="40" type="' . esc_attr( $type ) .
				'" value="' . esc_attr( $value ) . '" />';
		}
		if ( $desc ) {
			echo "<p class='stlms-description'>" . wp_kses_post( $desc ) . '</div>';
		}
	}

	/**
	 * Main Settings panel.
	 *
	 * @since 1.0
	 */
	public function view_admin_settings() {
		$tab = '';
		if ( isset( $_GET['tab'] ) && ! empty( $_GET['tab'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$tab = sanitize_text_field( wp_unslash( $_GET['tab'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}
		?>
		<div class="wrap stlms-settings">
			<div id="icon-options-general" class="icon32"></div>
			<nav class="nav-tab-wrapper">
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'general', menu_page_url( 'stlms-settings', false ) ) ); ?>"
					class="nav-tab <?php echo 'general' === $tab || empty( $tab ) ? esc_attr( 'active' ) : ''; ?>"><?php esc_html_e( 'General', 'skilltriks' ); ?></a>
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'bulk-import', menu_page_url( 'stlms-settings', false ) ) ); ?>"
					class="nav-tab <?php echo 'bulk-import' === $tab ? esc_attr( 'active' ) : ''; ?>"><?php esc_html_e( 'Bulk Import', 'skilltriks' ); ?></a>
				<?php if ( is_plugin_active( 'skilltriks-theme-pack/skilltriks-theme-pack.php' ) ) : ?>
					<a href="<?php echo esc_url( add_query_arg( 'tab', 'theme', menu_page_url( 'stlms-settings', false ) ) ); ?>"
						class="nav-tab <?php echo 'theme' === $tab ? esc_attr( 'active' ) : ''; ?>"><?php esc_html_e( 'Theme', 'skilltriks' ); ?></a>
					<?php if ( 'layout-default' !== $this->options['theme'] ) : ?>
					<a href="<?php echo esc_url( add_query_arg( 'tab', 'customise-theme', menu_page_url( 'stlms-settings', false ) ) ); ?>"
						class="nav-tab <?php echo 'customise-theme' === $tab ? esc_attr( 'active' ) : ''; ?>"><?php esc_html_e( 'Customise Theme', 'skilltriks' ); ?></a>
					<?php endif; ?>
				<?php endif; ?>
			</nav>
			<?php
			if ( 'bulk-import' === $tab ) {
				require_once STLMS_TEMPLATEPATH . '/admin/settings/setting-bulk-import.php';
			} elseif ( is_plugin_active( 'skilltriks-theme-pack/skilltriks-theme-pack.php' ) && 'theme' === $tab ) {
				require_once STLMS_TEMPLATEPATH . '/admin/settings/setting-theme.php';
			} elseif ( is_plugin_active( 'skilltriks-theme-pack/skilltriks-theme-pack.php' ) && 'customise-theme' === $tab ) {
				require_once STLMS_TEMPLATEPATH . '/admin/settings/setting-customise-theme.php';
			} else {
				require_once STLMS_TEMPLATEPATH . '/admin/settings/setting-general.php';
			}
			?>
		</div>
			<?php
	}

	/**
	 * Get option by keyname.
	 *
	 * @param string $key_name Key name.
	 * @return mixed
	 */
	public function get_option( $key_name ) {
		return isset( $this->options[ $key_name ] ) ? $this->options[ $key_name ] : '';
	}

	/**
	 * Get customize theme options.
	 */
	public function customize_theme_options() {

		if ( isset( $_POST['customize-theme-nonce'] )
			&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['customize-theme-nonce'] ) ), 'customize_theme' ) ) :
			$colors         = array();
			$typography     = array();
			$theme_settings = array();
			$theme_name     = $this->options['theme'];
			$colors         = \ST\Lms\layout_colors();
			$colors         = isset( $colors[ $theme_name ] ) ? $colors[ $theme_name ] : array();
			$layout         = \ST\Lms\layout_typographies();
			$html_tags      = $layout['tag'];
			$typographies   = $layout['typography'];

			// Get colors.
			foreach ( $colors as $color => $value ) :
				if ( ! empty( $_POST[ $color ] ) ) {
					$colors[ $color ] = sanitize_hex_color( wp_unslash( $_POST[ $color ] ) );
				}
			endforeach;

			// Get typography.
			foreach ( $html_tags as $html_tag ) :
				foreach ( $typographies as $key => $value ) :
					if ( ! empty( $_POST[ $key . '_' . $html_tag ] ) ) {
						$typography[ $html_tag ][ $key ] = sanitize_text_field( wp_unslash( $_POST[ $key . '_' . $html_tag ] ) );
					}
				endforeach;
			endforeach;

			// Get font-family.
			if ( ! empty( $_POST['font_family_global'] ) ) {
				$typography['global']['font_family'] = sanitize_text_field( wp_unslash( $_POST['font_family_global'] ) );
			}
			if ( ! empty( $_POST['font_family_body'] ) ) {
				$typography['body']['font_family'] = sanitize_text_field( wp_unslash( $_POST['font_family_body'] ) );
			}

			$typography = wp_parse_args( $typography, $this->options[ $theme_name ]['typography'] );
			$colors     = wp_parse_args( $colors, $this->options[ $theme_name ]['colors'] );

			$args = array(
				$theme_name =>
				array(
					'colors'     => $colors,
					'typography' => $typography,
				),
			);

			$theme_settings = wp_parse_args( $args, $this->options );

			if ( ! empty( $theme_settings[ $theme_name ]['colors'] ) || ! empty( $theme_settings[ $theme_name ]['typography'] ) ) {
				update_option( 'stlms_settings', $theme_settings );
			}

			if ( isset( $_POST['reset'] ) ) {
				unset( $this->options[ $theme_name ]['typography'], $this->options[ $theme_name ]['colors'] );
				update_option( 'stlms_settings', $this->options );
			}

		endif;

		// phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
		wp_redirect( add_query_arg( 'tab', 'customise-theme', wp_get_referer() ) );
		exit;
	}

	/**
	 * Handle layout activation.
	 */
	public function handle_layout_activation() {
		$value = '';

		if ( isset( $_GET['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'layout_nonce' ) ) {
			if ( isset( $_GET['theme'] ) && ! empty( $_GET['theme'] ) ) :
				$value = sanitize_text_field( wp_unslash( $_GET['theme'] ) );
				if ( ! isset( $this->options['theme'] ) || $this->options['theme'] !== $value ) :
					$this->options['theme'] = $value;
					update_option( 'stlms_settings', $this->options );
				endif;
			endif;

			// phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
			wp_redirect( add_query_arg( 'theme', $value, wp_get_referer() ) );
			exit;
		} else {
			wp_die(
				esc_html_e( 'Security check failed. Please try again.', 'skilltriks' ),
				esc_html_e( 'Error', 'skilltriks' ),
				array( 'back_link' => true )
			);
		}
	}

	/**
	 * Get new user role.
	 */
	public function stlms_new_user_role() {
		if ( isset( $_POST['user-role-nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['user-role-nonce'] ) ), 'user_role' ) ) {
			if ( isset( $_POST['user_role'] ) && ! empty( $_POST['user_role'] ) ) :
				$role_name = sanitize_text_field( wp_unslash( $_POST['user_role'] ) );
				$role_key  = preg_replace( '/\s+/', '_', strtolower( $role_name ) );
				if ( ! isset( $this->options['user_role'] ) || ! array_key_exists( $role_key, $this->options['user_role'] ) ) :
					$this->options['user_role'][ $role_key ] = ucwords( $role_name );
					update_option( 'stlms_settings', $this->options );
				endif;
				// phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
				wp_redirect( add_query_arg( 'role', sanitize_title( $role_name ), wp_get_referer() ) );
			endif;
		} else {
			wp_die(
				esc_html_e( 'Security check failed. Please try again.', 'skilltriks' ),
				esc_html_e( 'Error', 'skilltriks' ),
				array( 'back_link' => true )
			);
		}
	}

	/**
	 * Get new user capabilities.
	 */
	public function stlms_user_capabilities() {
		if ( isset( $_POST['user-caps-nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['user-caps-nonce'] ) ), 'user_caps' ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$caps         = isset( $_POST['users_can'] ) ? map_deep( $_POST['users_can'], 'sanitize_text_field' ) : array();
			$user_role    = isset( $_POST['role'] ) ? sanitize_text_field( wp_unslash( $_POST['role'] ) ) : '';
			$default_caps = ! empty( get_role( 'editor' ) ) ? get_role( 'editor' )->capabilities : array( 'read' => true );

			if ( ! empty( $user_role ) && ! empty( $caps ) ) {
				if ( array_key_exists( $user_role, $this->options['user_role'] ) ) {
					$caps        = array_fill_keys( $caps, true );
					$role_exists = get_role( $user_role );
					$caps        = array_merge( $caps, $default_caps );
					if ( empty( $role_exists ) ) {
						add_role( $user_role, $this->options['user_role'][ $user_role ], $caps );
					} else {
						$existing_caps = get_role( $user_role )->capabilities;
						foreach ( $caps as $key => $value ) {
							if ( ! array_key_exists( $key, $existing_caps ) ) {
								$role_exists->add_cap( $key, true );
							}
						}
						foreach ( $existing_caps as $key => $value ) {
							if ( ! array_key_exists( $key, $caps ) ) {
								$role_exists->remove_cap( $key );
							}
						}
					}
				}
			}

			// phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
			wp_redirect( add_query_arg( 'page', 'stlms_manage_roles', admin_url( 'admin.php' ) ) );
		} else {
			wp_die(
				esc_html_e( 'Security check failed. Please try again.', 'skilltriks' ),
				esc_html_e( 'Error', 'skilltriks' ),
				array( 'back_link' => true )
			);
		}
	}
}
