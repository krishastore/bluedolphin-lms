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
            this.addMedia();
		},
		/**
		 * Dialog box.
		 */
		dialogInit: function () {
			$('#bulk-import-modal').dialog({
				title: settingObject.i18n.PopupTitle,
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
				title: settingObject.i18n.CancelPopupTitle,
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

        		// Get the item data
				var _this = $(this);
				var itemData = {
					import_status: _this.data('status'),
					import_type: _this.data('import'),
					file_name:  _this.data('file'),
					import_date:  _this.data('date'),
					success_rows:  _this.data('success'),
					fail_rows: _this.data('fail'),
					total_rows: _this.data('total'),
					progress: _this.data('progress'),
					file_path: _this.data('path'),
				};

				// Populate the modal with the item data
				var modal = $('#bulk-import-modal');
				var column = 0;

				if( 4 !== itemData.import_status ) {
					if( 1 === itemData.import_type ){
						column = 12;
					}else if( 2 === itemData.import_type ){
						column = 10;
					}else if( 3 === itemData.import_type ){
						column = 18;
					}
				}

				modal.find('.bdlms-import-msg, .bdlms-fileupload-progress').addClass('import');
				if ( 2 === itemData.import_status ) {
					modal.find('.bdlms-import-msg').addClass('success-msg').removeClass('import');
					modal.find('.bdlms-import-msg ._left h3').text(settingObject.i18n.SuccessTitle);
				} else if ( 4 === itemData.import_status ) {
					modal.find('.bdlms-import-msg').addClass('error-msg').removeClass('import');
					modal.find('.bdlms-import-msg ._left h3').text(settingObject.i18n.FailTitle);
				} else if ( 3 === itemData.import_status ) {
					modal.find('.bdlms-import-msg').addClass('cancel-msg').removeClass('import');
					modal.find('.bdlms-import-msg ._left h3').text(settingObject.i18n.CancelTitle);
				} else if ( 1 === itemData.import_status ) {
					modal.find('.bdlms-import-msg, .bdlms-fileupload-progress').removeClass('import');
					modal.find('.bdlms-import-msg').addClass('upload-msg');
					modal.find('.bdlms-import-msg ._left h3').text(settingObject.i18n.UploadTitle);
					modal.find('.fileupload-value').text(itemData.progress + '%');
					modal.find('.bdlms-progress-bar').css('width', itemData.progress + '%');
				}

				modal.find('.import-file-name .name').text(itemData.file_name);
				modal.find('.import-file-name span').text(itemData.import_date);
				modal.find('.bdlms-import-file .download a').attr("href", itemData.file_path);
				modal.find('.file-name').text(itemData.file_name);
				modal.find('.file-row-column').text(`${itemData.total_rows} ${settingObject.i18n.ImportRows}, ${column} ${settingObject.i18n.ImportColumns}`);
				modal.find('.bdlms-imported-qus .success-count').text(itemData.success_rows);
				modal.find('.bdlms-imported-qus .fail-count').text(itemData.fail_rows);
				modal.find('.bdlms-imported-qus .total-count').text(itemData.total_rows);

				// Show the modal
				modal.removeClass('hidden').addClass('active');
			});

    		// Hide the modal when clicking the "Done" button
   		 	$('.bdlms-import-action .button-primary').on('click', function() {
				$('#bulk-import-modal').prev().find('.ui-dialog-titlebar-close').trigger('click');
			});
			$(document).on('click', '.bdlms-bulk-import-cancel', function(e) {
				$('#bulk-import-cancel-modal').dialog('open');
				e.preventDefault();
				cancelId = $(this).data('id');
				fileId = $(this).data('fileid');
				importType = $(this).data('import');
			});
			$(document).on('click', '#bulk-import-cancel-modal .bdlms-import-action button', function(e) {
				e.preventDefault();
				$.post(
					settingObject.ajaxurl,
					{
						action: 'bdlms_get_import_cancel_data',
						_nonce: settingObject.nonce,
						status: this.id,
						id : cancelId,
						attachment_id :fileId,
						import_type: importType,
					},
					function(response) {
						$('#bulk-import-cancel-modal').prev().find('.ui-dialog-titlebar-close').trigger('click');
						window.location.reload();
				});
			});
		},

        /**
		 * Add media.
		 */
		addMedia: function() {
			var _this = this;
			// On upload button click.
			$( 'body' ).on( 'click', '.bdlms-open-media', function( e ) {
				e.preventDefault();
				var libraryType = $(this).attr('data-library_type');
				var allowedExt = $(this).attr('data-ext');
				var button = $( this );
				var importType = $('#filter-import-type').val();

				var wp_media_uploader = wp.media( {
					state: 'customState',
					states: [
						new wp.media.controller.Library({
                            title: settingObject.i18n.PopupTitle,
							id: 'customState', 
							library: wp.media.query({
								type: libraryType,
							}),
							multiple: false,
							date: false
						})
					]
				} ).on( 'select', function() { // it also has "open" and "close" events
					var attachment = {
						id: 0,
						name: '',
						url: ''
					};
					var buttonText = settingObject.i18n.emptyMediaButtonTitle;
					var mediaName  = settingObject.i18n.nullMediaMessage;
					if ( wp_media_uploader.state().get( 'selection' ).length ) {
						attachment = wp_media_uploader.state().get( 'selection' ).first().toJSON();
						var attachmentUrl = attachment.url;
						mediaName = '<a href="' + attachmentUrl + '" target="_blank">' + attachmentUrl.split('/').pop() + '</a>';
						buttonText = settingObject.i18n.MediaButtonTitle;
					}
					button
					.text(buttonText)
					.parent()
					.find('span.bdlms-media-name')
					.html(mediaName);
					button.parent().find( 'input:hidden' ).val( attachment.id ).trigger( 'change' );

					if( settingObject.HasOpenSpout ){
						$.post(
							settingObject.ajaxurl,
							{
								action: 'bdlms_get_file_attachment_id',
								_nonce: settingObject.nonce,
								attachment_id: attachment.id,
								import_type: importType,
							},
							function(response) {
								window.location.reload();
						});

						wp_media_uploader.close();

					}else{
						var statusError = new wp.media.view.UploaderStatusError({
							message: settingObject.i18n.errorMediaMessage
						});
						
						wp_media_uploader.views.add('.upload-errors', statusError, { at: 0 });	
						$(document).find('.media-modal-content .media-frame .media-frame-content .media-sidebar .media-uploader-status').css('display', 'block');
						$(document).find('.media-modal-content .media-frame .media-frame-content .media-sidebar .media-uploader-status .upload-errors').css('display', 'block');
						$(document).find('#__wp-uploader-id-2').css('display', 'block');			
					}
				} )
				.on( 'selection:toggle', function() {
					$(wp_media_uploader?.el)
					.find('button.media-button-select')
					.removeAttr('disabled');
				} )
				.once( 'uploader:ready', function() {
					var uploader = wp_media_uploader.uploader.uploader.uploader;
					uploader.setOption(
						'filters',
						{
							mime_types: [
								{
									extensions: allowedExt
								}
							]
						}
					);
				} );
				$(document).on( 'click', '.media-button-select', function() {
					wp_media_uploader.trigger('select');
				} );

				wp_media_uploader.on( 'open', function() {
                
                    $(document).find('.media-modal-content .media-frame .media-frame-toolbar .media-toolbar-primary').append(`<a href='#'>${settingObject.i18n.DemoFileTitle}</a>`);

					var selectedVal = button.parent().find( 'input:hidden' ).val();
					if ( '' === selectedVal ) {
						return;
					}
					var selection = wp_media_uploader.state().get('selection');
					attachment = wp.media.attachment(selectedVal);
					attachment.fetch();
					selection.add( attachment ? [ attachment ] : [] );
				} );

				wp_media_uploader.open();
			});
		}
	};
	$(function () {
		settingModule.init();
	});
} )(jQuery, window.wp);