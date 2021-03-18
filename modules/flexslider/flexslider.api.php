<?php

/**
 * @file
 * API documentation for FlexSlider.
 *
 * By design, FlexSlider should be entirely configurable from the web interface.
 * However some implementations may require to access the FlexSlider library
 * directly by using flexslider_add(). This will return an attached array that
 * you may add to a render array.
 *
 * Here are some sample uses of flexslider_add().
 */

/**
 * Attach flexslider to an element using the specified option set.
 *
 * This call will look for an HTML element with id attribute of "my_image_list"
 * and return the JS settings to initialize FlexSlider on it using the option
 * set named "default".
 */
$attached = flexslider_add('my_image_list', 'default');

/**
 * Attach flexslider to an element using the library defaults.
 *
 * You also have the option of skipping the option set parameter if you want
 * to run with the library defaults.
 */
$attached = flexslider_add('my_image_list');

/**
 * Attach the flexslider library.
 *
 * Finally, you can simply attach the library.
 * This method would assume you would take care of
 * initializing a FlexSlider instance in your theme or custom javascript
 * file.
 *
 * Ex: $('#slider').flexslider();
 */
$attached = flexslider_add();
