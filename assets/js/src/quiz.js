/**
 * This file contains the functions needed for handle quiz module.
 *
 * @since 1.0.0
 * @output assets/js/quiz.js
 */

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
			 * Initializes the inline editor.
			 */
			init: function () {
				var _this = this;
				_this.dialogInit();

				// Inline quick edit.
				$(document).on("click", ".button-link.editinline", function () {
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
				});

				$(document).on("click", "[data-accordion]", function () {
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
				});
			},

			/**
			 * Dialog box.
			 */
			dialogInit: function () {
				$("#add_new_question").dialog({
					title: "From where you want to add a new Question?",
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

				$("#questions_bank").dialog({
					title: "Questions Bank",
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

				$(document).on("click", ".add-new-question", function () {
					$("#add_new_question").dialog("open");
				});
				$(document).on("click", ".open-questions-bank", function () {
					$("#questions_bank").dialog("open");
				});
			},
		};
		$(function () {
			quizModule.init();
		});
	}
)(jQuery, window.wp);
