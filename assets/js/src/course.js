jQuery(document).ready(function($){
	const tabs = document.querySelectorAll(".bdlms-tab");
	const tabContents =
		document.querySelectorAll(".bdlms-tab-content");

	tabs.forEach((tab) => {
		tab.addEventListener("click", function () {
			const tabId = this.getAttribute("data-tab");

			// Hide all tab contents
			tabContents.forEach((content) => {
				content.classList.remove("active");
			});

			// Remove active class from all tabs
			tabs.forEach((t) => {
				t.classList.remove("active");
			});

			// Show the corresponding tab content with fade effect
			const tabContent = document.querySelector(
				`.bdlms-tab-content[data-tab="${tabId}"]`,
			);
			if (tabContent) {
				tabContent.classList.add("active");
			}
			// Add active class to clicked tab
			this.classList.add("active");
		});
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
	$(document).on('click', '.bdlms-curriculum-dd-button', function (e) {
		e.preventDefault();
		$(this).next("ul").slideToggle();
	});
});