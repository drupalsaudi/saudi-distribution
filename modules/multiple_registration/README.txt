CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Requirements
 * Installation
 * Configuration
 * Maintainers


INTRODUCTION
------------

This module adds the ability to create role-specific registration pages.

Using this module you will be able to configure registration form fields
to show them on role-specific registration pages.
Also, you can disable default registration page or hide some
"role registration page" if it's needed.
You can set the "required" parameter for the field by special role.

 * For a full description of the module, visit the project page:
   https://www.drupal.org/project/multiple_registration

 * To submit bug reports and feature suggestions, or to track changes:
   https://www.drupal.org/project/issues/multiple_registration


REQUIREMENTS
------------

This module requires no modules outside of Drupal core.


INSTALLATION
------------

 * Install the Multiple Registration module as you would normally install a
   contributed Drupal module. Visit https://www.drupal.org/node/1897420 for
   further information.


CONFIGURATION
-------------

    1. Navigate to Administration > Extend and enable the module.
    2. Navigate to Administration > People > Roles and add a new role (Remember
       to create new user roles, because standard drupal roles do not
       accommodate the feature.) Save.
    3. Navigate to Administration > Configuration > People > Account settings to
       edit registration forms. Save configuration.
    4. In the Operations drop down for the new role, select "Add own
       registration page" to configure a page path. Save configuration.
    5. Navigate to Administration > Configuration > People > Multiple
       registrations pages for configuration.


MAINTAINERS
-----------

 * Yaroslav Samoylenko (ysamoylenko) - https://www.drupal.org/u/ysamoylenko
 * Alex Liannoy - https://www.drupal.org/u/alex-liannoy
 * Oleksandr Ivanchenko - https://www.drupal.org/u/oleksandr-ivanchenko

Supporting organizations:

 * ZANZARRA - https://www.drupal.org/zanzarra
 * EPAM Systems - https://www.drupal.org/epam-systems
