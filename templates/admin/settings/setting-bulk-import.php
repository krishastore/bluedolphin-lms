<?php
/**
 * Template: Setting Bulk Import Tab.
 *
 * @package BlueDolphin\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use BlueDolphin\Lms\Helpers\ImportTable as Table;

/**
 * Renders all the Data to be displayed in import log table.
 */
?>
	</pre><div class="wrap"><div class="import-wrap"><h1><?php esc_html_e( 'Bulk Import Log', 'bluedolphin-lms' ); ?></h1>
		<div class="bdlms-media-choose">
			<div class="bdlms-media-file">
				<span class="bdlms-media-name"><?php esc_html_e( 'No File Chosen', 'bluedolphin-lms' ); ?></span>
				<div class="filter-wrap">
					<select name="filter" id="filter-import-type">
						<option selected="selected" value="1"><?php esc_html_e( 'Questions', 'bluedolphin-lms' ); ?></option>
						<option value="2"><?php esc_html_e( 'Lessons', 'bluedolphin-lms' ); ?></option>
						<option value="3"><?php esc_html_e( 'Courses', 'bluedolphin-lms' ); ?></option>
					</select>
				</div>
				<a href="javascript:;" class="bdlms-open-media button button-primary" data-library_type="text/csv" data-ext="csv"><?php esc_html_e( 'Import', 'bluedolphin-lms' ); ?></a>
			</div>
		</div>
	</div>
<?php
	Table::instance()->views();
	Table::instance()->prepare_items();
?>
	<form>
	<input type="hidden" name="page" value="bdlms-settings">
	<input type="hidden" name="tab" value="bulk-import">
<?php
	Table::instance()->search_box( 'search', 'search_id' );
	Table::instance()->display();
	echo '</form></div>';
?>
<div id="bulk-import-modal" class="hidden" style="max-width:400px">
	<div class="bdlms-import-data">
		<div class="bdlms-import-msg">
			<div class="_left">
				<h3></h3>
				<div class="import-file-name">
					<div class="name"></div>
					<span></span>				
				</div>				
			</div>
			<div class="_right">
				<a href="#"><?php esc_html_e( 'View Log', 'bluedolphin-lms' ); ?></a>
			</div>
		</div>
		<div class="bdlms-fileupload-progress">
			<div class="fileupload-value"></div>
			<div class="bdlms-progress">
				<div class="bdlms-progress-bar" style="width: 50%;"></div>
			</div>
		</div>
		<div class="bdlms-import-file">
			<div class="icon">
				<svg width="42" height="42">
					<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#csv"></use>
				</svg>
			</div>
			<div class="file-info">
				<div class="file-name"></div>
				<div class="file-row-column"></div>
			</div>
			<div class="download">
				<a href="#">
					<svg width="24" height="24">
						<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#download"></use>
					</svg>
				</a>
			</div>
		</div>
		<div class="bdlms-imported-qus">
			<h3><?php esc_html_e( 'Imported Questions to Question Bank', 'bluedolphin-lms' ); ?></h3>
			<ul>
				<li>
					<div>
						<svg width="14" height="14">
						<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#tick"></use>
					</svg> <?php esc_html_e( 'Successful Import', 'bluedolphin-lms' ); ?> 
					</div>
					<span class="success-count"></span>
				</li>
				<li>
					<div>
						<svg width="14" height="14">
							<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#error"></use>
						</svg>
						<?php esc_html_e( 'Fail to Import', 'bluedolphin-lms' ); ?> 
					</div>
					<span class="fail-count"></span>
				</li>
				<li>
					<div><?php esc_html_e( 'Total Items Imported', 'bluedolphin-lms' ); ?></div>
					<span class="total-count"></span>
				</li>
			</ul>
		</div>
		<div class="bdlms-import-action">
			<button class="button button-primary"><?php esc_html_e( 'Done', 'bluedolphin-lms' ); ?></button>
		</div>
	</div>
</div>
<div id="bulk-import-cancel-modal" class="hidden" style="max-width:400px">
	<div class="bdlms-import-data">
		<div class="bdlms-imported-qus">
			<div><?php esc_html_e( 'Do you want to keep the Data or Remove it.', 'bluedolphin-lms' ); ?></div>
		</div>
		<div class="bdlms-import-action">
			<button id="remove" class="button"><?php esc_html_e( 'Remove', 'bluedolphin-lms' ); ?></button>
			<button id="keep-data" class="button button-primary"><?php esc_html_e( 'Keep Data', 'bluedolphin-lms' ); ?></button>
		</div>
	</div>
</div>
<?php
