/**
 * This file contains the functions needed for handle lesson module.
 *
 * @since 1.0.0
 * @output assets/js/lesson.js
 */

window.wp = window.wp || {};

/**
 * Manages the quick edit and bulk edit windows for editing posts or pages.
 *
 * @namespace lessonModule
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

		window.lessonModule = {
			/**
			 * Snackbar notice.
			 */
			snackbarNotice: function (message) {
				var _t = this;
				$('.bdlms-snackbar-notice').find('p').html(message);
				$('.bdlms-snackbar-notice').toggleClass('open', 1000);
				if ($('.bdlms-snackbar-notice').hasClass('open')) {
					setTimeout(function () {
						_t.snackbarNotice('');
					}, 3000);
				}
			},

			/**
			 * Initializes the inline editor.
			 */
			init: function () {
				var _this = this;
				_this.dialogInit();
				_this.addMedia();
				_this.handleMaterials();

				$(document).on("click", '[data-modal="assign_lesson"]', function (e) {
					e.preventDefault();
					$("#course_list_modal").dialog("open");
				} );

				// Inline quick edit.
				$(document).on('click', '.post-type-bdlms_lesson .button-link.editinline', function(e) {
					e.preventDefault();
					var  currentRow = $(this).parents('tr');
					var editRow = currentRow.next('tr.hidden').next('tr.inline-edit-row');

					$('.inline-edit-private', editRow).parents('div.inline-edit-group').remove();
					var rightCustomBox = $('.inline-edit-col-right:not(.inline-edit-lesson):visible', editRow);
					var selectedStatus = $('select', rightCustomBox).val();
					var duration = $("td.duration.column-duration span.duration-val", currentRow).text();
					var durationType = $("td.duration.column-duration span.duration-type", currentRow).text();
					var selectedCourses = $("td.course.column-course a[data-course_id]", currentRow).map(function() {
						return $(this).data('course_id');
					}).get();
					$(' > *', rightCustomBox).appendTo('.inline-edit-col-left:visible');
					jQuery('.inline-edit-col-left:visible select[name="_status"]').val(selectedStatus);
					$(".inline-edit-lesson-item:visible input:not(:checkbox)", editRow ).val(duration.replace(/[^0-9]/g, ''));
					$(".inline-edit-lesson-item:visible select", editRow ).val(durationType);
					if ( selectedCourses && selectedCourses.length ) {
						$.each(selectedCourses, function(i, v){
							$('.bdlms_course-checklist:visible input[value="' + v + '"]', editRow ).attr('checked', true).prop('checked', true);
						});
					}
				});
			},

			/**
			 * Dialog box.
			 */
			dialogInit: function () {
				var _this = this;
				$('#course_list_modal').dialog({
					title: lessonObject.i18n.PopupTitle,
					dialogClass: "wp-dialog bdlms-modal",
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
						$('#bdlms_course_list').load(
							lessonObject.contentLoadUrl + ' #bdlms_course_list > *',
							{
								fetch_courses: 1,
								post_id: $('#post_ID').val()
							},
							function () {
								$('.bdlms-choose-course').trigger('change');
							}
						);
					},
					create: function () {},
				});

				$(document).on('change', '.bdlms-choose-course', function () {
					var totalChecked = $("input:checkbox:checked", $(this).parents('ul'));
					$(this)
					.parents('.bdlms-qus-bank-modal')
					.find('.bdlms-add-course')
					.attr('disabled', function () {
						return false;
					})
					.next('.bdlms-qus-selected')
					.text(function (i, txt) {
						return txt.replace(/\d+/, totalChecked.length);
					});
				});

				$(document).on('click', '.bdlms-add-course', function (e) {
					var _btn = $(this);
					var courseIds = $('.bdlms-choose-course:checked')
					.map(function () {
						return $(this).val();
					})
					.get();
					var postId = $('#post_ID').val();

					$('.bdlms-choose-quiz:visible').attr('disabled', true);
					_btn.parent('div')
					.find('span.spinner')
					.addClass('is-active')
					.parent('div')
					.find('button')
					.attr('disabled', true);

					$.post( lessonObject.ajaxurl,
					{
						action: 'bdlms_assign_to_course',
						bdlms_nonce: lessonObject.nonce,
						selected: courseIds,
						post_id: postId,
					},
					function (data) {
						$('.bdlms-choose-quiz:visible').removeAttr('disabled');
						_btn.parent('div')
						.find('span.spinner')
						.removeClass('is-active')
						.parent('div')
						.removeAttr('disabled');
						$('#course_list_modal').dialog('close');
						_this.snackbarNotice(data.message);
					},
					'json'
					);
					e.preventDefault();
				});

				$(document).on('input', 'input.bdlms-qus-bank-search', function () {
					var searchBox = $(this);
					var searchKeyword = searchBox.val();
					clearTimeout($.data(this, "timer"));
					$(this).data( 'timer', setTimeout(function() {
						searchBox
						.addClass("ui-autocomplete-loading")
						.parents('.bdlms-qus-bank-modal')
						.addClass("searching")
						.find('.bdlms-qus-list-scroll li')
						.each(function(i, e) {
							var text = jQuery(e).find('label').text().toLowerCase();
							var matched = text.indexOf(searchKeyword.toLowerCase());
							if ( matched >= 0 ) {
								$(e).removeClass('hidden');
								return;
							}
							$(e).addClass('hidden');
						})
						.parent('.bdlms-qus-list-scroll')
						.after(function() {
							$(this).next('p').remove();
							if( 0 === $(this).find('li:not(.hidden)').length ) {
								return '<p>' + lessonObject?.i18n.emptySearchResult + '</p>';
							}
							return '';
						})
						.parents('.bdlms-qus-bank-modal')
						.removeClass("searching")
						.find('.ui-autocomplete-loading')
						.removeClass('ui-autocomplete-loading');
					}, 500));
				});
			},
			/**
			 * Init text editor.
			 */
			textEditorInit: function(editorId) {
				wp.editor.initialize(
					editorId,
					{ 
						tinymce: {
							wpautop: true,
							plugins : 'charmap colorpicker hr lists paste tabfocus textcolor fullscreen wordpress wpautoresize wpeditimage wpemoji wpgallery wplink wptextpattern',
							toolbar1: 'formatselect,bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,wp_more,spellchecker,fullscreen,wp_adv,listbuttons',
							toolbar2: 'styleselect,strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help',
							textarea_rows : 15
						},
						quicktags: {
							buttons: 'strong,em,link,block,del,ins,img,ul,ol,li,code,more,close'
						},
						mediaButtons: true,
					}
				);
			},

			/**
			 * Remove text editor.
			 *
			 * @param string editorId 
			 */
			removetextEditor: function(editorId) {
				wp.editor.remove(editorId);
			},

			/**
			 * Add media.
			 */
			addMedia: function() {
				var _this = this;
				// Change media type.
				$(document).on('change', '.media-type-select input:radio', function() {
					var mediaType = $(this).val();
					if ( 'text' === mediaType ) {
						$('.bdlms-video-type-box').addClass('hidden');
						$('.lesson-media-editor').removeClass('hidden');
						if ( ! _this.editorLoaded ) {
							_this.editorLoaded = true;
							_this.textEditorInit('media_text_editor');
						}
						return;
					}
					_this.editorLoaded = false;
					_this.removetextEditor('media_text_editor');
					$('.lesson-media-editor').addClass('hidden');
					$('.bdlms-video-type-box').removeClass('hidden');
				});
				$('.media-type-select input:radio:checked').trigger('change');

				// On upload button click.
				$( 'body' ).on( 'click', '.bdlms-open-media', function( e ) {
					e.preventDefault();
					var libraryType = $(this).attr('data-library_type');
					var button = $( this );

					var wp_media_uploader = wp.media( {
						state: 'customState',
						states: [
							new wp.media.controller.Library({
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
						var buttonText = lessonObject.i18n.emptyMediaButtonTitle;
						var mediaName  = lessonObject.i18n.nullMediaMessage;
						if ( wp_media_uploader.state().get( 'selection' ).length ) {
							attachment = wp_media_uploader.state().get( 'selection' ).first().toJSON();
							var attachmentUrl = attachment.url;
							mediaName = '<a href="' + attachmentUrl + '" target="_blank">' + attachmentUrl.split('/').pop() + '</a>';
							buttonText = lessonObject.i18n.MediaButtonTitle;
					  	}
						button
						.text(buttonText)
						.parent()
						.find('span.bdlms-media-name')
						.html(mediaName);
						button.parent().find( 'input:hidden' ).val( attachment.id ).trigger( 'change' );
						wp_media_uploader.close();
					} )
					.on( 'selection:toggle', function() {
						$(wp_media_uploader?.el)
						.find('button.media-button-select')
						.removeAttr('disabled');
					} );
					
					$(document).on( 'click', '.media-button-select', function() {
						wp_media_uploader.trigger('select');
					} );

					wp_media_uploader.on( 'open', function() {
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
			
			/**
			 * Rename input name.
			 */
			inputRename: function () {
				$('.bdlms-materials-item').each(function(index, item) {
					$(item).find('input, select, textarea').attr('name', function( i, val ) {
						val = val.replace(/\[([0-9]+)\]/g, '[' + index + ']');
						return val;
					});
				});
			},

			/**
			 * Handle materials.
			 */
			handleMaterials: function() {
				var _this = this;
				$(document).on('click', '.bdlms-materials-box__footer button', function(e) {
					var tmpl = $('#materials_item_tmpl').html();
					var parentElement = $(this).parents('.bdlms-materials-box').find('.bdlms-materials-box__body');
					$(tmpl).appendTo(parentElement);
					_this.inputRename();
					e.preventDefault();
				});
				$(document).on('change', '.material-type select', function() {
					var type = $(this).val();
					var parentElement = $(this).parents('.bdlms-materials-item');
					if ( 'external' === type ) {
						$('[data-media_type="choose_file"]', parentElement).addClass('hidden');
						$('[data-media_type="file_url"]', parentElement).removeClass('hidden');
						return;
					}
					$('[data-media_type="file_url"]', parentElement).addClass('hidden');
					$('[data-media_type="choose_file"]', parentElement).removeClass('hidden');
				});
				$(document).on('click', 'button.bdlms-remove-material', function(e) {
					$(this)
					.parents('.bdlms-materials-item')
					.remove();
					e.preventDefault();
				});
			}
		};
		$(function () {
			lessonModule.init();
		});
} )(jQuery, window.wp);
