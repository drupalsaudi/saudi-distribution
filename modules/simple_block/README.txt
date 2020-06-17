Description
===========

Missing the Drupal 7 simple blocks that were so easy to export with FE Block
(fe_block) module? Here's Simple Block, a module that lets you define simple
exportable blocks that have only a title and a formatted text. Each block is
identified by an unique user configurable machine name string.

Unlike the core Block Content (block_content), this module stores the blocks as
config entities making the import/export trivial.

Usage
=====

1. Provide Default Blocks.

   Just add the blocks as YAMLs in your module config/install.
   Example (simple_block.simple_block.foo.yml):

   langcode: en
   status: true
   id: foo
   title: 'The foo block'
   content:
     value: 'Just a simple block...'
     format: plain_text

2. Export Blocks

   Use the 'config' module to export blocks as config entities from the
   interface, at /admin/config/development/configuration/single/export.

3. Add/Edit/Delete Blocks

   Go to /admin/structure/block/simple-block and use the provided UI.

Author
======

Claudiu Cristea | https://www.drupal.org/u/claudiu.cristea
