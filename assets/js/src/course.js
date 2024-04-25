/**
 * This file contains the functions needed for handle course module.
 *
 * @since 1.0.0
 * @output assets/js/course.js
 */

window.wp = window.wp || {};

/**
 * Manages the quick edit and bulk edit windows for editing posts or pages.
 *
 * @namespace courseModule
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

	window.courseModule = {
		/**
		 * Initializes
		 */
		init: function() {
			this.tabs();
			this.dialogInit();
			this.initSortable();
			this.addMoreSettingItem();
			this.addMedia();
			this.handleMaterials();
			this.handleCurriculum();
			this.countCurriculum();
		},
		/**
		 * Custom tabs.
		 */
		tabs: function() {
			const tabs = document.querySelectorAll('.bdlms-tab');
			const tabContents =
			document.querySelectorAll('.bdlms-tab-content');

			tabs.forEach((tab) => {
				tab.addEventListener('click', function () {
					const tabId = this.getAttribute('data-tab');

					// Hide all tab contents
					tabContents.forEach((content) => {
						content.classList.remove('active');
					});

					// Remove active class from all tabs
					tabs.forEach((t) => {
						t.classList.remove('active');
					});

					// Show the corresponding tab content with fade effect
					const tabContent = document.querySelector(
						`.bdlms-tab-content[data-tab='${tabId}']`,
					);
					if (tabContent) {
						tabContent.classList.add('active');
					}
					// Add active class to clicked tab
					this.classList.add('active');
				});
			});

			$(document).on('click', '[data-accordion="true"]', function (e) {
				e.preventDefault();
				$(this)
					.parents('.bdlms-quiz-qus-item')
					.find('.bdlms-quiz-qus-toggle')
					.toggleClass('active');
				$(this)
					.parents('.bdlms-quiz-qus-item')
					.toggleClass('active');
				$(this)
					.parents('.bdlms-quiz-qus-item')
					.find('.bdlms-quiz-qus-item__body')
					.slideToggle();
				
				$( '.bdlms-answer-type select' ).change();
			});
			$(document).on('click', '.bdlms-curriculum-dd-button', function (e) {
				e.preventDefault();
				$(this).next('ul').slideToggle();
			});

			// trigger events.
			$(document).on('click', '.bdlms-quiz-qus-item__footer .bdlms-delete-link', this.removeSection);
			$(document).on('click', '.bdlms-curriculum-item .curriculum-remove-item', this.removeSectionItem);
			$(document).on('click', '.bdlms-course-settings .bdlms-cs-action a', this.removeSettingItem);
		},
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
		 * Init sortable.
		 */
		initSortable: function (obj) {
			var _this = obj;
			// Drag curriculum section.
			$('ul.bdlms-quiz-qus-list', document)
			.sortable({
				appendTo: 'parent',
				axis: 'y',
				containment: 'parent',
				items: 'li',
				handle: '.bdlms-options-drag',
				placeholder: 'sortable-placeholder',
				forcePlaceholderSize: true,
				stop: function () {
					// _this.reorderAnswer();
				},
			})
			.disableSelection();

			// Drag curriculum section list item.
			$('div.bdlms-curriculum-item-list', document)
			.sortable({
				appendTo: 'parent',
				axis: 'y',
				containment: 'parent',
				items: '.bdlms-curriculum-item:not(:last)',
				handle: '.bdlms-curriculum-item-drag',
				placeholder: 'sortable-placeholder',
				forcePlaceholderSize: true,
				stop: function () {
					// _this.reorderAnswer();
				},
			})
			.disableSelection();

			// Drag settings item.
			$('ul.cs-drag-list:not(.cs-no-drag)', document)
			.sortable({
				appendTo: 'parent',
				axis: 'y',
				containment: 'parent',
				items: 'li',
				handle: '.bdlms-options-drag',
				placeholder: 'sortable-placeholder',
				forcePlaceholderSize: true,
				stop: function () {
					// _this.reorderAnswer();
				},
			})
			.disableSelection();
		},
		removeSection: function() {
			$(this)
			.parents('ul.bdlms-quiz-qus-list')
			.remove();
		},
		removeSectionItem: function() {
			$(this)
			.parents('.bdlms-curriculum-item')
			.remove();
		},
		removeSettingItem: function() {
			var parentGroup = $(this).parents('ul.cs-drag-list-group');
			$(this)
			.parents('li')
			.remove();

			console.log(parentGroup?.find('> li').length);
			if ( parentGroup?.find('> li').length === 1 ) {
				parentGroup
				.find('li')
				.find('.bdlms-cs-action')
				.addClass('hidden');
			}
		},
		addMoreSettingItem: function() {
			$('button[data-add_more="true"]', document).on('click', function(e) {
				var newItem = $(this)
				.parents('.bdlms-cs-row')
				.find('.bdlms-cs-action')
				.removeClass('hidden')
				.parents('ul.cs-drag-list-group')
				.find('> li:last')
				.clone(true);
				// Clean fields.
				$(newItem).find('input, textarea')
				.val('')
				.removeAttr('value');
				// Append new item.
				$(this).prev('ul.cs-drag-list-group').append(newItem);
				e.preventDefault();
			});
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
				var allowedExt = $(this).attr('data-ext');
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
					var buttonText = courseObject.i18n.emptyMediaButtonTitle;
					var mediaName  = courseObject.i18n.nullMediaMessage;
					if ( wp_media_uploader.state().get( 'selection' ).length ) {
						attachment = wp_media_uploader.state().get( 'selection' ).first().toJSON();
						var attachmentUrl = attachment.url;
						mediaName = '<a href="' + attachmentUrl + '" target="_blank">' + attachmentUrl.split('/').pop() + '</a>';
						buttonText = courseObject.i18n.MediaButtonTitle;
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
				var parentElement = $(this).parents('.bdlms-materials-box').find('.bdlms-materials-box__body .bdlms-materials-list');
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
			$(document).on('click', 'button.bdlms-remove-material, a.bdlms-delete-link', function(e) {
				$(this)
				.parents('.bdlms-materials-list-item')
				.remove();
				e.preventDefault();
			});
			// Edit Material
			$(document).on('click', '.bdlms-materials-list-action .edit-material', function(e) {
				$('.bdlms-materials-list-item:not(.material-add-new)').find('.bdlms-save-material').trigger('click');
				$(this)
				.parents('ul')
				.addClass('hidden')
				.parent('.bdlms-materials-list-item')
				.find('.bdlms-materials-item')
				.removeClass('hidden');
				e.preventDefault();
			} );

			// Save Material
			$(document).on('click', '.bdlms-save-material', function(e) {
				var parentElement = $(this).parents('.bdlms-materials-list-item');
				var fileTitle = $('input.material-file-title', parentElement ).val();
				var typeText = $('option:selected', $(parentElement).find('.material-type select') ).text();
				parentElement
				.find('li.assignment-type')
				.text(typeText)
				.parent('ul')
				.find('li.assignment-title')
				.text(fileTitle)
				.parents('.bdlms-materials-list-item')
				.removeClass('material-add-new')
				.find('ul.hidden')
				.removeClass('hidden')
				.next('.bdlms-materials-item')
				.addClass('hidden');
				e.preventDefault();
			} );
		},
		handleCurriculum: function() {
			var _this = this;
			$(document).on('click', '.curriculum-edit-item', function(e){
				e.preventDefault();
				$(this)
				.parents('.bdlms-curriculum-item-action')
				.prev('input:text')
				.attr('readonly', function(index, attr){
					return 'readonly' === attr ? null : 'readonly';
				})
				.focus();
			});
			$(document).on('click', '.curriculum-toggle-item', function(e){
				e.preventDefault();
				$(this)
				.parents('.bdlms-curriculum-item')
				.find('.bdlms-curriculum-dd')
				.toggleClass('is-hide');
			});
			$('.bdlms-curriculum-item-name').keydown(function(e){ 
				var id = e.which || 0;
				if (id == 13) {
					_this.createNewCurriculum(this);
					e.preventDefault();
					return false;
				}
			});
			$(document).on('click', '.bdlms-curriculum-item .icon.plus-icon', function(e){
				_this.createNewCurriculum(this);
				e.preventDefault();
			});
			$(document).on('click', '.bdlms-curriculum-type li', function(e){
				var selectedType = $(this).attr('data-type');
				var iconSelector = '.icon.' + selectedType + '-icon';

				$(this)
				.parents('.bdlms-curriculum-dd')
				.find('.bdlms-curriculum-dd-button')
				.find(iconSelector)
				.removeClass('hidden');

				$(this)
				.addClass('active')
				.parents('.bdlms-curriculum-dd')
				.find('.bdlms-curriculum-dd-button')
				.find('.icon:not(.down-arrow-icon)')
				.not(iconSelector)
				.addClass('hidden')
				.parents('.bdlms-curriculum-dd')
				.find('.bdlms-curriculum-type')
				.hide()
				.find('li.active')
				.not(this)
				.removeClass('active');
				e.preventDefault();
			});
		},
		createNewCurriculum: function(element) {
			var currentItem = $(element).parents('.bdlms-curriculum-item');
			if ( '' === currentItem.find('.bdlms-curriculum-item-name').val().trim() ) {
				return;
			}
			newItemHtml = currentItem.clone();
			// Clear input data.
			currentItem.find('.bdlms-curriculum-item-name').val('');
			currentItem.find('.bdlms-curriculum-type li.active').removeClass('active');
			currentItem.find('.bdlms-curriculum-type li:first').trigger('click');
			// Insert new item.
			$(newItemHtml).find('.bdlms-curriculum-item-name').attr('readonly', true);
			$(newItemHtml).find('.bdlms-curriculum-item-drag').find('.plus-icon').addClass('hidden');
			$(newItemHtml).find('.bdlms-curriculum-item-drag').find('.drag-icon').removeClass('hidden');
			$(newItemHtml).find('.bdlms-curriculum-item-action.hidden').removeClass('hidden');
			$(newItemHtml).insertBefore(currentItem);
			console.log( newItemHtml );
		},
		countCurriculum: function() {
			var totalLesson = 0;
			$('.bdlms-quiz-qus-item').each(function(){
				var headerPoints = $(this).find('.bdlms-quiz-qus-point');
				var selectedType = $(this).find('.bdlms-curriculum-type li.active').attr('data-type');
				if ( 'lesson' === selectedType ) {
					totalLesson++;
					// Update count.
					headerPoints
					.find('.' + selectedType + '-count')
					.text(totalLesson);
				}

				console.log(headerPoints, selectedType, totalLesson );
			});
		},
		/**
		 * Dialog box.
		 */
		dialogInit: function () {

		}
	};
	$(function () {
		courseModule.init();
	});
} )(jQuery, window.wp);
