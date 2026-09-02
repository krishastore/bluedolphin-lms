<?php
/**
 * Template: Course setting - Author.
 *
 * @package ST\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="stlms-tab-content<?php echo esc_attr( $active_class ); ?>" data-tab="downloadable-materials">
	<div class="stlms-cs-download">
		<div class="stlms-materials-box brd-0">
			<div class="stlms-materials-box__header">
				<h3><?php esc_html_e( 'Materials', 'skilltriks' ); ?></h3>
				<p><?php echo esc_html( sprintf( __( 'Max Size: %s   |   Format: .PDF, .TXT', 'skilltriks' ), esc_html( size_format( $max_upload_size ) ) ) ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment ?></p>
			</div>
		</div>
		<div class="stlms-materials-box">
			<div class="stlms-materials-box__body">
				<div class="stlms-materials-list">
					<ul>
						<li><strong><?php esc_html_e( 'File Title', 'skilltriks' ); ?></strong></li>
						<li><strong><?php esc_html_e( 'Method', 'skilltriks' ); ?></strong></li>
						<li><strong><?php esc_html_e( 'Action', 'skilltriks' ); ?></strong></li>
					</ul>
					<?php
						require_once STLMS_TEMPLATEPATH . '/admin/course/materials-item.php';
					?>
				</div>
			</div>
			<div class="stlms-materials-box__footer">
				<button type="button" class="button"><?php esc_html_e( 'Add More Materials', 'skilltriks' ); ?></button>
			</div>
		</div>
	</div>
</div>
