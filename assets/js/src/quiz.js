/* global quizModules, quizModule, questionObject */
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
				$( '.stlms-snackbar-notice' ).find('p').html(message);
				$( '.stlms-snackbar-notice' ).toggleClass( 'open', 1000 );
				if ( $( '.stlms-snackbar-notice' ).hasClass( 'open' ) ) {
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
				$( document ).on( 'change', '.stlms-answer-type select', function() {
					var type = $( this ).val();
					var questionGroup = $(this).parents('li');
					var questionBox = $( '.' + type, questionGroup );
					if ( 'true_or_false' === type ) {
						$( '.stlms-answer-wrap .stlms-add-option', questionGroup ).addClass( 'hidden' );
					} else {
						$( '.stlms-answer-wrap .stlms-add-option', questionGroup ).removeClass( 'hidden' );
					}
					$( '.stlms-answer-group', questionGroup ).addClass( 'hidden' );
					questionBox.removeClass( 'hidden' );
				} );

				// Inline quick edit.
				$(document).on("click", ".button-link.editinline", function (e) {
					e.preventDefault();
					var  currentRow = $(this).parents('tr');
					var editRow = currentRow.next('tr.hidden').next('tr.inline-edit-row');
					$(".inline-edit-private", editRow).parents("div.inline-edit-group").remove();
					var rightCustomBox = $(".inline-edit-col-right:not(.inline-edit-quiz):visible", editRow);
					var passingMarks = $("td.passing_marks.column-passing_marks", currentRow).text();
					rightCustomBox.remove();
					$(".inline-edit-quiz-item.stlms-passing-marks:visible input", editRow ).val(passingMarks);
					$( '.stlms-answer-type select' ).change();
				});

				$(document).on('click', '[data-accordion="true"]', function (e) {
					e.preventDefault();
					$(this)
						.parents(".stlms-quiz-qus-item")
						.find(".stlms-quiz-qus-toggle")
						.toggleClass("active");
					$(this)
						.parents(".stlms-quiz-qus-item")
						.toggleClass("active");
					$(this)
						.parents(".stlms-quiz-qus-item")
						.find(".stlms-quiz-qus-item__body")
						.slideToggle();
					
					$( '.stlms-answer-type select' ).change();
				});

				$(document).on('click', '.stlms-cancel-edit', function(e) {
					e.preventDefault();
					$(this)
					.parents('.stlms-quiz-qus-item__body')
					.slideToggle();	
				} );

				$(document).on('click', '.stlms-save-questions', function(e) {
					e.preventDefault();
					var saveButton = $(this);
					var postId = saveButton.attr('data-post_id') || 0;
					var parentGroup = saveButton.parents('.stlms-quiz-qus-item__body');
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
							value: 'stlms_quiz_question'
						},
						{
							name: 'stlms_nonce',
							value: quizModules.nonce
						}
					);

					saveButton
					.attr('disabled', true)
					.parent('.stlms-add-option')
					.find('span.spinner')
					.addClass('is-active');

					$.post(
						quizModules.ajaxurl,
						formData,
						function( res ) {
							saveButton
							.removeAttr('disabled')
							.parent('.stlms-add-option')
							.find('span.spinner')
							.removeClass('is-active');

							_this.snackbarNotice(res.message);
						},
						'json'
					);
				} );

				// Delete project.
				$( document ).on( 'click', '.stlms-delete-link', this.deleteProject );
				// Insert `Add More Question` button.
				$(quizModules.addMoreButton).insertAfter('#quiz-questions h2.ui-sortable-handle');
				// Click to duplicate.
				$( document ).on( 'click', '.stlms-duplicate-link:not(.in-queue)', this.duplicateProject );
			},

			/**
			 * Dialog box.
			 */
			dialogInit: function () {
				$('#add_new_question').dialog({
					title: quizModules.i18n.addNewPopupTitle,
					dialogClass: 'wp-dialog stlms-modal',
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
					dialogClass: 'wp-dialog stlms-modal',
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
					open: function (event, ui) {
						$('#stlms_qus_list').load(
							quizModules.contentLoadUrl + ' #stlms_qus_list > *',
							{
								fetch_question: 1,
								questionIds: function() {
									return $('input.stlms-qid').map(function() {
										return $(this).val();
									}).get();
								}
							},
							function () {
								$('.stlms-choose-existing').trigger('change');
							}
						);
					},
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
				$(document).on('change', '.stlms-choose-existing', function() {
					var totalChecked = $('.stlms-choose-existing:checked');
					$(this)
					.parents('.stlms-qus-bank-modal')
					.find('.stlms-add-question')
					.attr('disabled', function() {
						return totalChecked.length === 0;
					})
					.next('.stlms-qus-selected')
					.text( function(i,txt) {
						return txt.replace(/\d+/, totalChecked.length);
					} );
				});
				$(document).on('click', '.stlms-add-question, .create-your-own', function(e) {
					var _btn = $(this);
					var qIds = $('.stlms-choose-existing:checked:not(:disabled)').map(function() {
						return $(this).val();
					}).get();
					
					var actionType = _btn.hasClass('create-your-own') ? 'create_new' : 'update_existing';
					if ( 'update_existing' === actionType && qIds.length === 0 ) {
						$('#questions_bank').dialog('close');
						return;
					}

					$('.stlms-choose-existing:visible').attr('disabled', true);
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
							action: 'stlms_add_new_question',
							stlms_nonce: quizModules.nonce,
							selected: qIds,
							_action: _btn.hasClass('create-your-own') ? 'create_new' : 'update_existing',
						},
						function(data) {
							$('.stlms-choose-existing:visible').removeAttr('disabled');
							_btn
							.parent('div')
							.find('span.spinner')
							.removeClass('is-active')
							.parent('div')
							.find('button')
							.removeAttr('disabled');
							$('#questions_bank, #add_new_question').dialog('close');
							if ( '' !== data.html ) {
								$(data.html).appendTo('ul.stlms-quiz-qus-list');
								if ( _btn.hasClass('create-your-own') ) {
									$('ul.stlms-quiz-qus-list > li:last').find('a[data-accordion]').trigger('click');
								}
								quizModule.snackbarNotice(data.message);
							}
						},
						'json'
					);
					e.preventDefault();
				});
				$(document).on('input', 'input.stlms-qus-bank-search', function () {
					var searchBox = $(this);
					var searchKeyword = searchBox.val();
					clearTimeout($.data(this, "timer"));
					$(this).data( 'timer', setTimeout(function() {
						searchBox
						.addClass("ui-autocomplete-loading")
						.parents('.stlms-qus-bank-modal')
						.addClass("searching")
						.find('.stlms-qus-list-scroll li')
						.each(function(i, e) {
							var text = jQuery(e).find('label').text().toLowerCase();
							var matched = text.indexOf(searchKeyword.toLowerCase());
							if ( matched >= 0 ) {
								$(e).removeClass('hidden');
								return;
							}
							$(e).addClass('hidden');
						})
						.parent('.stlms-qus-list-scroll')
						.after(function() {
							$(this).next('p').remove();
							if( 0 === $(this).find('li:not(.hidden)').length ) {
								return  '<p>' + questionObject?.i18n.emptySearchResult + '</p>';
							}
							return '';
						})
						.parents('.stlms-qus-bank-modal')
						.removeClass("searching")
						.find('.ui-autocomplete-loading')
						.removeClass('ui-autocomplete-loading');
					}, 500));
				});
			},

			/**
			 * Live preview.
			 */
			livePreview: function() {
				$( document ).on('input', '.stlms-quiz-name input:text', function(e) {
					var updatedVal = $(this).val();
					$(this)
					.parents('li')
					.find('.stlms-quiz-qus-name span:not(.stlms-quiz-qus-point)')
					.text(updatedVal);
					e.preventDefault();
				} );

				$( document ).on('input', '.stlms-question-points', function(e) {
					var updatedVal = $(this).val();
					var previewElement = $(this)
					.parents('li')
					.find('.stlms-quiz-qus-name span.stlms-quiz-qus-point');
					
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
				$( '.stlms-quiz-qus-list.stlms-sortable-answers', document ).sortable( {
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
				var postId = newItem.find('input.stlms-qid').val();
				
				cloneButton.addClass('in-queue');
				$.post(
					quizModules.ajaxurl,
					{
						action: 'stlms_inline_duplicate_question',
						post: postId,
						stlms_nonce: quizModules.nonce,
						post_status: 'auto-draft'
					},
					function(res) {
						if ( res.status ) {
							newItem.find('input.stlms-qid').val(res.post_id);
							newItem.find('.stlms-save-questions').attr('data-post_id', res.post_id);
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
