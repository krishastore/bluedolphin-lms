<?php
/**
 * Template: Setting Theme Options Tab.
 *
 * @package ST\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$theme_name = isset( $this->options['theme'] ) ? $this->options['theme'] : '';
?>

<div class="stlms-tab-title-wrap">
	<h1 class="title">
		<?php esc_html_e( 'Choose your theme', 'skilltriks' ); ?>
	</h1>
</div>

<div class="theme-template-wrap">
	<ul>
		<li>
			<div class="theme-template-card">
				<div class="card-top">
					<div class="image-wrap">
						<img src="<?php echo esc_url( STLMS_ASSETS . '/images/default-theme.png' ); // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>" alt="default-theme-preview" width="666" height="432">
					</div>
					<div class="overlay">
						<div class="btn-wrap">
							<?php if ( 'layout-default' === $theme_name ) { ?>
								<a href="<?php echo esc_url( add_query_arg( 'tab', 'customise-theme', menu_page_url( 'stlms-settings', false ) ) ); ?>" class="button button-primary"><?php echo esc_html_e( 'Customize', 'skilltriks' ); ?></a>
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
								" class="button button-primary"><?php esc_html_e( 'Activate', 'skilltriks' ); ?></a>
							<?php } ?>
						</div>
					</div>
				</div>
				<div class="card-bottom <?php echo 'layout-default' === $theme_name ? 'active' : ''; ?>">
					<div class="theme-title"><?php echo 'layout-default' === $theme_name ? esc_html_e( 'Active: ', 'skilltriks' ) : ''; ?><?php esc_html_e( 'Default Theme', 'skilltriks' ); ?></div>
				</div>
			</div>
		</li>
		<li>
			<div class="theme-template-card">
				<div class="card-top">
					<div class="image-wrap">
						<img src="<?php echo esc_url( STLMS_ASSETS . '/images/theme-preview.png' ); // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>" alt="theme-2-preview" width="666" height="432">
					</div>
					<div class="overlay">
						<div class="btn-wrap">
							<?php if ( 'layout-2' === $theme_name ) { ?>
							<a href="<?php echo esc_url( add_query_arg( 'tab', 'customise-theme', menu_page_url( 'stlms-settings', false ) ) ); ?>" class="button button-primary"><?php echo esc_html_e( 'Customize', 'skilltriks' ); ?></a>
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
								" class="button button-primary"><?php esc_html_e( 'Activate', 'skilltriks' ); ?></a>
							<?php } ?>
							<button class="button button-primary stlms-bulk-import"><?php esc_html_e( 'Preview', 'skilltriks' ); ?></button>
						</div>
					</div>
				</div>
				<div class="card-bottom <?php echo 'layout-2' === $theme_name ? 'active' : ''; ?>">
					<div class="theme-title"><?php echo 'layout-2' === $theme_name ? esc_html_e( 'Active: ', 'skilltriks' ) : ''; ?><?php esc_html_e( 'Theme Layout 2', 'skilltriks' ); ?></div>
				</div>
			</div>
		</li>
	</ul>
</div>

<div class="preview-theme-modal wp-dialog stlms-modal bulk-import-modal" id='bulk-import-modal'>
	<img src="<?php echo esc_url( STLMS_ASSETS . '/images/theme-preview.png' ); // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>" alt="theme-2-preview" width="666" height="432">
</div>
