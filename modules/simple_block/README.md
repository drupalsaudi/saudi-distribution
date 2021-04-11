Description
-----------

The module provides a block plugin that produces blocks based on config
entities. Unlike the core _Block Content_ (block_content), this module stores
the blocks as config entities rather than content. This has some advantages when
you need to provide blocks using the config management. Each block is identified
by a unique string ID machine name string and contains only a title, and a
formatted text area.

Layout Builder integration
--------------------------

By installing the _Simple Block + Layout Builder_ (simple_block_layout_builder)
sub-module, such blocks can be created directly in the Layout Builder UI.

Dependencies
------------

* Filter (filter),
* Block (block). This dependency will be removed in [#3206088](
  https://www.drupal.org/project/simple_block/issues/3206088),
* Layout Builder (layout_builder). Only for _Simple Block + Layout Builder_
  (simple_block_layout_builder) sub-module.

Usage
-----

* Visit the `/admin/structure/block/simple-block` UI and add blocks.
* Such blocks can be exported as config YAMLs in your config sync directory and
  deployed as configuration to other environments.
* In order to create such blocks directly from Layout Builder, install the
  simple_block_layout_builder sub-module. A new _Create simple block_ link will
  appear when clicking _Add block_ in a section. The content of the block can be
  also edited from the Layout Builder UI, by clicking the _Configure_ contextual
  menu link. Note that such blocks are not automatically deleted when they are
  removed from the layout. This will be handled in [#3206910](
  https://www.drupal.org/project/simple_block/issues/3206910).

Maintainers
-----------
 * Claudiu Cristea (author): https://www.drupal.org/u/claudiucristea
 * Neslee Canil Pinto: https://www.drupal.org/u/neslee-canil-pinto
