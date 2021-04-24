About
=====

Adds a field display formatter to allow you to display field content using
FlexSlider. The module doesn't require Field UI to be enabled by default
(so you can leave it off once everything is configured) but it is recommended
to use to setup your display settings.

Usage
=====

Manage the fields on any entity (ex: node of type Article)

Ex: admin/structure/types/manage/article

Select any field of type "image" or "media" and set the display options to
"FlexSlider". Then select your option set in the display formatter settings.
That's it!


Responsive Images
============

The Responsive Image module is a core Drupal 8 module that provides an image
formatter and breakpoint mappings to output responsive images using the
HTML5 picture tag.

FlexSlider Fields provides a FlexSlider Responsive formatter that
utilizes the core responsive image formatter. This formatter is available for
multi-file image fields when the Responsive Image module is installed.

If the Responsive Image module is uninstalled after installing FlexSlider,
be sure to rebuild the cache to clean out any references to the FlexSlider
Responsive formatter.
