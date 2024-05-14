import smartWizard from 'smartwizard';

jQuery(function ($) {
	var showQuizResult = false;
	var wizardId = "#smartwizard";
	$(".bdlms-prev-wizard").on("click", function () {
		// Navigate previous
		$(wizardId).smartWizard("prev");
		return true;
	});

	$(document).on( 'click', '.bdlms-next-wizard', function () {
		// Navigate next.
		$(wizardId).smartWizard('next');
		return true;
	});
	$(wizardId).on("showStep", function (e, anchorObject, stepIndex, stepDirection, stepPosition) {
		if ( 'first' === stepPosition) {
			$(".bdlms-lesson-view__footer").addClass("hidden");
		} else {
			if ( ! showQuizResult ) {
				$(".bdlms-lesson-view__footer").removeClass("hidden");
			}
		}
		$('body').trigger('bdlms:show:step', {currentStepIndex: stepIndex, currentStepPosition: stepPosition});
	});
	$(wizardId).on("leaveStep", function(e, anchorObject, currentStepIndex, nextStepIndex, stepDirection) {
		if(anchorObject.prevObject.length - 1 == nextStepIndex) {
			if ( showQuizResult ) {
				return true;
			}
			$('body').trigger('bdlms:show:quizResult');
			$('.bdlms-lesson-view__footer:visible button').attr('disabled', true);
			$(wizardId).smartWizard('loader', 'show');
	
			var inputField = $('.tab-content:visible').find('input:radio:checked, input:checkbox:checked, input:text');
			inputField
			.parents('.bdlms-quiz-option-list, .bdlms-quiz-input-ans')
			.css({opacity: 0.5, 'pointer-events': 'none' })

			var quizTime       = $('#bdlms_quiz_countdown').data('timestamp');
			var totalQuestions = $('#bdlms_quiz_countdown').data('total_questions');
			var countDownTimer = 0;
			if ( window?.minutes_MSbdlms_quiz_countdown ) {
				countDownTimer += window?.minutes_MSbdlms_quiz_countdown * 60;
			}
			if ( window?.seconds_MSbdlms_quiz_countdown ) {
				countDownTimer += window?.seconds_MSbdlms_quiz_countdown;
			}

			var postData = inputField.serialize();
			postData += '&action=bdlms_save_quiz_data&nonce=' + BdlmsObject.securityNonce + '&quiz_id=' + BdlmsObject.quizId + '&course_id=' + BdlmsObject.courseId;
			postData += '&quiz_timestamp=' + quizTime + '&timer_timestamp=' + countDownTimer + '&total_questions=' + totalQuestions;

			$.post(
				BdlmsObject.ajaxurl,
				postData,
				function(response) {
					$(wizardId).smartWizard('loader', 'hide');
					if ( response.status ) {
						var lastTab = $('.tab-content .tab-pane:last');
						lastTab
						.find('.bdlms-quiz-result-item #grade')
						.html(response.grade)
						.parents('.bdlms-quiz-result-item')
						.next('.bdlms-quiz-result-item')
						.find('#accuracy')
						.html(response.accuracy)
						.parents('.bdlms-quiz-result-item')
						.next('.bdlms-quiz-result-item')
						.find('#time')
						.html(response.time);
						$('.bdlms-lesson-view__footer:visible').addClass('hidden');
						showQuizResult = true;
						$(wizardId).smartWizard('next');
					} else {
						$('.bdlms-lesson-view__footer:visible button').attr('disabled', true);
					}
				},
				'json'
			)
			.fail(function() {
				$(wizardId).smartWizard('loader', 'hide');
				$('.bdlms-lesson-view__footer:visible button:not(.bdlms-check-answer)').attr('disabled', false);
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

	$(document).on('change', '.bdlms-quiz-option-list input:radio, .bdlms-quiz-option-list input:checkbox', function() {
		if ( $(this).is(':checkbox') ) {
			var checked = $(this)
			.parents('ul')
			.find('input:checkbox')
			.filter(':checked');
			$('.bdlms-check-answer').attr('disabled', function(){
				return checked.length > 0 ? false : true;
			});
			return;
		}
		$('.bdlms-check-answer').removeAttr('disabled');
	});

	$(document).on('input', 'input[name^="bdlms_written_answer"]', function(){
		var val = $(this).val();
		$('.bdlms-check-answer').attr('disabled', function(){
			return '' !== val.trim() ? false : true;
		});
	});

	$(document).on('click', '.bdlms-lesson-view__footer .bdlms-check-answer', function(e){
		var quickCheckBtn = $(this);
		$(wizardId).smartWizard('loader', 'show');
		$(this).attr('disabled', true);

		var inputField = $('.bdlms-quiz-view-content:visible').find('input:radio:checked, input:checkbox:checked, input:text');
		var postData = inputField.serialize();
		postData += '&action=bdlms_check_answer&nonce=' + BdlmsObject.securityNonce;
		$.post(
			BdlmsObject.ajaxurl,
			postData,
			function(response) {
				if ( true === response.status ) {
					inputField
					.addClass('valid')
					.parents('.bdlms-quiz-option-list, .bdlms-quiz-input-ans')
					.css({opacity: 0.5, 'pointer-events': 'none' })
					.find('input:radio, input:checkbox, input:text')
					.removeClass('invalid');
				} else {
					inputField
					.addClass('invalid')
					.parents('.bdlms-quiz-option-list, .bdlms-quiz-input-ans')
					.css({opacity: 0.5, 'pointer-events': 'none' })
					.find('input:radio, input:checkbox, input:text')
					.removeClass('valid');
				}
				inputField
				.parents('.bdlms-quiz-option-list')
				.next('.bdlms-alert')
				.remove();

				$(response.message).insertAfter(inputField.parents('.bdlms-quiz-option-list, .bdlms-quiz-input-ans'));
				
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
	// jQuery('.bdlms-lesson-list')
	// .find('input:checkbox')
	// .filter(':checked');
});