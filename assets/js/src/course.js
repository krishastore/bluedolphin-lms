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
			this.addedItemsArray = [];
			this.tabs();
			this.dialogInit();
			this.initSortable();
			this.addMoreSettingItem();
			this.addMedia();
			this.handleMaterials();
			this.handleCurriculum();
		},
		/**
		 * Custom tabs.
		 */
		tabs: function() {
			const tabs = document.querySelectorAll('.bdlms-tab');

			tabs.forEach((tab) => {
				tab.addEventListener('click', function () {
					const tabContents = this.parentNode.parentNode.querySelectorAll('.bdlms-tab-content');
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
			$(document).on('click', '.add-new-section', this.addNewSection);
			$(document).on('change', '[data-tab="assessment"] input:radio', this.toggleAssessment );
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
					this.inputRename('.bdlms-quiz-qus-list > li');
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
				},
			})
			.disableSelection();
		},
		removeSection: function() {
			var parentItem = $(this).parents('li');
			var totalCurriculum = $('> li', $(this).parents('ul.bdlms-quiz-qus-list'));
			if ( totalCurriculum.length > 1 ) {
				parentItem.remove();
				courseModule.inputRename('.bdlms-quiz-qus-list > li');
				return;
			}
			courseModule.resetCurriculum(parentItem);
		},
		removeSectionItem: function() {
			var itemParent = $(this).parents('.bdlms-quiz-qus-item__body');
			$(this)
			.parents('.bdlms-curriculum-item')
			.remove();
			courseModule.countCurriculum(itemParent);
		},
		removeSettingItem: function() {
			var parentGroup = $(this).parents('ul.cs-drag-list-group');
			$(this)
			.parents('li')
			.remove();

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
						// Check the image dimensions.
						if ( ( attachment.width <= 220 && attachment.height <= 40 ) && ( courseObject.HasGdLibrary ) ) {
							var attachmentUrl = attachment.url;
							mediaName = '<a href="' + attachmentUrl + '" target="_blank">' + attachmentUrl.split('/').pop() + '</a>';
							buttonText = courseObject.i18n.MediaButtonTitle;
							button.parent().find( 'input:hidden' ).val( attachment.id ).trigger( 'change' );
						}else{
							wp_media_uploader.content.get().$el.find('.media-uploader-status .upload-errors').empty();
							
							var Message = courseObject.HasGdLibrary ? courseObject.i18n.uploadSizeMessage : courseObject.i18n.errorMediaMessage;
							var statusError = new wp.media.view.UploaderStatusError({
								message: Message
							});
							
							wp_media_uploader.content.get().$el.find('.media-uploader-status .upload-errors').append(statusError.render().el);
							$(document).find('.media-modal-content .media-frame .media-frame-content .media-sidebar .media-uploader-status').css('display', 'block');
							$(document).find('.media-modal-content .media-frame .media-frame-content .media-sidebar .media-uploader-status .upload-errors').css('display', 'block');
							$(document).find('.media-modal-content .media-frame .media-frame-content .media-sidebar .media-uploader-status .upload-dismiss-errors').css('display', 'block');
							
							// Reopen the media modal to keep it open.
							wp_media_uploader.open();
						}
					}
					button
					.text(buttonText)
					.parent()
					.find('span.bdlms-media-name')
					.html(mediaName);
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
		inputRename: function (selector) {
			$(selector).each(function(index, item) {
				$(item).find('input:not(.bdlms-curriculum-item-name), select, textarea').attr('name', function( i, val ) {
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
				_this.inputRename('.bdlms-materials-item');
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
				_this.inputRename('.bdlms-materials-item');
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
			$(document).on('keydown', '.bdlms-curriculum-item-name', function(e){ 
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
				.removeClass('hidden')

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
			var itemName = currentItem.find('.bdlms-curriculum-item-name').val().trim();
			if ( '' === itemName ) {
				return;
			}
			currentItem.addClass('searching').find('.bdlms-curriculum-item-name').addClass('ui-autocomplete-loading');

			$.post(
				courseObject.ajaxurl,
				{
					action: 'bdlms_create_course_curriculum',
					_nonce: courseObject.nonce,
					title: itemName,
					type: currentItem.find('.bdlms-curriculum-type li.active').attr('data-type')
				},
				function(response) {
					if ( response.post_id > 0 ) {
						currentItem
						.find('.bdlms-curriculum-item-name')
						.prev('input:hidden')
						.val(response.post_id)
						.parents('.bdlms-curriculum-item')
						.removeClass('searching')
						.find('.curriculum-view-item')
						.attr('href', response.view_link)
						.next('.curriculum-edit-item')
						.attr('href', response.edit_link);

						newItemHtml = currentItem.clone();
						// Clear input data.
						currentItem.find('.bdlms-curriculum-item-name').removeClass('ui-autocomplete-loading').val('');
						currentItem.find('.bdlms-curriculum-item-name').prev('input:hidden').val('');
						currentItem.find('.bdlms-curriculum-type li.active').removeClass('active');
						currentItem.find('.bdlms-curriculum-type li:first').trigger('click');
						// Insert new item.
						$(newItemHtml).find('.bdlms-curriculum-type').remove();
						$(newItemHtml).find('.bdlms-curriculum-dd-button .icon.hidden').remove();
						$(newItemHtml).find('.bdlms-curriculum-dd-button .icon.down-arrow-icon').remove();
						$(newItemHtml).find('.bdlms-curriculum-item-name').attr('readonly', true);
						$(newItemHtml).find('.bdlms-curriculum-item-drag').find('.plus-icon').addClass('hidden');
						$(newItemHtml).find('.bdlms-curriculum-item-drag').find('.drag-icon').removeClass('hidden');
						$(newItemHtml).find('.bdlms-curriculum-item-action.hidden').removeClass('hidden');
						$(newItemHtml).insertBefore(currentItem);
						courseModule.countCurriculum(currentItem);
						courseModule.snackbarNotice(response.message);
					}
				},
				'json'
			);
		},
		countCurriculum: function(element) {
			var totalLesson = 0, totalQuiz = 0;
			$('.bdlms-quiz-qus-item', element.parents('li')).find('.bdlms-curriculum-item:not(:last)').each(function(){
				var mainItemWrap = $(this).parents('.bdlms-quiz-qus-item');
				var headerPoints = mainItemWrap.find('.bdlms-quiz-qus-point');
				var selectedType = $(this).find('svg.quiz-icon:not(.hidden)').length ? 'quiz' : 'lesson';
				if ( 'lesson' === selectedType ) {
					totalLesson++;
					// Update count.
					headerPoints
					.find('.' + selectedType + '-count span')
					.text(totalLesson);
				}

				if ( 'quiz' === selectedType ) {
					totalQuiz++;
					// Update count.
					headerPoints
					.find('.' + selectedType + '-count span')
					.text(totalQuiz);
				}
			});
		},
		resetCurriculum: function(element) {
			$(element)
			.find('input, select, textarea')
			.val('')
			.removeAttr('value')
			.parents('li')
			.find('.bdlms-quiz-qus-point li span')
			.text('0')
			.parents('.bdlms-quiz-qus-item')
			.removeClass('active')
			.find('.bdlms-quiz-qus-item__body')
			.hide()
			.find('.bdlms-curriculum-item-list .bdlms-curriculum-item:not(:last)')
			.remove();
			courseModule.inputRename('.bdlms-quiz-qus-list > li');
		},
		addNewSection: function() {
			var cloneItem = $(this).parent('.bdlms-quiz-qus-footer').prev('ul.bdlms-quiz-qus-list').find('> li:last').clone();
			courseModule.resetCurriculum(cloneItem);
			$(this).parent('.bdlms-quiz-qus-footer').prev('ul.bdlms-quiz-qus-list').append(cloneItem);
			courseModule.inputRename('.bdlms-quiz-qus-list > li');
		},
		toggleAssessment: function() {
			if ( '2' === $(this).val() ) {
				$(this)
				.parents('.bdlms-tab-content.active')
				.find('.cs-passing-grade')
				.addClass('hidden');
				return;
			}
			$(this)
			.parents('.bdlms-tab-content.active')
			.find('.cs-passing-grade')
			.removeClass('hidden');
		},
		/**
		 * Dialog box.
		 */
		dialogInit: function () {
			$('#select_items').dialog({
				title: courseObject.i18n.PopupTitle,
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
					$('.bdlms-qus-bank-modal .bdlms-tab').removeClass('active');
					$('.bdlms-qus-bank-modal .bdlms-tab:first').click();
				},
				create: function () {
				},
				beforeClose: function() {
					$('.opened-item').removeClass('opened-item');
				}
			});
			// Select Items.
			$(document).on('click', '.select-items', function(e) {
				var parentElement = $(this).parents('.bdlms-quiz-qus-item.active').addClass('opened-item');
				window.courseModule.addedItemsArray = $('input:hidden[name^="_bdlms_course"]:not(:last)', parentElement)
				.map(
				function(){
					return $(this).val();
				})
				.get();
				$('#select_items').dialog('open');
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
							return  '<p>' + courseObject?.i18n.emptySearchResult + '</p>';
						}
						return '';
					})
					.parents('.bdlms-qus-bank-modal')
					.removeClass("searching")
					.find('.ui-autocomplete-loading')
					.removeClass('ui-autocomplete-loading');
				}, 500));
			});

			$(document).on('click', 'button[data-tab="assign-quiz-list"]', function() {
				var currentTab = $(this);
				currentTab
				.parents('.bdlms-qus-bank-modal')
				.addClass("searching")
				.find('.bdlms-add-item')
				.attr('disabled', function () {
					return true;
				});
				$('#curriculums_list').load(courseObject.contentLoadUrl + ' #curriculums_list > *',
					{
						fetch_items: 1,
						post_id: $('#post_ID').val(),
						type: currentTab.data('filter_type'),
						existing_items: window?.courseModule?.addedItemsArray || []
					},
					function () {
						currentTab
						.parents('.bdlms-qus-bank-modal')
						.removeClass('searching');
					},
				);
			} );

			$(document).on('change', '.bdlms-choose-item', function () {
				var totalChecked = $('input:checkbox:checked', $(this).parents('ul'));
				$(this)
				.parents('.bdlms-qus-bank-modal')
				.find('.bdlms-add-item')
				.attr('disabled', function () {
					return totalChecked.length === 0;;
				})
				.next('.bdlms-qus-selected')
				.text(function (i, txt) {
					return txt.replace(/\d+/, totalChecked.length);
				});
			});

			$(document).on('click', '.bdlms-add-item', function (e) {
				var _btn = $(this);
				$('.bdlms-choose-item:visible').attr('disabled', true);
				_btn
				.parent('div')
				.find('span.spinner')
				.addClass('is-active')
				.parent('div')
				.find('button')
				.attr('disabled', true);

				var selectType = $('.bdlms-qus-bank-modal .bdlms-tab.active').data('filter_type').replace('bdlms_', '');
				$('#curriculums_list ul li:not(.disabled-choose-item) .bdlms-choose-item:checked').each(function(index, element){
					var itemId = $(this).val();
					var itemText = $(this).next('label').text();
					// Insert new item.
					var lastItem = $('.opened-item .bdlms-curriculum-item:visible:last');
					var newItemHtml = lastItem.clone();
					$(newItemHtml)
					.find('.bdlms-curriculum-item-name')
					.val(itemText)
					.prev('input:hidden')
					.val(itemId)
					.parents('.bdlms-curriculum-item')
					.removeClass('searching')
					.find('.curriculum-view-item')
					.attr('href', '#link-view')
					.next('.curriculum-edit-item')
					.attr('href', '#link-edit');
					$(newItemHtml).find('.bdlms-curriculum-type').remove();
					$(newItemHtml).find('.bdlms-curriculum-dd-button .icon').removeClass('hidden');
					$(newItemHtml).find('.bdlms-curriculum-dd-button .icon:not(.' + selectType + '-icon)').remove();
					$(newItemHtml).find('.bdlms-curriculum-item-name').attr('readonly', true);
					$(newItemHtml).find('.bdlms-curriculum-item-drag').find('.plus-icon').addClass('hidden');
					$(newItemHtml).find('.bdlms-curriculum-item-drag').find('.drag-icon').removeClass('hidden');
					$(newItemHtml).find('.bdlms-curriculum-item-action.hidden').removeClass('hidden');
					$(newItemHtml).insertBefore(lastItem);
					courseModule.countCurriculum(lastItem);
				});
				courseModule.snackbarNotice(courseObject?.i18n?.itemAddedMessage.replace('%s', selectType));
				
				$('.bdlms-choose-item:visible').attr('disabled', false);
				$('#select_items').dialog('close');

				_btn
				.parent('div')
				.find('span.spinner')
				.removeClass('is-active')
				.parent('div')
				.find('button')
				.attr('disabled', false);

				e.preventDefault();
			});
		}
	};
	$(function () {
		courseModule.init();
	});
} )(jQuery, window.wp);
