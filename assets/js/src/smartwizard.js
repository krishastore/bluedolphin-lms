import smartWizard from 'smartwizard';

jQuery(function ($) {
	var wizardId = "#smartwizard";
	$(".bdlms-prev-wizard").on("click", function () {
		// Navigate previous
		$(wizardId).smartWizard("prev");
		return true;
	});

	$(".bdlms-next-wizard").on("click", function () {
		// Navigate next.
		$(wizardId).smartWizard("next");
		$(this).attr('disabled', true);
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
			$('.bdlms-next-wizard').attr('disabled', function(){
				return checked.length > 0 ? false : true;
			});
			return;
		}
		$('.bdlms-next-wizard').removeAttr('disabled');
	});

	$(document).on('input', 'input[name^="bdlms_written_answer"]', function(){
		var val = $(this).val();
		$('.bdlms-next-wizard').attr('disabled', function(){
			return '' !== val.trim() ? false : true;
		});
	});

	// jQuery('.bdlms-lesson-list')
	// .find('input:checkbox')
	// .filter(':checked');
});