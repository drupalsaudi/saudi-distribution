
CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Installation
 * Usage
 * Credits


INTRODUCTION 8.2.x version
------------
Font Awesome (http://fontawesome.com) is the web's most popular icon set and
toolkit. This release of the Font Awesome Icons module supports Font Awesome
versions higher than 5.0. For older versions of Font Awesome, you should
download and install Font Awesome Icons 8.1.x. See the Font Awesome Icons
page on Drupal.org for more information.

"fontawesome" provides integration of "Font Awesome" with Drupal. Once enabled
"Font Awesome" icon fonts could be used as:

1. Directly inside of any HTML (node/block/view/panel). Inside HTML you can
   place Font Awesome icons just about anywhere with an <i> tag.

   Example for an info icon: <i class="fas fa-camera-retro"></i>

   See more examples of using "Font Awesome" within HTML at:
   https://fontawesome.com/how-to-use/on-the-web/referencing-icons/basic-use


INSTALLATION
------------

1. Using Drush (https://github.com/drush-ops/drush#readme)

    $ drush en fontawesome

    Upon enabling, this will also attempt to download and install the library
    in `/libraries/fontawesome`. If, for whatever reason, this process
    fails, you can re-run the library install manually by first clearing Drush
    caches:

    $ drush cc drush

    and then using another drush command:-

    (Drush 8)
    $ drush fa-download
    (Drush 9)
    $ drush fa:download

2. Manually

    a. Install the "Font Awesome" library following one of these 2 options:
       - run "drush fa-download" (recommended, it will download the right
         package and extract it at the right place for you.)
       - manual install: Download & extract "Font Awesome"
         (http://fontawesome.com) and place inside
         "/libraries/fontawesome" directory. The JS file should
         be at /libraries/fontawesome/js/all.js
         Direct link for downloading latest version (current is v5.13.1) is:
         https://use.fontawesome.com/releases/v5.13.1/fontawesome-free-5.13.1-web.zip
    b. Enable the module at Administer >> Site building >> Modules.


USAGE
_____
Font Awesome can be used in many ways - you can manually insert Font Awesome
tags wherever you see fit after enabling the module, but there are other ways
as well. See
  https://fontawesome.com/how-to-use/on-the-web/referencing-icons/basic-use
for information on basic usage.

CSS Pseudo-elements - if you are using the older version of Font Awesome, CSS
with webfonts, you can use CSS Pseudo-elements for inserting your icons rather
than the default method. See
  https://fontawesome.com/how-to-use/on-the-web/advanced/css-pseudo-elements
for more information on how to add the icons through CSS.

Font Awesome icon field - this module includes the option to add a Font Awesome
icon field to any of your content types without the need for coding.

Font Awesome CKEditor plugin - this module includes a CKEditor plugin which
will allow you to insert Font Awesome icons into any CKEditor text field with
the plugin enabled. It can be enabled under
  Configuration -> Content authoring -> Text formats and editors
From here, simply add the icon to your active toolbar (it looks like a flag).
Please note that in order to use SVG with JS version of Font Awesome, you will
need to either disable the "Correct faulty and chopped off HTML" filter, or you
will have to add the required SVG tags to the exception list. A list of SVG
tags can be found here:
  https://www.w3.org/TR/SVG11/eltindex.html
  or
  https://developer.mozilla.org/en-US/docs/Web/SVG/Element

TROUBLESHOOTING
---------------
If the Font Awesome module is installed but icons are not showing, try the
following steps:
1. Flush the Drupal cache
2. Check the status report for issues on the libary being loaded.
3. If you have chosen to load the library manually ("Load Font Awesome libary?"
  is disabled), confirm that your manual library is loaded properly.

CREDITS
-------
* Daniel Moberly https://drupal.org/u/danielmoberly
