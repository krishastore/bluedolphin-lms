<?php
/**
 * The file that manage the setting options.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BlueDolphin\Lms
 */

namespace BlueDolphin\Lms\Helpers;

use BlueDolphin\Lms\ErrorLog as EL;

/**
 * Helpers utility class.
 */
class SettingOptions {

	/**
	 * Class instance.
	 *
	 * @var $instance
	 */
    private static $_instance = null; // phpcs:ignore

	/**
	 * Global options.
	 *
	 * @var $options
	 */
	public $options;

	/**
	 * Setting group
	 *
	 * @var $option_group  string
	 */
	private $option_group = '__bdlms_settings';

	/**
	 * Option section
	 *
	 * @var $option_section string
	 */
	private $option_section = '__bdlms_section';

	/**
	 * Option name
	 *
	 * @var $option_name string
	 */
	private $option_name = '__bdlms_settings';

	/**
	 * Setting fields
	 *
	 * @var $fields array
	 */
	private $fields = array();

	/**
	 * The main instance var.
	 *
	 * @var SettingOptions The one SettingOptions instance.
	 * @since 1.0.0
	 */
	private static $instance;

	/**
	 * Init the main singleton instance class.
	 *
	 * @return SettingOptions Return the instance class
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof SettingOptions ) ) {
			self::$instance = new SettingOptions();
		}
		return self::$instance;
	}

	/**
	 * Init function.
	 */
	public function init() {
		// Set global setting options.
		$this->fields = array(
			'client_id'     => array(
				'title' => esc_html__( 'Client ID', 'bluedolphin-lms' ),
				// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
				'desc'  => sprintf( __( 'Google application <a href="%s" target="_blank">Client ID</a>', 'bluedolphin-lms' ), 'https://github.com/googleapis/google-api-php-client/blob/main/docs/oauth-web.md#create-authorization-credentials' ),
				'type'  => 'password',
				'value' => '',
			),
			'client_secret' => array(
				'title' => esc_html__( 'Client Secret', 'bluedolphin-lms' ),
				// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
				'desc'  => sprintf( __( 'Google application <a href="%s" target="_blank">Client Secret</a>', 'bluedolphin-lms' ), 'https://github.com/googleapis/google-api-php-client/blob/main/docs/oauth-web.md#create-authorization-credentials' ),
				'type'  => 'password',
				'value' => '',
			),
			'redirect_uri'  => array(
				'title'    => esc_html__( 'Redirect URL', 'bluedolphin-lms' ),
				// phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment
				'desc'     => sprintf( __( 'Google application <a href="%s" target="_blank">redirect URL</a>, Please copy the URL and add it to your application.', 'bluedolphin-lms' ), 'https://github.com/googleapis/google-api-php-client/blob/main/docs/oauth-web.md#redirect_uri' ),
				'type'     => 'url',
				'value'    => home_url( \BlueDolphin\Lms\get_page_url( 'login', true ) ),
				'readonly' => true,
			),
		);
		// Get options.
		$this->options = array_filter( get_option( $this->option_name ) ? get_option( $this->option_name ) : array() );
		// Add admin menu.
		add_action( 'admin_menu', array( $this, 'register_settings' ), 30 );
	}

	/**
	 * Option Page.
	 */
	public function register_settings() {
		$setting_name = esc_html__( 'Settings', 'bluedolphin-lms' );
		// Add option page.
		add_submenu_page( \BlueDolphin\Lms\PARENT_MENU_SLUG, $setting_name, $setting_name, 'manage_options', 'bdlms-settings', array( $this, 'view_admin_settings' ) );
		// Register setting.
		register_setting( $this->option_group, $this->option_name, array( $this, 'sanitize_settings' ) );
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
	}

	/**
	 * Array deep sanitize text.
	 *
	 * @param array $args The arguments.
	 *
	 * @return array Post data.
	 */
	public function sanitize_settings( $args ) {
		return array_map( 'sanitize_text_field', $args );
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
		if ( ! empty( $args['readonly'] ) ) {
			// phpcs:ignore
			echo "<input id='$id' name='{$this->option_name}[{$id}]' size='40' type='{$type}' value='{$value}' readonly/>";
		} else {
			// phpcs:ignore
			echo "<input id='$id' name='{$this->option_name}[{$id}]' size='40' type='{$type}' value='{$value}' />";
		}
		if ( $desc ) {
			echo "<p class='description'>" . wp_kses_post( $desc ) . '</div>';
		}
	}

	/**
	 * Main Settings panel.
	 *
	 * @since 1.0
	 */
	public function view_admin_settings() {
		global $doing_option; ?>
		<div class="wrap bdlms-settings">
			<div id="icon-options-general" class="icon32"></div>
			<h2><?php echo esc_html( 'Settings' ); ?></h2>
			<form action="options.php" method="post">
				<?php
				settings_errors();
				settings_fields( $this->option_group );
				do_settings_sections( $this->option_group );
				submit_button( esc_html__( 'Save', 'bluedolphin-lms' ) );
				?>
			</form>
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
}
