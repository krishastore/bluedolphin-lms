/**
 * This file contains the functions needed for handle quiz module.
 *
 * @since 1.0.0
 * @output assets/js/quiz.js
 */

import './questions.js';

window.wp = window.wp || {};

/**
 * Manages the quick edit and bulk edit windows for editing posts or pages.
 *
 * @namespace quizModule
 *
 * @since 1.0.0
 *
 * @type {Object}
 *
 * @property {string} type The type of inline editor.
 * @property {string} what The prefix before the post ID.
 *
 */
(
	function ($, wp) {
		window.quizModule = {
			
			/**
			 * Snackbar notice.
			 */
			snackbarNotice : function( message ) {
				var _t = this;
				$( '.bdlms-snackbar-notice' ).find('p').html(message);
				$( '.bdlms-snackbar-notice' ).toggleClass( 'open', 1000 );
				if ( $( '.bdlms-snackbar-notice' ).hasClass( 'open' ) ) {
					setTimeout( function() {
						_t.snackbarNotice('');
					}, 3000 );
				}
  			},

			/**
			 * Initializes the inline editor.
			 */
			init: function () {
				var _this = this;
				_this.dialogInit();
				_this.livePreview();
				_this.initSortable(_this);

				// Show / Hide answers.
				$( document ).on( 'change', '.bdlms-answer-type select', function() {
					var type = $( this ).val();
					var questionGroup = $(this).parents('li');
					var questionBox = $( '.' + type, questionGroup );
					if ( 'true_or_false' === type ) {
						$( '.bdlms-answer-wrap .bdlms-add-option', questionGroup ).addClass( 'hidden' );
					} else {
						$( '.bdlms-answer-wrap .bdlms-add-option', questionGroup ).removeClass( 'hidden' );
					}
					$( '.bdlms-answer-group', questionGroup ).addClass( 'hidden' );
					questionBox.removeClass( 'hidden' );
				} );

				// Inline quick edit.
				$(document).on("click", ".button-link.editinline", function (e) {
					e.preventDefault();
					$(".inline-edit-private")
						.parents("div.inline-edit-group")
						.remove();
					var rightCustomBox = $(
						".inline-edit-col-right:not(.inline-edit-quiz):visible",
					);
					var passingMarks = $(
						"td.passing_marks.column-passing_marks",
					).text();
					rightCustomBox.remove();
					$(
						".inline-edit-quiz-item.bdlms-passing-marks:visible input",
						document,
					).val(passingMarks);
					
					$( '.bdlms-answer-type select' ).change();
				});

				$(document).on('click', '[data-accordion="true"]', function (e) {
					e.preventDefault();
					$(this)
						.parents(".bdlms-quiz-qus-item")
						.find(".bdlms-quiz-qus-toggle")
						.toggleClass("active");
					$(this)
						.parents(".bdlms-quiz-qus-item")
						.toggleClass("active");
					$(this)
						.parents(".bdlms-quiz-qus-item")
						.find(".bdlms-quiz-qus-item__body")
						.slideToggle();
					
					$( '.bdlms-answer-type select' ).change();
				});

				$(document).on('click', '.bdlms-cancel-edit', function(e) {
					e.preventDefault();
					$(this)
					.parents('.bdlms-quiz-qus-item__body')
					.slideToggle();	
				} );

				$(document).on('click', '.bdlms-save-questions', function(e) {
					e.preventDefault();
					var saveButton = $(this);
					var postId = saveButton.attr('data-post_id') || 0;
					var parentGroup = saveButton.parents('.bdlms-quiz-qus-item__body');
					var formData = $('input:visible, select:visible, textarea:visible', parentGroup ).serializeArray();
					formData = formData.filter(function(obj) {
						obj.name = obj.name.replace(/[0-9]/g, '').replace( '[]', '');
						return obj;
					});
					formData.push(
						{
							name: 'post_id',
							value: postId
						},
						{
							name: 'action',
							value: 'bdlms_quiz_question'
						},
						{
							name: 'bdlms_nonce',
							value: quizModules.nonce
						}
					);

					saveButton
					.attr('disabled', true)
					.parent('.bdlms-add-option')
					.find('span.spinner')
					.addClass('is-active');

					$.post(
						quizModules.ajaxurl,
						formData,
						function( res ) {
							saveButton
							.removeAttr('disabled')
							.parent('.bdlms-add-option')
							.find('span.spinner')
							.removeClass('is-active');

							_this.snackbarNotice(res.message);
						},
						'json'
					);
				} );

				// Delete project.
				$( document ).on( 'click', '.bdlms-delete-link', this.deleteProject );
				// Insert `Add More Question` button.
				$(quizModules.addMoreButton).insertAfter('#quiz-questions h2.ui-sortable-handle');
				// Click to duplicate.
				$( document ).on( 'click', '.bdlms-duplicate-link:not(.in-queue)', this.duplicateProject );
			},

			/**
			 * Dialog box.
			 */
			dialogInit: function () {
				$('#add_new_question').dialog({
					title: quizModules.i18n.addNewPopupTitle,
					dialogClass: 'wp-dialog bdlms-modal',
					autoOpen: false,
					draggable: false,
					width: 'auto',
					modal: true,
					resizable: false,
					closeOnEscape: true,
					position: {
						my: 'center',
						at: 'center',
						of: window,
					},
					open: function (event, ui) {},
					create: function () {},
				});

				$('#questions_bank').dialog({
					title: quizModules.i18n.existingPopupTitle,
					dialogClass: 'wp-dialog bdlms-modal',
					autoOpen: false,
					draggable: false,
					width: 'auto',
					modal: true,
					resizable: false,
					closeOnEscape: true,
					position: {
						my: 'center',
						at: 'center',
						of: window,
					},
					open: function (event, ui) {},
					create: function () {},
				});

				$(document).on("click", ".add-new-question", function(e) {
					e.preventDefault();
					$('#add_new_question').dialog('open');
				});
				$(document).on("click", ".open-questions-bank", function (e) {
					e.preventDefault();
					$('#add_new_question').dialog('close');
					$('#questions_bank').dialog('open');
				});
				$(document).on('change', '.bdlms-choose-existing', function() {
					var totalChecked = $('.bdlms-choose-existing:checked');
					$(this)
					.parents('.bdlms-qus-bank-modal')
					.find('.bdlms-add-question')
					.attr('disabled', function() {
						return totalChecked.length === 0;
					})
					.next('.bdlms-qus-selected')
					.text( function(i,txt) {
						return txt.replace(/\d+/, totalChecked.length);
					} );
				});
				$(document).on('click', '.bdlms-add-question, .create-your-own', function(e) {
					var _btn = $(this);
					var qIds = $('.bdlms-choose-existing:checked').map(function() {
						return $(this).val();
					}).get();
					
					$('.bdlms-choose-existing:visible').attr('disabled', true);
					_btn
					.parent('div')
					.find('span.spinner')
					.addClass('is-active')
					.parent('div')
					.find('button')
					.attr('disabled', true);

					$.post(
						quizModules.ajaxurl,
						{
							action: 'bdlms_add_new_question',
							bdlms_nonce: quizModules.nonce,
							selected: qIds,
							_action: _btn.hasClass('create-your-own') ? 'create_new' : 'update_existing',
						},
						function(data) {
							$('.bdlms-choose-existing:visible').removeAttr('disabled');
							_btn
							.parent('div')
							.find('span.spinner')
							.removeClass('is-active')
							.parent('div')
							.removeAttr('disabled');
							$('#questions_bank, #add_new_question').dialog('close');
							if ( '' !== data.html ) {
								$(data.html).appendTo('ul.bdlms-quiz-qus-list');
								if ( _btn.hasClass('create-your-own') ) {
									$('ul.bdlms-quiz-qus-list > li:last').find('a[data-accordion]').trigger('click');
								}
								quizModule.snackbarNotice(data.message);
							}
						},
						'json'
					);
					e.preventDefault();
				});
				$(document).on('input', 'input.bdlms-qus-bank-search', function() {
					var searchBox = $(this);
					// Debounce the event callback while users are typing.
					clearTimeout( $.data( this, 'timer' ) );
					$( this ).data( 'timer', setTimeout( function() {
						searchBox
						.addClass('ui-autocomplete-loading')
						.parent()
						.addClass('searching');

						$('#bdlms_qus_list').load(
							quizModules.searchActionUrl + ' #bdlms_qus_list > *',
							{
								s: searchBox.val()
							},
							function() {
							searchBox
							.removeClass('ui-autocomplete-loading')
							.parent()
							.removeClass('searching');
							}
						);
					}, 500 ) );
				});
			},

			/**
			 * Live preview.
			 */
			livePreview: function() {
				$( document ).on('input', '.bdlms-quiz-name input:text', function(e) {
					var updatedVal = $(this).val();
					$(this)
					.parents('li')
					.find('.bdlms-quiz-qus-name span:not(.bdlms-quiz-qus-point)')
					.text(updatedVal);
					e.preventDefault();
				} );

				$( document ).on('input', '.bdlms-question-points', function(e) {
					var updatedVal = $(this).val();
					var previewElement = $(this)
					.parents('li')
					.find('.bdlms-quiz-qus-name span.bdlms-quiz-qus-point');
					
					previewElement.text( function(i,txt) {
						return txt.replace(/\d+/, updatedVal);
					} );
					e.preventDefault();
				} );
			},

			/**
			 * Delete question.
			 */
			deleteProject: function(e) {
				e.preventDefault();
				$(this).parents('li').remove();
			},

			/**
             * Init sortable.
             */
			initSortable: function(obj) {
				var _this = obj;
				$( '.bdlms-quiz-qus-list.bdlms-sortable-answers', document ).sortable( {
					appendTo: 'parent',
					axis: 'y',
					containment: 'parent',
					items: 'li',
					placeholder: "sortable-placeholder",
					forcePlaceholderSize: true,
					stop: function () {}
				} ).disableSelection();
			},

			/**
             * Duplicate project.
             */
			duplicateProject: function(e) {
				e.preventDefault();
				var cloneButton = $(this);
				var newItem = cloneButton.parents('li').clone(true);
				var postId = newItem.find('input.bdlms-qid').val();
				
				cloneButton.addClass('in-queue');
				$.post(
					quizModules.ajaxurl,
					{
						action: 'bdlms_inline_duplicate_question',
						post: postId,
						bdlms_nonce: quizModules.nonce,
						post_status: 'auto-draft'
					},
					function(res) {
						if ( res.status ) {
							newItem.find('input.bdlms-qid').val(res.post_id);
							newItem.find('.bdlms-save-questions').attr('data-post_id', res.post_id);
							newItem.find('input, select, textarea').attr('name', function( i, val ) {
								val = val.replace(/\[([0-9]+)\]/g, '[' + res.post_id + ']');
								return val;
							});
							$(newItem).insertAfter(cloneButton.parents('li'));
							$(newItem).find('a[data-accordion]').trigger('click');
						}
						cloneButton.removeClass('in-queue');
						quizModule.snackbarNotice(res.message);
					},
					'json'
				);
			}
		};
		$(function () {
			quizModule.init();
		});
	}
)(jQuery, window.wp);
