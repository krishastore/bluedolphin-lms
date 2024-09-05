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
  
  // Filter items.
  var sendFilterItemRequest = function() {
  	var data = $('form.bdlms-filter-form').serializeArray();
		var url = new URL(window.location.href);
		if ( data.length > 0 ) {
			var getCurrentVal = [];
			url.searchParams.delete('category');
			url.searchParams.delete('levels');
            var updateUrl = BdlmsObject.currentUrl;
			var url = new URL(updateUrl);
			$.each(data, function(index, item){
				var inputName = item.name.replace('[]', '');
				if ( 'order_by' === inputName || '_s' === inputName || 'progress' === inputName ) {
                    if ( '' !== item.value ) {
                        url.searchParams.set(inputName, item.value);
                    }
				} else {
					getCurrentVal.push(item.value);
					url.searchParams.set(inputName, getCurrentVal.toString(','));
				}
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
  };

  // Filter category.
  $(document).on('change', '.bdlms-filter-list input:checkbox:not(#bdlms_category_all)', function() {
    sendFilterItemRequest();
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

  $(document).on('change', '.bdlms-form-group select.category', function() {
    $('.bdlms-filter-form input[name="category"]').val( $(this).val() );
    sendFilterItemRequest();
  });
  $(document).on('change', '.bdlms-form-group select.progress', function() {
    $('.bdlms-filter-form input[name="progress"]').val( $(this).val() );
    sendFilterItemRequest();
  });
  $(document).on('change', '.bdlms-sort-by select', function(){
		$('.bdlms-filter-form input[name="order_by"]').val( $(this).val() );
		sendFilterItemRequest();
  });

  $(document).on('submit','.bdlms-course-search form', function() {
  	$('.bdlms-filter-form input[name="_s"]').val( $('input:text', $(this)).val() );
		sendFilterItemRequest();
  });

  $(document).on('click', '.bdlms-reset-btn', function() {
    var url = new URL(window.location.href);
    url.searchParams.delete('category');
    url.searchParams.delete('progress');
    url.searchParams.delete('_s');
    $('.bdlms-filter-form input[name="category"]').val('');
    $('.bdlms-filter-form input[name="progress"]').val('');
    $('.bdlms-filter-form input[name="_s"]').val('');
    window.history.replaceState(null, null, url.toString());
    sendFilterItemRequest();
    $('.bdlms-form-group select.category, .bdlms-form-group select.progress, .bdlms-search input:text').val('');
  });
	// var uri = window.location.toString();
	// if (uri.indexOf("?") > 0) {
	// 	var clean_uri = uri.substring(0, uri.indexOf("?"));
	// 	window.history.replaceState({}, document.title, clean_uri);
	// }
});

jQuery(window).on('load', function() {
  
	var activeElement = jQuery('.bdlms-lesson-accordion .bdlms-lesson-list li.active');

	var activeHeight = activeElement.innerHeight();
	if (activeElement.length) {
		var container = jQuery('.bdlms-lesson-accordion');
		var elementTop = activeElement.offset().top - activeHeight - 40 ;
		var elementTop2 = activeElement.position().top - 80;
		setTimeout(() => {
			container.animate({
				scrollTop: screen.width <= 1419 ? elementTop2 : elementTop
			}, 1000);
		}, 3000);
	}

  /*==============================================================*/
  // click to scroll section start
  /*==============================================================*/
  jQuery(".goto-section").on("click", function (e) {
    e.preventDefault();
    var target = jQuery(this).data("id");
    jQuery("html, body")
      .stop()
      .animate(
        {
          scrollTop: jQuery("#" + target).offset().top - 20,
        },
        1600,
        "swing",
        function () {}
      );
  });
  /*==============================================================*/
  // click to scroll section end
  /*==============================================================*/

  jQuery(document).on('click', '#download-certificate', function(e) {
		e.preventDefault();
    jQuery(this).next('.bdlms-loader').addClass('is-active');
		var courseId = jQuery(this).data('course'); // Retrieve the course ID from a data attribute

		jQuery.ajax({
			url: BdlmsObject.ajaxurl,
			type: 'POST',
			data: {
				action: 'bdlms_download_course_certificate',
				_nonce: BdlmsObject.nonce,
				course_id: courseId,
			},
			xhrFields: {
				responseType: 'blob' // Specify that we expect a blob response (PDF file)
			},
			success: function(response) {
        jQuery('#download-certificate').next('.bdlms-loader').removeClass('is-active');
				// Create a URL for the blob and trigger a download
				var url = window.URL.createObjectURL(response);
				var a = document.createElement('a');
				a.href = url;
				a.download = BdlmsObject.fileName + courseId;
				document.body.appendChild(a);
				a.click();
				a.remove();
				// Release the object URL
				window.URL.revokeObjectURL(url);
			},
      error: function() {
        setTimeout(function () {
          jQuery('#download-certificate').next('.bdlms-loader').removeClass('is-active'); 
        }, 3000 );
      }
		});
	});

  jQuery(document).on('click', '#enrol-now', function(e) {
		e.preventDefault();
    var loader = jQuery(this).find('.bdlms-loader');
    loader.addClass('is-active');
		var courseId = jQuery(this).data('course'); // Retrieve the course ID from a data attribute

		jQuery.ajax({
			url: BdlmsObject.ajaxurl,
			type: 'POST',
			data: {
				action: 'bdlms_enrol_course',
				_nonce: BdlmsObject.nonce,
				course_id: courseId,
			},
			success: function(response) {
        loader.removeClass('is-active');
        window.location.replace( response.url );
			},
      error: function() {
        setTimeout(function () {
          loader.removeClass('is-active'); 
        }, 3000 );
      }
		});
	});

  // User Dropdown Toggle
  jQuery(".bdlms-user-dd .bdlms-user-dd__toggle").on("click", function () {
    jQuery(this).next(".bdlms-user-dd__menu").slideToggle();
  });
});
