/* global StlmsObject */
import smartWizard from 'smartwizard';

jQuery(function ($) {
	var showQuizResult = false;
	var wizardId = "#smartwizard";
	$(".stlms-prev-wizard").on("click", function () {
		// Navigate previous
		$(wizardId).smartWizard("prev");
		return true;
	});
	$(document).on( 'click', '.stlms-continue-course', function () {
		var nextPageLink = $('.stlms-next-btn').attr('href');
		window.location.href = nextPageLink;
		return false;
	});
	$(document).on( 'click', '.stlms-next-wizard', function () {
		// Navigate next.
		$(wizardId).smartWizard('next');
		return true;
	});
	$(wizardId).on("showStep", function (e, anchorObject, stepIndex, stepDirection, stepPosition) {
		if ( 'first' === stepPosition) {
			$(".stlms-lesson-view__footer,.stlms-quiz-header").addClass("hidden");
		} else {
			if ( ! showQuizResult ) {
				$(".stlms-lesson-view__footer,.stlms-quiz-header").removeClass("hidden");
			}
		}
		$('body').trigger('stlms:show:step', {currentStepIndex: stepIndex, currentStepPosition: stepPosition});
	});
	$(wizardId).on("leaveStep", function(e, anchorObject, currentStepIndex, nextStepIndex, stepDirection) {
		if(anchorObject.prevObject.length - 1 == nextStepIndex) {
			if ( showQuizResult ) {
				return true;
			}
			$('body').trigger('stlms:show:quizResult');
			$('.stlms-lesson-view__footer:visible button').attr('disabled', true);
			$(wizardId).smartWizard('loader', 'show');
	
			var inputField = $('.tab-content:visible').find('input:radio:checked, input:checkbox:checked, input:text');
			inputField
			.parents('.stlms-quiz-option-list, .stlms-quiz-input-ans')
			.css({opacity: 0.5, 'pointer-events': 'none' })

			var quizTime       = $('#stlms_quiz_countdown').data('timestamp');
			var totalQuestions = $('#stlms_quiz_countdown').data('total_questions');
			var countDownTimer = 0;
			if ( window?.minutes_MSstlms_quiz_countdown ) {
				countDownTimer += window?.minutes_MSstlms_quiz_countdown * 60;
			}
			if ( window?.seconds_MSstlms_quiz_countdown ) {
				countDownTimer += window?.seconds_MSstlms_quiz_countdown;
			}

			var postData = inputField.serialize();
			postData += '&action=stlms_save_quiz_data&nonce=' + StlmsObject.securityNonce + '&quiz_id=' + StlmsObject.quizId + '&course_id=' + StlmsObject.courseId;
			postData += '&quiz_timestamp=' + quizTime + '&timer_timestamp=' + countDownTimer + '&total_questions=' + totalQuestions;

			$.post(
				StlmsObject.ajaxurl,
				postData,
				function(response) {
					$(wizardId).smartWizard('loader', 'hide');
					if ( response.status ) {
						var lastTab = $('.tab-content .tab-pane:last');
						lastTab
						.find('.stlms-quiz-result-item #grade')
						.html(response.correctAnswers)
						.parents('.stlms-quiz-result-item')
						.next('.stlms-quiz-result-item')
						.find('#accuracy')
						.html(response.attemptedQuestions)
						.parents('.stlms-quiz-result-item')
						.next('.stlms-quiz-result-item')
						.find('#time')
						.html(response.time);
						$('.stlms-check-answer').hide();
						$('.stlms-next-wizard')
						.removeClass('stlms-next-wizard')
						.addClass('stlms-continue-course')
						.removeAttr('disabled');
						$('.stlms-lesson-view__footer .stlms-quiz-timer').hide();
						showQuizResult = true;
						if ( response.passed ) {
							$('.quiz-failed-text').hide();
							$('.quiz-passed-text').show();
						} else {
							$('.quiz-passed-text').hide();
							$('.quiz-failed-text').show();
						}
						$(wizardId).smartWizard('next');
					} else {
						$('.stlms-lesson-view__footer:visible button').attr('disabled', true);
					}
				},
				'json'
			)
			.fail(function() {
				$(wizardId).smartWizard('loader', 'hide');
				$('.stlms-lesson-view__footer:visible button:not(.stlms-check-answer)').attr('disabled', false);
			});
			return false;
		}
	});
	$(wizardId)?.smartWizard({
		autoAdjustHeight: false,
		anchor: false,
		enableUrlHash: false,
		transition: {
			animation: "fade", // none|fade|slideHorizontal|slideVertical|slideSwing|css
		},
		toolbar: {
			showNextButton: false, // show/hide a Next button
			showPreviousButton: false, // show/hide a Previous button
		}
	});

	$(document).on('change', '.stlms-quiz-option-list input:radio, .stlms-quiz-option-list input:checkbox', function() {
		if ( $(this).is(':checkbox') ) {
			var checked = $(this)
			.parents('ul')
			.find('input:checkbox')
			.filter(':checked');
			$('.stlms-check-answer').attr('disabled', function(){
				return checked.length > 0 ? false : true;
			});
			return;
		}
		$('.stlms-check-answer').removeAttr('disabled');
	});

	$(document).on('input', 'input[name^="stlms_written_answer"]', function(){
		var val = $(this).val();
		$('.stlms-check-answer').attr('disabled', function(){
			return '' !== val.trim() ? false : true;
		});
	});

	$(document).on('click', '.stlms-lesson-view__footer .stlms-check-answer', function(e){
		var quickCheckBtn = $(this);
		$(wizardId).smartWizard('loader', 'show');
		$(this).attr('disabled', true);

		var inputField = $('.stlms-quiz-view-content:visible').find('input:radio:checked, input:checkbox:checked, input:text');
		var postData = inputField.serialize();
		postData += '&action=stlms_check_answer&nonce=' + StlmsObject.securityNonce;
		$.post(
			StlmsObject.ajaxurl,
			postData,
			function(response) {
				if ( true === response.status ) {
					inputField
					.addClass('valid')
					.parents('.stlms-quiz-option-list, .stlms-quiz-input-ans')
					.css({opacity: 0.5, 'pointer-events': 'none' })
					.find('input:radio, input:checkbox, input:text')
					.removeClass('invalid');
				} else {
					inputField
					.addClass('invalid')
					.parents('.stlms-quiz-option-list, .stlms-quiz-input-ans')
					.css({opacity: 0.5, 'pointer-events': 'none' })
					.find('input:radio, input:checkbox, input:text')
					.removeClass('valid');
				}
				inputField
				.parents('.stlms-quiz-option-list')
				.next('.stlms-alert')
				.remove();

				$(response.message).insertAfter(inputField.parents('.stlms-quiz-option-list, .stlms-quiz-input-ans'));
				
				$(wizardId).smartWizard('loader', 'hide');
				$(this).attr('disabled', false);
			},
			'json'
		)
		.fail(function() {
			$(wizardId).smartWizard('loader', 'hide');
			quickCheckBtn.attr('disabled', false);
		});
		return false;
	});
});