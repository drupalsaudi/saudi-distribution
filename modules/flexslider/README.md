About
=====
Integrates the FlexSlider library into Drupal.

Current Options
---------------
Allows you to use FlexSlider in a few different ways

- As a library to be used with any other theme or module by calling
flexslider_add() (N.B. This returns an array to be used as #attached on your
own render array).
- Integrates with Fields (flexslider_fields)
- Adds a Views display mode (flexslider_views)

About FlexSlider
----------------

Library available at https://github.com/woothemes/FlexSlider

- Simple, semantic markup
- Supported in all major browsers
- Horizontal/vertical slide and fade animations
- Multiple slider support, Callback API, and more
- Hardware accelerated touch swipe support
- Custom navigation options
- Use any html elements in the slides
- Built for beginners and pros, alike
- Free to use under the GPLv2+ license

Installation
============

Dependencies
------------

- [FlexSlider Library](https://github.com/woothemes/FlexSlider)

The [Libraries API](http://drupal.org/project/libraries) module is no longer
required if you are using Drupal 8.9+, OR you have put the flexslider
library in the standard location. i.e. '[DRUPAL ROOT]/libraries')

Tasks
-----

1. Download the FlexSlider library from
https://github.com/woothemes/FlexSlider
(To use Composer instead, see instructions in the Composer section below)
2. Unzip the file and rename the folder to "flexslider" (pay attention to the
case of the letters)
3. Put the folder in one of the following places relative to drupal root.
    - libraries (this is the standard location)
    - profiles/PROFILE-NAME/libraries
    - sites/all/libraries (ONLY if Libraries API is installed)
    - sites/default/libraries
    - sites/SITE-NAME/libraries
4. The following files are required (last file is required for javascript
debugging)
    - jquery.flexslider-min.js
    - flexslider.css
    - jquery.flexslider.js
5. Ensure you have a valid path similar to this one for all files
    - Ex: libraries/flexslider/jquery.flexslider-min.js

That's it!


Composer
----------
Composer may be used to download the library as follows...

1. Add the following to composer.json _installer-paths_ section
(if not already added)
  `
    "libraries/{$name}": ["type:drupal-library"]
  `

2. Add the FlexSlider Library package to your composer file. Use _ONE_ of the
following methods.
    * Use https://github.com/balbuf/drupal-libraries-installer
    OR
    * Add the following to composer.json _repositories_ section
    (your version may differ)


        {
          "type": "package",
          "package": {
            "name": "woocommerce/flexslider",
            "version": "2.7.2",
            "type": "drupal-library",
            "source": {
              "url": "https://github.com/woocommerce/FlexSlider.git",
              "type": "git",
              "reference": "2.7.2"
            }
          }
        }

3. Open a command line terminal and navigate to the same directory as your
composer.json file and run
  `
    composer require woocommerce/flexslider:~2.0
  `

Drush Make (Drush 8.x and below only)
-------------------------------------

You can also use Drush Make to download the library automatically. Simply
copy/paste the 'flexslider.make.example' to 'flexslider.make' or copy the
contents of the make file into your own make file.

Usage
======

Option Sets
-----------

No matter how you want to use FlexSlider (with fields or views) you need to
define "option sets" to tell FlexSlider how you want it to display. An option
set defines all the settings for displaying the slider. Things like slide
direction, speed, starting slide, etc... You can define as many option sets as
you like and on top of that they're all exportable! Which means you can carry
configuration of your Flex Slider instances from one site to the next or
create features.

Go to admin/config/media/flexslider

From there you can edit the default option set and define new ones. These will
be listed as options in the various forms where you setup FlexSlider to
display.
NOTE: under advanced options, you can set a namespace prefix for the optionset.
This will allow you to build custom CSS for each optionset.  Start by copying
the flexslider_img.css from the assets subfolder to your theme.  Build new
custom CSS for each prefix in your optionsets.

Carousels
---------

Carousels can be created with Flexslider2 by setting an Item Width for images
and a Margin Width in the optionset.  Use the flexslider_thumbnail image style
and set your item width to fit the desired number of images into the div space
available.
NOTE: the margin width setting should correspond IN PIXELS to the margin widths
set by your img CSS in your theme.  This will allow Flexslider to properly
calculate the "total width" of the image+margins so that horizontal scrolling
behaves properly.

Flexslider Views
----------------

Flex Slider Views allows you to build views which display their results in
Flex Slider. Similarly to how you can output fields as an "HTML List" or
"Table", you can now select "Flex Slider" as an option.

Create or edit a view and ensure it can load a content type which contain
image fields. Set your display fields to include an image field. In the field
settings, DO NOT SET THE FORMATTER TO FLEXSLIDER. This will attempt to put Flex
Sliders inside other Flex Sliders and will just get messy. Ensure you don't
include any wrapper markup, labels or container markup for the field value
itself. Save your field.

Next, go to "Format" in the main Views windows. Click and select "Flex Slider",
then select your option set. Save your view and you should see your results
displayed in Flex Slider.

Debugging
---------

You can toggle the development version of the library in the administrative
settings page. This will load the unminified version of the library.  Uncheck
this when moving to a production site to load the smaller minified version.

CSS
---

This module comes with a css file that attempts to fix the issues with styling
the FlexSlider on Drupal sites. These fixes are a moving target, as the library
and Drupal tend to change quite often. You can opt out of loading this css as
well as the base css that comes with the library, on the administrative
settings page.

### Image Width/Height Attributes

If your images aren't resizing, ensure the width and height attributes are
removed. The module will attempt to remove them automatically on any image
matching the pattern

    ul.slides > li > img


Export API
==========

You can export your FlexSlider option presets using D8 Configuration Management
by going to admin/config/development/configuration/single/export and choosing
FlexSlider optionset as the Configuration type.

External Links
==============

- [Wiki Documentation for FlexSlider 2]
(https://github.com/woothemes/FlexSlider/wiki/FlexSlider-Properties)
