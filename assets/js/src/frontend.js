jQuery(document).ready(function ($) {
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
      $(el).find(".bdlms-accordion-header").addClass("active");
    }
  });
  $(".bdlms-accordion .bdlms-accordion-header").click(function () {
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

  $(".bdlms-filter-toggle").on("click", function () {
    $(".bdlms-course-filter").toggleClass("active");
  });

  $(document).on('submit', '.bdlms-login__body form', function() {
    var _this =  $(this);
    _this
    .find('.bdlms-error-message')
    .addClass('hidden');

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
          .text(response.message);
        }
      },
      'json'
    );
    return false;
  });
});
