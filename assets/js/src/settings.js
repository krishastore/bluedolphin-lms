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
			this.addLogo();
			this.openTab();
			this.updatePreviewText();
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
				var importMsg = {
					"1" : settingObject.i18n.ImportQuestionMsgText, 
					"2" : settingObject.i18n.ImportLessonMsgText,
					"3" : settingObject.i18n.ImportCourseMsgText 
				};

				if( 4 !== itemData.import_status ) {
					if( 1 === itemData.import_type ){
						column = 12;
					}else if( 2 === itemData.import_type ){
						column = 10;
					}else if( 3 === itemData.import_type ){
						column = 18;
					}
				}

				modal.find('.bdlms-import-msg, .bdlms-fileupload-progress').addClass('import').removeClass('success-msg error-msg cancel-msg');
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
				modal.find('.bdlms-imported-qus h3').text(importMsg[itemData.import_type] || '');
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

					var importCSV = {
						"1" : settingObject.QuestionCsvPath, 
						"2" : settingObject.LessonCsvPath,
						"3" : settingObject.CourseCsvPath 
					};
	
                
                    $(document).find('.media-modal-content .media-frame .media-frame-toolbar .media-toolbar-primary').append(`<a href='${importCSV[importType]}'>${settingObject.i18n.DemoFileTitle}</a>`);

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
		},
		addLogo: function(){
			jQuery(document).ready(function($) {
				$('.upload_image_button').on('click', function(e) {
					e.preventDefault();
					var button = $(this);
					var image = $(this).data('target');
					var width = '#company_logo' === image ? 240 : 220;
					var height = '#company_logo' === image ? 60 : 40;
					var custom_uploader = wp.media({
						library: {
							type: 'image' // Restrict to images only.
						},
						multiple: false
					});
			
					// When an image is selected, run a callback.
					custom_uploader.on('select', function() {
						var attachment = custom_uploader.state().get('selection').first().toJSON();
						
						// Check the image dimensions.
						if ( ( attachment.width <= width && attachment.height <= height ) && settingObject.HasGdLibrary ) {
							$(button.data('target')).val(attachment.url);
							button.siblings('img').remove();
							button.after('<br /><img src="' + attachment.url + '" style="max-width:240px; margin-top:10px;" />');
						} else {
							custom_uploader.content.get().$el.find('.media-uploader-status .upload-errors').empty();

							var Message = settingObject.HasGdLibrary ? settingObject.i18n.uploadSizeMessage : settingObject.i18n.errorMediaMessage;
							Message = Message.replace( '240', width );
							Message = Message.replace( '60', height );
							var statusError = new wp.media.view.UploaderStatusError({
								message: Message
							});
		
							custom_uploader.content.get().$el.find('.media-uploader-status .upload-errors').append(statusError.render().el);
							$(document).find('.media-modal-content .media-frame .media-frame-content .media-sidebar .media-uploader-status').css('display', 'block');
							$(document).find('.media-modal-content .media-frame .media-frame-content .media-sidebar .media-uploader-status .upload-errors').css('display', 'block');
							$(document).find('.media-modal-content .media-frame .media-frame-content .media-sidebar .media-uploader-status .upload-dismiss-errors').css('display', 'block');
		
							// Reopen the media modal to keep it open.
							custom_uploader.open();
						}

						button.parent().find( 'input:hidden' ).val( attachment.id ).trigger( 'change' );	
					});

					custom_uploader.on( 'selection:toggle', function() {
						$(custom_uploader?.el)
						.find('button.media-button-select')
						.removeAttr('disabled');
					} );

					$(document).on( 'click', '.media-button-select', function() {
						custom_uploader.trigger('select');
					} );

					custom_uploader.on('open', function() {
						var selection = custom_uploader.state().get('selection');

						selection.props.set('mime', 'image/jpeg,image/png,image/jpg'); // Restrict to JPEG and PNG only.

						var selectedVal = button.parent().find( 'input:hidden' ).val();
						if ( '' === selectedVal ) {
							return;
						}
						attachment = wp.media.attachment(selectedVal);
						attachment.fetch();
						selection.add( attachment ? [ attachment ] : [] );
					});
					// Open the media frame.
					custom_uploader.open();
				});
			});
		},
		openTab: function () {
			// Add click event listeners to all tab buttons
			const tabButtons = document.querySelectorAll(
				".nav-tabs .nav-link",
			);

			tabButtons.forEach((button) => {
				button.addEventListener("click", function (e) {
					e.preventDefault();

					// Remove active class from all tabs and hide all content
					document
						.querySelectorAll(".nav-tabs .nav-link")
						.forEach((tab) => {
							tab.classList.remove("active");
							tab.setAttribute("aria-selected", "false");
						});

					document
						.querySelectorAll(".tab-pane")
						.forEach((content) => {
							content.classList.remove("active");
							content.style.display = "none";
						});

					// Add active class to clicked tab and show its content
					this.classList.add("active");
					this.setAttribute("aria-selected", "true");

					const tabId = this.getAttribute("data-tab");
					const tabContent = document.getElementById(tabId);

					document.querySelectorAll(".form-select").forEach((select) => {
						if ( ! select.value) {
							select.setAttribute('disabled', 'true');
						}
					});

					if (tabContent) {
						tabContent.classList.add("active");
						tabContent.style.display = "block";
						
						const selectElements = tabContent.querySelectorAll('select');
						if (tabContent.classList.contains('active')) {
							selectElements.forEach(select => {
								select.removeAttribute('disabled');
							});
						}	
					}
				});
			});

			// Select all inputs
			const valueInputs = document.querySelectorAll('input[type="text"]');
			const colorInputs = document.querySelectorAll('input[type="color"]');

			// Function to sync the color from the color picker to the text input
			const syncColorFromPicker = (index) => {
				valueInputs[index].value = colorInputs[index].value;
			};

			// Function to sync the color from the text input to the color picker
			const syncColorFromText = (index) => {
				colorInputs[index].value = valueInputs[index].value;
			};

			// Bind events to callbacks
			colorInputs.forEach((colorInput, index) => {
			colorInput.addEventListener("input", () => syncColorFromPicker(index), false);
			valueInputs[index].addEventListener("input", () => syncColorFromText(index), false);

			// Optional: Trigger the picker when the text field is focused
			valueInputs[index].addEventListener("focus", () => colorInputs[index].click(), false);

			// Initialize text field with the current color value
			syncColorFromPicker(index);
			});
		},
		updatePreviewText: function () {
			document.querySelectorAll(".form-select").forEach((select) => {
				const targetId = select.getAttribute("data-target");
				const targetPreview = document.getElementById(targetId);
				const dataStyle = select.getAttribute("data-style");
		
				// Apply the initial value from the form on page load
				const value = select.value;
				targetPreview.style[dataStyle] = value === "Default" ? "" : value;
		
				// Add event listener to update the style when a change is made
				select.addEventListener("change", function () {
					const newValue = this.value;
					targetPreview.style[dataStyle] = newValue === "Default" ? "" : newValue;
				});
			});
		
			// Trigger a simulated "change" event on each select to apply styles on initial load
			document.querySelectorAll(".form-select").forEach((select) => {
				select.dispatchEvent(new Event("change"));
			});
		},		
	};
	$(function () {
		settingModule.init();
	});
} )(jQuery, window.wp);