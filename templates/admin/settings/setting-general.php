<?php
/**
 * Template: Setting General Tab.
 *
 * @package BD\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<h2><?php echo esc_html( 'Settings' ); ?></h2>
<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
	<input type = 'hidden' name = 'action' value = 'bdlms_setting' / >
	<?php
	wp_nonce_field( 'bdlms_setting', 'bdlms-setting-nonce' );
	do_settings_sections( $this->option_group ); // @phpstan-ignore variable.undefined
	submit_button( esc_html__( 'Save', 'bluedolphin-lms' ) );
	?>
</form>

<?php
