import smartWizard from 'smartwizard';

jQuery(function ($) {
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
			$(".bdlms-lesson-view__footer").removeClass("hidden");
		}
		$('body').trigger('bdlms:show:step', {currentStepIndex: stepIndex, currentStepPosition: stepPosition});
	});
	$(wizardId).on("leaveStep", function(e, anchorObject, currentStepIndex, nextStepIndex, stepDirection) {
		if(anchorObject.prevObject.length - 1 == nextStepIndex) {
			$(wizardId).smartWizard('loader', 'show');
			var inputField = $('.tab-content:visible').find('input:radio:checked, input:checkbox:checked, input:text');
			var postData = inputField.serialize();
			postData += '&action=bdlms_save_quiz_data&nonce=' + BdlmsObject.securityNonce;
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