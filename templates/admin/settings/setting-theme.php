<?php
/**
 * Template: Setting Theme Options Tab.
 *
 * @package BD\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$theme_name = isset( $this->options['theme'] ) ? $this->options['theme'] : '';
?>

<div class="bdlms-tab-title-wrap">
	<h1 class="title">
		<?php esc_html_e( 'Choose your theme', 'bluedolphin-lms' ); ?>
	</h1>
</div>

<div class="theme-template-wrap">
	<ul>
		<li>
			<div class="theme-template-card">
				<div class="card-top">
					<div class="image-wrap">
						<img src="https://dummyimage.com/600x400/fff/000&text=bdlms" alt="">
					</div>
					<div class="overlay">
						<div class="btn-wrap">
							<?php if ( 'layout-default' === $theme_name ) { ?>
								<a href="<?php echo esc_url( add_query_arg( 'tab', 'customise-theme', menu_page_url( 'bdlms-settings', false ) ) ); ?>" class="button button-primary"><?php echo esc_html_e( 'Customize', 'bluedolphin-lms' ); ?></a>
							<?php } else { ?>
								<a href="
								<?php
									echo esc_url(
										add_query_arg(
											array(
												'action' => 'activate_layout',
												'tab'    => 'theme',
												'theme'  => 'layout-default',
												'nonce'  => wp_create_nonce( 'layout_nonce' ),
											),
											admin_url( 'admin.php' )
										)
									);
								?>
								" class="button button-primary"><?php esc_html_e( 'Activate', 'bluedolphin-lms' ); ?></a>
							<?php } ?>
						</div>
					</div>
				</div>
				<div class="card-bottom <?php echo 'layout-default' === $theme_name ? 'active' : ''; ?>">
					<div class="theme-title"><?php echo 'layout-default' === $theme_name ? esc_html_e( 'Active: ', 'bluedolphin-lms' ) : ''; ?><?php esc_html_e( 'Default Theme', 'bluedolphin-lms' ); ?></div>
				</div>
			</div>
		</li>
		<li>
			<div class="theme-template-card">
				<div class="card-top">
					<div class="image-wrap">
						<img src="https://dummyimage.com/600x400/fff/000&text=bdlms" alt="">
					</div>
					<div class="overlay">
						<div class="btn-wrap">
							<?php if ( 'layout-2' === $theme_name ) { ?>
							<a href="<?php echo esc_url( add_query_arg( 'tab', 'customise-theme', menu_page_url( 'bdlms-settings', false ) ) ); ?>" class="button button-primary"><?php echo esc_html_e( 'Customize', 'bluedolphin-lms' ); ?></a>
							<?php } else { ?>
								<a href="
								<?php
								echo esc_url(
									add_query_arg(
										array(
											'action' => 'activate_layout',
											'tab'    => 'theme',
											'theme'  => 'layout-2',
											'nonce'  => wp_create_nonce( 'layout_nonce' ),
										),
										admin_url( 'admin.php' )
									)
								);
								?>
								" class="button button-primary"><?php esc_html_e( 'Activate', 'bluedolphin-lms' ); ?></a>
							<?php } ?>
							<button class="button button-primary bdlms-bulk-import"><?php esc_html_e( 'Preview', 'bluedolphin-lms' ); ?></button>
						</div>
					</div>
				</div>
				<div class="card-bottom <?php echo 'layout-2' === $theme_name ? 'active' : ''; ?>">
					<div class="theme-title"><?php echo 'layout-2' === $theme_name ? esc_html_e( 'Active: ', 'bluedolphin-lms' ) : ''; ?><?php esc_html_e( 'Theme Layout 2', 'bluedolphin-lms' ); ?></div>
				</div>
			</div>
		</li>
	</ul>
</div>

<div class="preview-theme-modal wp-dialog bdlms-modal bulk-import-modal" id='bulk-import-modal'>
	<img src="https://dummyimage.com/1920x1080/000/fff" alt="">
</div>
