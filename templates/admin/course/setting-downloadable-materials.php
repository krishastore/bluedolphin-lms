<?php
/**
 * Template: Course setting - Author.
 *
 * @package BlueDolphin\Lms
 */

?>
<div class="bdlms-tab-content<?php echo esc_attr( $active_class ); ?>" data-tab="downloadable-materials">
	<div class="bdlms-cs-download">
		<div class="bdlms-materials-box brd-0">
			<div class="bdlms-materials-box__header">
				<h3><?php esc_html_e( 'Materials', 'bluedolphin-lms' ); ?></h3>
				<p><?php printf( esc_html__( 'Max Size: %s   |   Format: .PDF, .TXT', 'bluedolphin-lms' ), esc_html( size_format( $max_upload_size ) ) ); // phpcs:ignore WordPress.WP.I18n.MissingTranslatorsComment ?></p>
			</div>
		</div>
		<div class="bdlms-materials-box">
			<div class="bdlms-materials-box__body">
				<div class="bdlms-materials-list">
					<ul>
						<li><strong><?php esc_html_e( 'File Title', 'bluedolphin-lms' ); ?></strong></li>
						<li><strong><?php esc_html_e( 'Method', 'bluedolphin-lms' ); ?></strong></li>
						<li><strong><?php esc_html_e( 'Action', 'bluedolphin-lms' ); ?></strong></li>
					</ul>
					<?php
						require_once BDLMS_TEMPLATEPATH . '/admin/course/materials-item.php';
					?>
				</div>
			</div>
			<div class="bdlms-materials-box__footer">
				<button type="button" class="button"><?php esc_html_e( 'Add More Materials', 'bluedolphin-lms' ); ?></button>
			</div>
		</div>				
	</div>
</div>