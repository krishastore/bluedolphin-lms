/* global StlmsObject */
jQuery(function ($) {
  // Password Toggle
  $(".stlms-password-toggle").on("click", function () {
    $(this).toggleClass("active");
    var input = $($(this).attr("toggle"));
    if (input.attr("type") == "password") {
      input.attr("type", "text");
    } else {
      input.attr("type", "password");
    }
  });

  // Accordion
  $(".stlms-accordion .stlms-accordion-item").each(function (i, el) {
    var isExpanded = $(el).data("expanded") === true;
    if (isExpanded) {
      $(el).find(".stlms-accordion-collapse").slideDown();
      $(el).find(".stlms-accordion-header:not(.no-accordion)").addClass("active");
    }
  });
  $(".stlms-accordion .stlms-accordion-header:not(.no-accordion)").click(function () {
    var currentAccordionItem = $(this).parents(".stlms-accordion-item");
    if ($(this).hasClass("active")) {
      currentAccordionItem.data("expanded", false);
      currentAccordionItem.find(".stlms-accordion-collapse").slideUp();
      currentAccordionItem
        .find(".stlms-accordion-header")
        .removeClass("active");
    } else {
      $(this)
        .parents(".stlms-accordion")
        .find(".stlms-accordion-item")
        .each(function (i, el) {
          $(el).data("expanded", false);
          $(el).find(".stlms-accordion-collapse").slideUp();
          $(el).find(".stlms-accordion-header").removeClass("active");
        });
      currentAccordionItem.data(
        "expanded",
        !currentAccordionItem.data("expanded")
      );
      currentAccordionItem.find(".stlms-accordion-collapse").slideToggle();
      currentAccordionItem
        .find(".stlms-accordion-header")
        .toggleClass("active");
    }
  });

  // Filter Sidebar Toggle
  $(".stlms-filter-toggle").on("click", function () {
    $(".stlms-course-filter").toggleClass("active");
  });
  $(".stlms-filter-close").on("click", function () {
    $(".stlms-course-filter").removeClass("active");
  });
  // Close Sidebar on Search Button Click
  $(".stlms-search button").on("click", function () {
    $(".stlms-course-filter").removeClass("active");
  });

  // Close Sidebar on Search Enter
  $(".stlms-form-control").on("keypress", function (e) {
    if (e.which == 13) {
      $(".stlms-course-filter").removeClass("active");
    }
  });
  // Lesson Sidebar Toggle.
  $(".stlms-lesson-toggle").on("click", '.icon', function () {
    $(".stlms-lesson-view").addClass("active");
  });
  $(".stlms-lesson-toggle").on("click", '.icon-cross', function () {
    $(".stlms-lesson-view").removeClass("active");
  });

   $(".stlms-ai-chat").on("click", function () {
  $(".stlms-ai-chat__wrap, body").addClass("active");
});

$(".stlms-ai-chat__overlay, .stlms-ai-chat-close").on("click", function () {
  $(".stlms-ai-chat__wrap, body").removeClass("active");
});

  // Login form ajax.
  $(document).on('submit', '.stlms-login__body form', function() {
    var _this =  $(this);
    _this
    .find('.stlms-error-message')
    .addClass('hidden')
    .next('.stlms-form-footer')
    .find('.stlms-loader')
    .addClass('is-active');

    $.post(
      StlmsObject.ajaxurl,
      _this.serialize(),
      function(response) {
        if ( response.status ) {
          window.location.href = response.redirect;
        } else {
          _this
          .find('.stlms-error-message')
          .removeClass('hidden')
          .find('span')
          .html(response.message)
          .parent('div')
          .next('.stlms-form-footer')
          .find('.stlms-loader')
          .removeClass('is-active');
        }
      },
      'json'
    );
    return false;
  });
  
  // Filter items.
  var sendFilterItemRequest = function() {
  	var data = $('form.stlms-filter-form').serializeArray();
		var url = new URL(window.location.href);
		if ( data.length > 0 ) {
			var getCatVal = [];
			var getlevelVal = [];
			url.searchParams.delete('category');
			url.searchParams.delete('levels');
			url.searchParams.delete('progress');
            var updateUrl = StlmsObject.currentUrl;
			var url = new URL(updateUrl);
			$.each(data, function(index, item){
				var inputName = item.name.replace('[]', '');
				if ( 'order_by' === inputName || '_s' === inputName || 'progress' === inputName ) {
					if ( '' !== item.value ) {
						url.searchParams.set(inputName, item.value);
					}
				} else {
					if( 'category' === inputName ){
						getCatVal.push(item.value);
						url.searchParams.set(inputName, getCatVal.toString(','));
					} else {
						getlevelVal.push(item.value);
						url.searchParams.set(inputName, getlevelVal.toString(','));
					}
				}
			});
		} else {
			for (const key of url.searchParams.keys()) {
				url.searchParams.delete(key);
			}
		}
		window.history.replaceState(null, null, url.toString());
    $('#stlms_course_view')
    .addClass('is-loading')
    .load(url.toString() + ' #stlms_course_view > *', function () {
      $(this).removeClass('is-loading');
      $(".stlms-filter-toggle").on("click", function () {
        $(".stlms-course-filter").toggleClass("active");
      });
      $(".stlms-filter-close").on("click", function () {
        $(".stlms-course-filter").removeClass("active");
      });
    });
  };

  // Filter category.
  $(document).on('change', '.stlms-filter-list input:checkbox:not(#stlms_category_all), .stlms-filter-list input:checkbox:not(#stlms_level_all), .stlms-filter-list input:radio', function() {
    sendFilterItemRequest();
  });
  $(document).on('change', '.stlms-filter-list input:checkbox#stlms_category_all, .stlms-filter-list input:checkbox#stlms_level_all', function() {
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

  $(document).on('change', '.stlms-form-group select.category', function() {
    $('.stlms-filter-form input[name="category"]').val( $(this).val() );
    sendFilterItemRequest();
  });
  $(document).on('change', '.stlms-form-group select.progress', function() {
    $('.stlms-filter-form input[name="progress"]').val( $(this).val() );
    sendFilterItemRequest();
  });
  $(document).on('change', '.stlms-sort-by select', function(){
		$('.stlms-filter-form input[name="order_by"]').val( $(this).val() );
		sendFilterItemRequest();
  });

  $(document).on('submit','.stlms-course-search form, .stlms-search-wrap form', function() {
  	$('.stlms-filter-form input[name="_s"]').val( $('input:text', $(this)).val() );
		sendFilterItemRequest();
  });

  $(document).on('click', '.stlms-reset-btn', function() {
    var url = new URL(window.location.href);
    url.searchParams.delete('category');
    url.searchParams.delete('progress');
    url.searchParams.delete('_s');
	url.searchParams.delete('levels');
    $('.stlms-filter-form input[name="category"]').val('');
    $('.stlms-filter-form input[name="progress"]:hidden').val('');
    $('.stlms-filter-form input[name="_s"]').val('');
	$('.stlms-filter-list input:checkbox').removeAttr("checked");
	$('.stlms-filter-list input:radio').removeAttr("checked");
    sendFilterItemRequest();
    window.history.replaceState(null, null, url.toString());
    $('.stlms-form-group select.category, .stlms-form-group select.progress, .stlms-search input:text').val('');
  });
	// var uri = window.location.toString();
	// if (uri.indexOf("?") > 0) {
	// 	var clean_uri = uri.substring(0, uri.indexOf("?"));
	// 	window.history.replaceState({}, document.title, clean_uri);
	// }
});

jQuery(function($) {
	$('input[id^="st_course_term"]').each(function() {
		if ($('input[id^="st_course_term"]').prop('checked')) {
			$('#stlms_category_all').prop('checked', true);
		} else {
			$('#stlms_category_all').prop('checked', false);
		}
	});
	$('input[id^="st_course_level"]').each(function() {
		if ($('input[id^="st_course_level"]').prop('checked')) {
			$('#stlms_level_all').prop('checked', true);
		} else {
			$('#stlms_level_all').prop('checked', false);
		}
	});
});

jQuery(window).on('load', function() {
  
	var activeElement = jQuery('.stlms-lesson-accordion .stlms-lesson-list li.active');

	var activeHeight = activeElement.innerHeight();
	if (activeElement.length) {
		var container = jQuery('.stlms-lesson-accordion');
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
    var target = jQuery(this).data("id") || jQuery(this).attr("id");
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
    jQuery(this).next('.stlms-loader').addClass('is-active');
		var courseId = jQuery(this).data('course'); // Retrieve the course ID from a data attribute

		jQuery.ajax({
			url: StlmsObject.ajaxurl,
			type: 'POST',
			data: {
				action: 'stlms_download_course_certificate',
				_nonce: StlmsObject.nonce,
				course_id: courseId,
			},
			xhrFields: {
				responseType: 'blob' // Specify that we expect a blob response (PDF file)
			},
			success: function(response) {
        jQuery('#download-certificate').next('.stlms-loader').removeClass('is-active');
				// Create a URL for the blob and trigger a download
				var url = window.URL.createObjectURL(response);
				var a = document.createElement('a');
				a.href = url;
				a.download = StlmsObject.fileName + courseId;
				document.body.appendChild(a);
				a.click();
				a.remove();
				// Release the object URL
				window.URL.revokeObjectURL(url);
			},
      error: function() {
        setTimeout(function () {
          jQuery('#download-certificate').next('.stlms-loader').removeClass('is-active'); 
        }, 3000 );
      }
		});
	});

  jQuery(document).on('click', '#enrol-now', function(e) {
		e.preventDefault();
    var loader = jQuery(this).find('.stlms-loader');
    loader.addClass('is-active');
		var courseId = jQuery(this).data('course'); // Retrieve the course ID from a data attribute

		jQuery.ajax({
			url: StlmsObject.ajaxurl,
			type: 'POST',
			data: {
				action: 'stlms_enrol_course',
				_nonce: StlmsObject.nonce,
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
	jQuery(".stlms-user-dd .stlms-user-dd__toggle").on("click", function ($) {
		$(this).toggleClass("active");
		$(this).next(".stlms-user-dd__menu").slideToggle();
	});
	jQuery(function($) {
		$("[data-dropdown]").on("click", function (e) {
			e.preventDefault();
			e.stopPropagation();
			const $btn = $(this);
			const dropdownId = $btn.data("dropdown");
			const $dropdown = $("#" + dropdownId);
			$btn.toggleClass("active");
			$dropdown.stop(true, true).slideToggle();
			$("[data-dropdown]")
			.not($btn)
			.each(function () {
				const otherId = $(this).data("dropdown");
				$("#" + otherId).slideUp();
				$(this).removeClass("active");
			});
		});
		$(document).on("click", function (e) {
			if (!$(e.target).closest(".stlms-dd-wrap").length) {
				$("[data-dropdown]").each(function () {
				const id = $(this).data("dropdown");
				$("#" + id).slideUp();
				$(this).removeClass("active");
				});
			}
		});
		$(".stlms-dd-content").on("click", "a", function () {
			const $dropdown = $(this).closest(".stlms-dd-content");
			const id = $dropdown.attr("id");
			$dropdown.slideUp();
			$("[data-dropdown='" + id + "']").removeClass("active");
		});
	});
});

jQuery(document).on('click', '.stlms-notification-icon, #mark-all-read', function(e) {
    e.preventDefault();

    var iconButton = jQuery(this);
    var dataId = iconButton.data('id');
    var type = iconButton.attr('id') === 'mark-all-read' ? 'all' : 'single';

    jQuery.ajax({
        url: StlmsObject.ajaxurl,
        type: 'POST',
        data: {
            action: 'stlms_read_notification',
            _nonce: StlmsObject.nonce,
            data_id: dataId,
            type: type
        },
        success: function(response) {
            if (type === 'all') {
                jQuery('.stlms-notification-card').addClass('read-notification');
				jQuery('#mark-all-read').css('display','none');
				jQuery('.stlms-notification-icon').addClass('hide');
            } else {
                iconButton.closest('.stlms-notification-card').addClass('read-notification');
                iconButton.addClass('hide');
            }
        }
    });
});

jQuery(function($) {
    var filterTopCourses = function(categoryId) {
        var url = new URL(window.location.href);

        url.searchParams.delete('top_course_category');
        
        if (categoryId && categoryId !== 'all') {
            url.searchParams.set('top_course_category', categoryId);
        }

        window.history.replaceState(null, null, url.toString());

        $.ajax({
            url: url.toString(),
            type: 'GET',
            success: function(response) {
                var newContent = $(response).find('.topSwiper .swiper-wrapper').html();
                $('.topSwiper .swiper-wrapper').html(newContent);
            }
        });
    };
    
    $(document).on('change', '.stlms-category-nav-wrap #all', function() {
        if ($(this).is(':checked')) {
            $('.stlms-category-nav-wrap input[type="checkbox"]').not(this).prop('checked', false);
            filterTopCourses('all');
        }
    });
    
    $(document).on('change', '.stlms-category-nav-wrap input[type="checkbox"]:not(#all)', function() {
        var categoryId = $(this).attr('id');
        
        if ($(this).is(':checked')) {
            $('.stlms-category-nav-wrap input[type="checkbox"]').not(this).prop('checked', false);
            filterTopCourses(categoryId);
        } else {
            $('#all').prop('checked', true);
            filterTopCourses('all');
        }
    });
    
    var loadFiltersFromURL = function() {
        var url = new URL(window.location.href);
        var category = url.searchParams.get('top_course_category');
        
        if (category) {
            $('#all').prop('checked', false);
            $('.stlms-category-nav-wrap input[id="' + category + '"]').prop('checked', true);
        } else {
            $('#all').prop('checked', true);
        }
    };

    loadFiltersFromURL(); 
});