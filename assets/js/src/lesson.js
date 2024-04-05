/**
 * This file contains the functions needed for handle lesson module.
 *
 * @since 1.0.0
 * @output assets/js/lesson.js
 */

window.wp = window.wp || {};

jQuery(document).ready(function($) {
	$("#course_list_modal").dialog({
		title: "Select Course",
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
	$(document).on("click", '[data-modal="assign_lesson"]', function (e) {
			e.preventDefault();
			$("#course_list_modal").dialog("open");
		},
	);
});