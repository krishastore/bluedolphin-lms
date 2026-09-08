/* global StlmsObject */
import { Fancybox } from '@fancyapps/ui';
import 'datatables.net-dt';
import 'datatables.net-responsive-dt';
import 'datatables.net-scroller-dt';
import Select2 from 'select2';

jQuery(function($) {
    $('#myTable').DataTable({
        responsive: true,
        ordering: true,
        order: [],
        "info": false,
        language: {
            emptyTable: 'You have not assigned any courses.',
            zeroRecords: 'There is no record to display.'
        },
        pagingType: 'simple_numbers',
        "fnDrawCallback": function(oSettings) {
            if (oSettings._iDisplayLength > oSettings.fnRecordsDisplay()) {
                $(oSettings.nTableWrapper).find('.dt-paging').hide();
            } else {
                $(oSettings.nTableWrapper).find('.dt-paging').show();
            }
        }
    });

    let debounceTimers = {};

    $('input[data-list]').on('input', function() {
        const $input = $(this);
        const listSelector = $input.data('list');
        const $listItems = $(`${listSelector} li`);
        const $noResults = $(`${listSelector}`).siblings('.no-results');

        clearTimeout(debounceTimers[listSelector]);

        debounceTimers[listSelector] = setTimeout(() => {
            const query = $input.val().toLowerCase();
            let visibleCount = 0;
            let visibleItems = [];

            $listItems.each(function() {
                const text = $(this).text().toLowerCase();
                const isVisible = text.includes(query);
                $(this).toggle(isVisible);
                if (isVisible) {
                    visibleCount++;
                    visibleItems.push($(this));
                }
            });

            $listItems.removeClass('last-item');

            if (visibleItems.length > 0) {
                visibleItems[visibleItems.length - 1].addClass('last-item');
            }

            if (visibleCount === 0) {
                $noResults.show();
                $(listSelector).hide()
            } else {
                $noResults.hide();
                $(listSelector).show()
            }
        }, 300);
    });

    function updateEmployeeCount() {
        let count = $('.stlms-employee:checked:not(:disabled)').length;
        $('#employee_cnt').text(count + ' Selected');

        count = $('.stlms-select2-multi').val() ? $('.stlms-select2-multi').val().length : count;

        if ( count > 1 ) {
            $('.stlms-switch-wrap').show();
        } else {
            $('.stlms-switch-wrap').hide();
        }
    }

    updateEmployeeCount();

    $(document).on('change', '.stlms-check', function() {
        updateEmployeeCount();
    });

    $(document).on('change', '.stlms-select2-multi', function () {
        updateEmployeeCount();
    });

    const $commonDateCheckbox = $('.stlms-switch-wrap .stlms-check');
    const $commonDateField = $('#common-date');
    const $uniqueDateContainer = $('#unique-date');
    const $employeeList = $('#employee-list');

    function updateDateFields() {
        const isCommon = $commonDateCheckbox.is(':checked');
        const today = new Date().toISOString().slice(0, 10);
        let selectedEmployees = [];

        if ($employeeList.is('select') && $employeeList.hasClass('select2-hidden-accessible')) {
            const selectedOptions = $employeeList.select2('data');
            selectedEmployees = selectedOptions.map(opt => opt.text.trim());
        } else {
            selectedEmployees = $employeeList.find('.stlms-check:checked:not(:disabled)').map(function () {
                return $(this).closest('label').text().trim();
            }).get();
        }

        if (isCommon) {
            $commonDateField.show();
            $uniqueDateContainer.hide().empty();
        } else {
            $commonDateField.hide();
            $uniqueDateContainer.empty().show();

            selectedEmployees.forEach(employeeName => {
                const dateField = `
                    <div class="stlms-form-col">
                        <div class="stlms-form-group">
                            <label>Completion Date For ${employeeName}</label>
                            <input type="date" min="${today}" name="completion_date[${employeeName}]" />
                        </div>
                    </div>
                `;

                $uniqueDateContainer.append(dateField);
            });
        }
    }

    // Initial run
    updateDateFields();

    // Trigger updates on change
    $commonDateCheckbox.on('change', updateDateFields);
    $employeeList.on('change', updateDateFields);
});

Fancybox.bind("[data-fancybox]", {});

jQuery(function($) {
    $('.stlms-select2').select2();
    $('.stlms-select2-multi').select2({
        dropdownParent: $('#assign-course')
    });

    $('.stlms-form-control, .stlms-filter-item .form-control').on('change', function () {
        const $this = $(this);
        let selectedValue = $this.val();
        selectedValue = selectedValue.replace('-', ' ');

        $('.stlms-form-control, .stlms-filter-item .form-control').not($this).each(function () {
            $(this).val('').trigger('change.select2');
        });

        if (selectedValue) {
            $('.dt-input').val(selectedValue).trigger('input');
        } else {
            $('.dt-input').val('').trigger('input');
        }
    });
    // Reset filter js
    $('.stlms-reset-btn').on('click', function (e) {
        e.preventDefault();
        $('.stlms-form-control, .form-control').val('').trigger('change.select2');
        $('.dt-input').val('').trigger('input');
    });
});

let snackbarTimeout;

function showSnackbar(snackbarId) {
    const $snackbar = jQuery('#' + snackbarId);
    $snackbar.addClass('show');

    clearTimeout(snackbarTimeout);

    snackbarTimeout = setTimeout(() => {
        $snackbar.removeClass('show');
    }, 3000);
}

// Hide snackbar on close button click
jQuery(document).on('click', '.hideSnackbar', function (e) {
    e.preventDefault();
    jQuery(this).closest('.stlms-snackbar').removeClass('show');
    jQuery(this).closest('.stlms-snackbar').addClass('hide');
    var url = new URL(window.location.href);
    url.searchParams.delete('status');
    window.history.replaceState(null, null, url.toString());
    clearTimeout(snackbarTimeout);
});

// Validate and show appropriate snackbar.
jQuery('#showSnackbar').on('click', function (e) {
    e.preventDefault();

    let $selectedCourse = jQuery('#course-list .stlms-check[type="radio"]:checked');
    let $selectedEmployees = jQuery('#employee-list .stlms-check[type="checkbox"]:checked:not(:disabled)');

    if (jQuery('form.stlms-assign-course__box').length) {
        $selectedCourse = jQuery('#assign-course');
        $selectedEmployees = jQuery('#employee-list').val();
    }

    if ($selectedCourse.length > 0 && $selectedEmployees.length > 0) {
        const courseId = $selectedCourse.val() ? $selectedCourse.val() : $selectedCourse.data('course');
        const assignCourseData = [];

        const isCommon = jQuery('.stlms-switch-wrap input[type="checkbox"]').is(':checked');
        const commonDate = jQuery('#common-date input[type="date"]').val();

        let employeeValues = [];

        if (jQuery('#employee-list').is('select')) {
            employeeValues = $selectedEmployees || [];
        } else {
            $selectedEmployees.each(function () {
                employeeValues.push(jQuery(this).val());
            });
        }

        employeeValues.forEach(function (encodedId, index) {
            let decodedId = '';
            try {
                decodedId = atob(encodedId);
            } catch (err) {
                showSnackbar('snackbar-error');
            }

            let completionDate = '';

            if (isCommon) {
                completionDate = commonDate;
            } else {
                const $uniqueDateInputs = jQuery('#unique-date input[type="date"]');
                if ($uniqueDateInputs.length > index) {
                    completionDate = $uniqueDateInputs.eq(index).val();
                }
            }

            assignCourseData.push({
                course_id: Number.parseInt(courseId),
                user_id: Number.parseInt(decodedId),
                completion_date: completionDate
            });
        });

        jQuery.post(
            StlmsObject.ajaxurl,
            {
                action: 'assign_new_course',
                _nonce: StlmsObject.nonce,
                assign_course_data: assignCourseData,
            },
            function (response) {
                showSnackbar('snackbar-success');
                setTimeout(function () {
                    window.location.href = StlmsObject.assignCourseUrl + '?status=success';
                }, 500);
            }
        );
    } else {
        showSnackbar('snackbar-error');
    }
});

jQuery(function($) {
	let isRequestInProgress = false;

	$('input[name="course"]').on('change', function () {
		if (isRequestInProgress) {
			return false;
		}

		const courseId = $(this).val();
		if (!courseId) return;

		// Disable other inputs during request.
		isRequestInProgress = true;
		$('#employee-list input[type="checkbox"]').prop('disabled', false);

		$.ajax({
			type: 'POST',
			url: StlmsObject.ajaxurl,
			data: {
				action: 'get_assigned_users',
				course_id: courseId,
				_nonce: StlmsObject.nonce,
			},
			success: function (response) {
				if (response.success && Array.isArray(response.data)) {
					// Disable already assigned users.
					$('#employee-list input[type="checkbox"]').each(function () {
						const val = $(this).val();
						const decoded = atob(val);
						if (response.data.includes(Number.parseInt(decoded))) {
							$(this).prop('disabled', true);
						}
					});
				}
			},
			error: function (err) {
				console.error('AJAX failed:', err);
			},
			complete: function () {
				isRequestInProgress = false;
			},
		});
	});
});

jQuery(function($) {
    $('#myTable').on('click', '.stlms-assigned-course__button.edit', function () {
        let $row = $(this).closest('tr');
        if ($row.hasClass('child')) {
            $row = $row.prevAll('tr[data-key]').first();
        }

        var courseName = $row.find('td').eq(0).text().trim();
        var assignedTo = $row.find('td').eq(1).text().trim();
        var timestamp = $row.find('td[data-date]').data('date') || '';
        var formattedDate = '';
        const today = new Date().toISOString().split('T')[0];
        if (timestamp) {
            var dateObj = new Date(timestamp * 1000);
            formattedDate = dateObj.toISOString().split('T')[0];
        }

        $('#edit-course .stlms-dialog__content-title span.course-name').text(courseName);
        $('#edit-course .stlms-dialog__content-title span.user-name').text(assignedTo);
        $('#edit-course #completion-date').val(formattedDate).attr('min', today);
        $('#edit-course label[for="completion-date"]').text('Completion Date For ' + assignedTo);
    });
});

jQuery('#edit-course .update, #delete-course .delete').on('click', function () {
    const actionType = jQuery(this).closest('.stlms-dialog').attr('id') === 'edit-course' ? 'edit' : 'delete';
    UpdateAssignCourse(actionType);
});

let currentActionKey = null;

jQuery('#myTable').on('click', '.stlms-assigned-course__button.edit, .stlms-assigned-course__button.delete', function () {
    let $tr = jQuery(this).closest('tr');
    if ($tr.hasClass('child')) {
        $tr = $tr.prev('tr[data-key]');
    }
    currentActionKey = $tr.data('key');
});

function UpdateAssignCourse(actionType) {
    if (!currentActionKey) return;

    let data = {
        action: 'update_assign_course',
        type: actionType,
        key: currentActionKey,
        _nonce: StlmsObject.nonce,
    };

    if (actionType === 'edit') {
        const completionDate = jQuery('#edit-course #completion-date').val();
        data.date = completionDate;
    }

    jQuery.ajax({
        type: 'POST',
		url: StlmsObject.ajaxurl,
        data: data,
        success: function (response) {
            window.location.reload();
        },
    });
}