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
			},

			/**
			 * Dialog box.
			 */
			dialogInit: function () {
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
					open: function (event, ui) {},
					create: function () {},
				});
			},
			addMedia: function() {
				$(document).on('change', '.media-type-select input:radio', function() {
					var mediaType = $(this).val();
					var editorId = $(this).parents('#add-media').find('.wp-lesson_media-wrap');
					console.log(editorId);
					if ( 'text' === mediaType ) {
						$('.bdlms-video-type-box').addClass('hidden');
						$('.lesson-media-editor').removeClass('hidden');
						return;
					}
					$('.lesson-media-editor').addClass('hidden');
					$('.bdlms-video-type-box').removeClass('hidden');
				});
				$('.media-type-select input:radio:checked').trigger('change');
			},
			handleMaterials: function() {
				$(document).on('click', '.bdlms-materials-box__footer button', function(e) {
					var tmpl = $('#materials_item_tmpl').html();
					var parentElement = $(this).parents('.bdlms-materials-box').find('.bdlms-materials-box__body');
					$(tmpl).appendTo(parentElement);
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
