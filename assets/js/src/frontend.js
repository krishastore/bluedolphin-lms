jQuery(function ($) {
  // Password Toggle
  $(".bdlms-password-toggle").on("click", function () {
    $(this).toggleClass("active");
    var input = $($(this).attr("toggle"));
    if (input.attr("type") == "password") {
      input.attr("type", "text");
    } else {
      input.attr("type", "password");
    }
  });

  // Accordion
  $(".bdlms-accordion .bdlms-accordion-item").each(function (i, el) {
    var isExpanded = $(el).data("expanded") === true;
    if (isExpanded) {
      $(el).find(".bdlms-accordion-collapse").slideDown();
      $(el).find(".bdlms-accordion-header:not(.no-accordion)").addClass("active");
    }
  });
  $(".bdlms-accordion .bdlms-accordion-header:not(.no-accordion)").click(function () {
    var currentAccordionItem = $(this).parents(".bdlms-accordion-item");
    if ($(this).hasClass("active")) {
      currentAccordionItem.data("expanded", false);
      currentAccordionItem.find(".bdlms-accordion-collapse").slideUp();
      currentAccordionItem
        .find(".bdlms-accordion-header")
        .removeClass("active");
    } else {
      $(this)
        .parents(".bdlms-accordion")
        .find(".bdlms-accordion-item")
        .each(function (i, el) {
          $(el).data("expanded", false);
          $(el).find(".bdlms-accordion-collapse").slideUp();
          $(el).find(".bdlms-accordion-header").removeClass("active");
        });
      currentAccordionItem.data(
        "expanded",
        !currentAccordionItem.data("expanded")
      );
      currentAccordionItem.find(".bdlms-accordion-collapse").slideToggle();
      currentAccordionItem
        .find(".bdlms-accordion-header")
        .toggleClass("active");
    }
  });

  // Filter Sidebar Toggle
  $(".bdlms-filter-toggle").on("click", function () {
    $(".bdlms-course-filter").toggleClass("active");
  });

  // Lesson Sidebar Toggle.
  $(".bdlms-lesson-toggle").on("click", '.icon', function () {
    $(".bdlms-lesson-view").addClass("active");
  });
  $(".bdlms-lesson-toggle").on("click", '.icon-cross', function () {
    $(".bdlms-lesson-view").removeClass("active");
  });

  // Login form ajax.
  $(document).on('submit', '.bdlms-login__body form', function() {
    var _this =  $(this);
    _this
    .find('.bdlms-error-message')
    .addClass('hidden')
    .next('.bdlms-form-footer')
    .find('.bdlms-loader')
    .addClass('is-active');

    $.post(
      BdlmsObject.ajaxurl,
      _this.serialize(),
      function(response) {
        if ( response.status ) {
          window.location.href = response.redirect;
        } else {
          _this
          .find('.bdlms-error-message')
          .removeClass('hidden')
          .find('span')
          .html(response.message)
          .parent('div')
          .next('.bdlms-form-footer')
          .find('.bdlms-loader')
          .removeClass('is-active');
        }
      },
      'json'
    );
    return false;
  });
  
  // Filter category.
  $(document).on('change', '.bdlms-filter-list input:checkbox:not(#bdlms_category_all)', function() {
    var data = $(this)
	.parents('form')
	.serializeArray();
	var url = new URL(window.location.href);
	if ( data.length > 0 ) {
		var getCurrentVal = [];
		url.searchParams.delete('category');
		url.searchParams.delete('levels');
		$.each(data, function(index, item){
			var inputName = item.name.replace('[]', '');
			getCurrentVal.push(item.value);
			url.searchParams.set(inputName, getCurrentVal.toString(','));
		});
	} else {
		for (const key of url.searchParams.keys()) {
			url.searchParams.delete(key);
		}
	}
	window.history.replaceState(null, null, url.toString());
	$('#bdlms_course_view')
	.addClass('is-loading')
	.load(
		url.toString() + ' #bdlms_course_view > *',
		function() {
			$(this).removeClass('is-loading');
		}
	);
  });
  $(document).on('change', '.bdlms-filter-list input:checkbox#bdlms_category_all, .bdlms-filter-list input:checkbox#bdlms_level_all', function() {
	var isChecked = $(this).is(':checked');
	$(this)
	.parents('ul')
	.find('input:checkbox')
	.not(this)
	.attr('checked', isChecked)
	.prop('checked', isChecked)
	.last()
	.trigger('change');
  });

  $(document).on('change', 'select[name="order_by"]', function(){
	$(this)
	.parent('form')
	.trigger('submit');
  });
  
	var uri = window.location.toString();
	if (uri.indexOf("?") > 0) {
		var clean_uri = uri.substring(0, uri.indexOf("?"));
		window.history.replaceState({}, document.title, clean_uri);
	}
});
