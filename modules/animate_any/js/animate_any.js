/**
 * @file
 * Js apply all animation to pages
 */

(function ($, Drupal) {
  'use strict';
  // Animation goes here
  Drupal.behaviors.animate_any = {
    attach: function (context, settings) {
      // Get all animation json data here
      var animations = $.parseJSON(settings.animate.animation_data);
      $.each(animations, function (i, element) {
        // First main identifier
        var animate_parent = element.parent;
        var animate_ident = $.parseJSON(element.identifier);
        // Second below identifier
        if ($(animate_parent).length !== 0) {
          $.each(animate_ident, function (k, item) {
            var section = $(item.section_identity);
            var jsevent = String(item.section_event);
            if ($(item.section_identity).length !== 0) {
              const item_data = {
                'animate_parent': animate_parent,
                'section_identity': item.section_identity,
                'section_animation': item.section_animation,
              };
              // Add animation to child section only when it is visible on viewport
              if (jsevent === 'scroll') {
                $(window).scroll(function () {
                  if (section.visible()) {
                    $(animate_parent).find(item.section_identity).addClass(item.section_animation + ' animated');
                  }
                });
              }
              else if (jsevent === 'onload') {
                $(document).ready(function () {
                  if (section.visible()) {
                    $(animate_parent).find(item.section_identity).addClass(item.section_animation + ' animated');
                  }
                });
              }
              else {
                $(animate_parent).find(item.section_identity).on(jsevent, function () {
                  $(animate_parent).find(item.section_identity).addClass(item.section_animation + ' animated');
                  // Remove animation class from an element to execute it multiple times when event is triggered.
                  clearClass(item_data);
                });
              }
            }
          });
        }
      });
    }
  };

  /**
   * Remove animation classes from an element.
   */
  function clearClass(item_data) {
    setTimeout(() => {
      $(item_data.animate_parent).find(item_data.section_identity).removeClass(item_data.section_animation + ' animated');
    }, 1000);
  }

  /**
   * Function use to identify the dom element visible or not
   */
  $.fn.visible = function () {

    var win = $(window);
    var viewport = {
      top: win.scrollTop(),
      left: win.scrollLeft()
    };
    viewport.right = viewport.left + win.width() - 100;
    viewport.bottom = viewport.top + win.height() - 100;

    var bounds = this.offset();
    bounds.right = bounds.left + this.outerWidth();
    bounds.bottom = bounds.top + this.outerHeight();

    return (!(viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom));
  };
})(jQuery, Drupal);
