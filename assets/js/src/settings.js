/**
 * This file contains the functions needed for handle settings module.
 *
 * @since 1.0.0
 * @output assets/js/settings.js
 */

window.wp = window.wp || {};

/**
 * Manages the general setting and bulk import.
 *
 * @namespace settingModule
 *
 * @since 1.0.0
 *
 * @type {Object}
 *
 * @property {string} type The type of inline editor.
 * @property {string} what The prefix before the post ID.
 *
 */
( function ($, wp) {

	window.settingModule = {
		/**
		 * Initializes
		 */
		init: function() {
			this.dialogInit();
		},
		/**
		 * Dialog box.
		 */
		dialogInit: function () {
			$('#bulk-import-modal').dialog({
				title: 'Import File',
				dialogClass: "wp-dialog bdlms-modal bulk-import-modal",
				autoOpen: false,
				draggable: false,
				width: "auto",
				modal: true,
				resizable: false,
				closeOnEscape: true,
				position: {
					my: "center",
					at: "center",
					of: window,
				},
				open: function (event, ui) {
				},
				create: function () {
				},
				beforeClose: function() {
				}
			});
			$('#bulk-import-cancel-modal').dialog({
				title: 'Cancel Import',
				dialogClass: "wp-dialog bdlms-modal bulk-import-modal",
				autoOpen: false,
				draggable: false,
				width: "auto",
				modal: true,
				resizable: false,
				closeOnEscape: true,
				position: {
					my: "center",
					at: "center",
					of: window,
				},
				open: function (event, ui) {
				},
				create: function () {
				},
				beforeClose: function() {
				}
			});
			$(document).on('click', '.bdlms-bulk-import', function(e) {
				$('#bulk-import-modal').dialog('open');
				e.preventDefault();
			});
			$(document).on('click', '.bdlms-bulk-import-cancel', function(e) {
				$('#bulk-import-cancel-modal').dialog('open');
				e.preventDefault();
			});
		}
	};
	$(function () {
		settingModule.init();
	});
} )(jQuery, window.wp);
