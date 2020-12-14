<?php

namespace Drupal\flexslider;

/**
 * Class FlexsliderDefaults.
 *
 * Defines the default settings for this module.
 */
class FlexsliderDefaults {

  /**
   * Returns default flexslider library options.
   */
  public static function defaultOptions() {
    return [
    // String Prefix string attached to the classes of all plugin elements.
      'namespace'           => 'flex-',
    // Selector Must match a simple pattern. '{container} > {slide}'.
      'selector'               => '.slides > li',
    // String Controls the animation type, "fade" or "slide".
      'animation'             => 'fade',
    // String Determines the easing method used in jQuery transitions.
      'easing'                  => 'swing',
    // String Controls the animation direction, "horizontal" or "vertical".
      'direction'               => 'horizontal',
    // Boolean Reverse the animation direction.
      'reverse'                 => FALSE,
    // Boolean Gives the slider a seamless infinite loop.
      'animationLoop'       => TRUE,
    // Boolean Animate height of slider smoothly for slides of varying height.
      'smoothHeight'        => FALSE,
    // Number The starting slide for the slider, in array notation.
      'startAt'                  => 0,
    // Boolean Setup a slideshow for the slider to animate automatically.
      'slideshow'              => TRUE,
    // Number Set the speed of the slideshow cycling, in milliseconds.
      'slideshowSpeed'      => 7000,
    // Number Set the speed of animations, in milliseconds.
      'animationSpeed'      => 600,
    // Number Set an initialization delay, in milliseconds.
      'initDelay'                   => 0,
    // Boolean Randomize slide order, on load.
      'randomize'                 => FALSE,
    // Boolean Pause the slideshow when interacting with control elements.
      'pauseOnAction'       => TRUE,
    // Boolean Pause the slideshow when hovering over slider,
    // then resume when no longer hovering.
      'pauseOnHover'           => FALSE,
    // Boolean Slider will use CSS3 transitions, if available.
      'useCSS'                     => TRUE,
    // Boolean Allow touch swipe navigation of the slider on enabled devices.
      'touch'                    => TRUE,
    // Boolean Prevents use of CSS3 3D Transforms, avoiding graphical glitches.
      'video'                    => FALSE,
    // Boolean Create navigation for paging control of each slide.
      'controlNav'                => TRUE,
    // Boolean Create previous/next arrow navigation.
      'directionNav'           => TRUE,
    // String Set the text for the "previous" directionNav item.
      'prevText'                   => 'Previous',
    // String Set the text for the "next" directionNav item.
      'nextText'                => 'Next',
    // Boolean Allow slider navigating via keyboard left/right keys.
      'keyboard'                => TRUE,
    // Boolean Allow keyboard navigation to affect multiple sliders.
      'multipleKeyboard'    => FALSE,
    // Boolean (Dependency) Allows slider navigating via mousewheel.
      'mousewheel'           => FALSE,
    // Boolean Create pause/play element to control slider slideshow.
      'pausePlay'              => FALSE,
    // String Set the text for the "pause" pausePlay item.
      'pauseText'                => 'Pause',
    // String Set the text for the "play" pausePlay item.
      'playText'                  => 'Play',
    // jQuery Object/Selector Container the navigation elements should be
    // appended to.
      'controlsContainer'       => ''    ,
    // jQuery Object/Selector Define element to be used in lieu of dynamic
    // controlNav.
      'manualControls'         => '',
    // Selector Mirror the actions performed on this slider with another slider.
      'sync'                            => '',
    // Selector Turn the slider into a thumbnail navigation for another slider.
      'asNavFor'                      => ''    ,
    // Number Box-model width of individual carousel items,
    // including horizontal borders and padding.
      'itemWidth'                => 0    ,
    // Number Margin between carousel items.
      'itemMargin'               => 0    ,
    // Number Minimum number of carousel items that should be visible.
      'minItems'                  => 0    ,
    // Number Maximum number of carousel items that should be visible.
      'maxItems'                => 0    ,
    // Number Number of carousel items that should move on animation.
      'move'                        => 0    ,
      'thumbCaptions' => FALSE,
      'thumbCaptionsBoth' => FALSE,
    ];
  }

}
