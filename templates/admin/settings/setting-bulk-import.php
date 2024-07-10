<?php
/**
 * Template: Setting Bulk Import Tab.
 *
 * @package BlueDolphin\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<button class="button button-primary bdlms-bulk-import">Bulk Import</button>
<button class="button button-primary bdlms-bulk-import-cancel">Bulk Import cancel</button>
<div id="bulk-import-modal" class="hidden" style="max-width:400px">
	<div class="bdlms-import-data">
		<div class="bdlms-import-msg">
			<div class="_left">
				<h3>Successful Import</h3>
				<div class="import-file-name">
					<div class="name">Artificial Intelligence Fill...</div>
					<span>24 July 2024</span>				
				</div>				
			</div>
			<div class="_right">
				<a href="#">View Log</a>
			</div>
		</div>
		<div class="bdlms-import-msg error-msg">
			<div class="_left">
				<h3>Failed Import</h3>
				<div class="import-file-name">
					<div class="name">Artificial Intelligence Fill...</div>
					<span>24 July 2024</span>				
				</div>				
			</div>
			<div class="_right">
				<a href="#">View Log</a>
			</div>
		</div>
		<div class="bdlms-import-msg cancel-msg">
			<div class="_left">
				<h3>Canceled Import</h3>
				<div class="import-file-name">
					<div class="name">Artificial Intelligence Fill...</div>
					<span>24 July 2024</span>				
				</div>				
			</div>
			<div class="_right">
				<a href="#">View Log</a>
			</div>
		</div>
		<div class="bdlms-import-msg cancel-msg">
			<div class="_left">
				<h3>Upload in Progress</h3>
				<div class="import-file-name">
					<div class="name">Artificial Intelligence Fill...</div>
					<span>24 July 2024</span>				
				</div>				
			</div>
			<div class="_right">
				<a href="#">View Log</a>
			</div>
		</div>
		<div class="bdlms-fileupload-progress">
			<div class="fileupload-value">50%</div>
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
				<div class="file-name">Artificial Intelligence Fill in the bank Question. CSV</div>
				<div class="file-row-column">20 Rows, 12 Columns</div>
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
			<h3>Imported Questions to Question Bank</h3>
			<ul>
				<li>
					<div>
						<svg width="14" height="14">
						<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#tick"></use>
					</svg> Successful Import
					</div>
					<span>234</span>
				</li>
				<li>
					<div>
						<svg width="14" height="14">
							<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#error"></use>
						</svg>
						Fail to Import 
					</div>
					<span>00</span>
				</li>
				<li>
					<div>Total Items Imported</div>
					<span>234</span>
				</li>
			</ul>
		</div>
		<div class="bdlms-import-action">
			<button class="button button-primary">Done</button>
		</div>
	</div>
</div>
<div id="bulk-import-cancel-modal" class="hidden" style="max-width:400px">
	<div class="bdlms-import-data">
		<div class="bdlms-imported-qus">
			<div>Do you want to keep the Data or Remove it.</div>
		</div>
		<div class="bdlms-import-action">
			<button class="button">Remove</button>
			<button class="button button-primary">Keep Data</button>
		</div>
	</div>
</div>

<?php

