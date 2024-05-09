import smartWizard from 'smartwizard';
jQuery(document).ready(function ($) {
	var wizardId = "#smartwizard";
	$(".bdlms-prev-wizard").on("click", function () {
		// Navigate previous
		$(wizardId).smartWizard("prev");
		return true;
	});

	$(".bdlms-next-wizard").on("click", function () {
		// Navigate next
		$(wizardId).smartWizard("next");
		return true;
	});
	$(wizardId).on("showStep", function (e, anchorObject, stepIndex, stepDirection, stepPosition) {
		if (stepPosition === "first") {
			$(".bdlms-lesson-view__footer").addClass("hidden");
		} else {
			$(".bdlms-lesson-view__footer").removeClass("hidden");
		}
	});
	$(wizardId)?.smartWizard({
		autoAdjustHeight: false,
		anchor: {
			enableNavigation: false,
		},
		enableUrlHash: false,
		transition: {
			animation: "fade", // none|fade|slideHorizontal|slideVertical|slideSwing|css
		},
		toolbar: {
			showNextButton: false, // show/hide a Next button
			showPreviousButton: false, // show/hide a Previous button
		},
	});
});