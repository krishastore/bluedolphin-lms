<?php
/**
 * Display capability list template.
 *
 * @package BlueDolphin\Lms
 */

?>
<div class="wrap">
	<h2 style="display:inline-block; margin-right: 5px;"><?php esc_html_e( 'User capability groups', 'bluedolphin-lms' ); ?></h2>
	<a href="javascript:;" class="page-title-action"><?php esc_html_e( 'Add New Group', 'bluedolphin-lms' ); ?></a>
	<hr class="wp-header-end">
	<form method="get">
		<?php $this->capability_list->prepare_items(); ?>
		<p class="search-box">
			<input type="hidden" name="page" value="bdlms_manage_caps">
			<label class="screen-reader-text" for="search_email-search-input"><?php esc_html_e( 'Search:', 'bluedolphin-lms' ); ?></label>
			<input type="search" id="search_email-search-input" name="s" value="<?php echo isset( $_GET['s'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_GET['s'] ) ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification ?>" placeholder="<?php esc_attr_e( 'Search by group name', 'bluedolphin-lms' ); ?>">
			<input type="hidden" name="_bdlms_user_caps_nonce" value="<?php echo esc_attr( wp_create_nonce( BDLMS_BASENAME ) ); ?>">
			<input type="submit" id="search-submit" class="button" value="<?php esc_attr_e( 'Search', 'bluedolphin-lms' ); ?>">
		</p>
		<?php $this->capability_list->display(); ?>
	</form>
</div>
