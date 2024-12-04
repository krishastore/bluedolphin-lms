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
<form action="options.php" method="post">
	<?php
	settings_errors();
	settings_fields( $this->option_group ); // @phpstan-ignore variable.undefined
	do_settings_sections( $this->option_group ); // @phpstan-ignore variable.undefined
	submit_button( esc_html__( 'Save', 'bluedolphin-lms' ) );
	?>
</form>

<?php
