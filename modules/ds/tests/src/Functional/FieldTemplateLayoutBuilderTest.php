<?php

namespace Drupal\Tests\ds\Functional;

/**
 * Tests for display of nodes and fields.
 *
 * @group ds
 */
class FieldTemplateLayoutBuilderTest extends TestBase {

  /**
   * Modules to install.
   *
   * @var array
   */
  public static $modules = [
    'node',
    'user',
    'field_ui',
    'rdf',
    'quickedit',
    'taxonomy',
    'block',
    'ds',
    'ds_extras',
    'ds_test',
    'ds_switch_view_mode',
    'layout_discovery',
    'layout_builder',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setup() {
    parent::setup();

    // Enable field templates.
    \Drupal::configFactory()->getEditable('ds.settings')
      ->set('field_template', TRUE)
      ->set('ft-layout-builder', TRUE)
      ->save();

    $this->drupalPlaceBlock('local_tasks_block');

    // Create a nodes to test field_block templates on
    $this->createContentType([
      'type' => 'bundle_with_section_field',
      'name' => 'Bundle with section field',
    ]);
    $this->createNode([
      'type' => 'bundle_with_section_field',
      'title' => 'The first node title',
      'body' => [
        [
          'value' => 'The first node body',
        ],
      ],
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function testDsLbFieldTemplate() {
    $assert_session = $this->assertSession();
    $page = $this->getSession()->getPage();

    $this->drupalLogin($this->drupalCreateUser([
      'configure any layout',
      'administer node display',
      'administer node fields',
    ]));

    $field_ui_prefix = 'admin/structure/types/manage/bundle_with_section_field';
    $selector_prefix = 'settings[formatter][third_party_settings][ds][ft]';

    // From the manage display page, go to manage the layout.
    $this->drupalGet("$field_ui_prefix/display/default");
    $this->drupalPostForm(NULL, ['layout[enabled]' => TRUE], 'Save');
    $this->clickLink('Manage layout');

    // Add the title field_block.
    $this->clickLink('Add block');
    $this->clickLink('Title');

    $page->selectFieldOption($selector_prefix . '[id]', 'expert');
    $page->pressButton('Add block');

    // Back on the layout builder page, pull the block ID so we can edit the
    // expert settings which is normally loaded with ajax. This also tests that
    // the expert form is rendering correctly.
    $title_blocks = $this->cssSelect('.block-field-blocknodebundle-with-section-fieldtitle');
    $title_block = reset($title_blocks);
    $block_id = $title_block->getAttribute('data-layout-block-uuid');
    $this->drupalGet('layout_builder/update/block/defaults/node.bundle_with_section_field.default/0/content/' . $block_id);
    $page->checkField($selector_prefix . '[settings][ow]');
    $page->fillField($selector_prefix . '[settings][ow-cl]', 'ds-wrapper');
    $page->checkField($selector_prefix . '[settings][fis]');
    $page->fillField($selector_prefix . '[settings][fis-cl]', 'ds-field-items');
    $page->fillField($selector_prefix . '[settings][lb]', 'This is the label');
    $page->pressButton('Update');

    drupal_flush_all_caches();

    $this->drupalGet($this->getUrl());

    // Check that the custom label and classes were applied.
    $assert_session->pageTextContains('This is the label');
    $assert_session->elementExists('css', '.ds-wrapper');
    $assert_session->elementExists('css', '.ds-field-items');

    // Save the defaults.
    $page->pressButton('Save layout');

    // Check that the node also has the label and fields.
    $this->drupalGet('node/1');
    $assert_session->pageTextContains('The first node title');
    $assert_session->pageTextContains('This is the label');
    $assert_session->elementExists('css', '.ds-wrapper');
    $assert_session->elementExists('css', '.ds-field-items');
  }

}
