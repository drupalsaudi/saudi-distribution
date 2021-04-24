<?php

namespace Drupal\Tests\simple_block_layout_builder\FunctionalJavascript;

use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Drupal\Tests\contextual\FunctionalJavascript\ContextualLinkClickTrait;
use Drupal\node\Entity\NodeType;

/**
 * Tests Simple Block module integration with Layout Builder.
 *
 * @group simple_block
 */
class SimpleBlockLayoutBuilderTest extends WebDriverTestBase {

  use ContextualLinkClickTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    // @todo Remove this dependency in #3206088.
    // @see https://www.drupal.org/project/simple_block/issues/3206088
    'block',
    'contextual',
    'field_ui',
    'node',
    'simple_block_layout_builder',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'classy';

  /**
   * Tests the simple_block_layout_builder sub-module.
   */
  public function testIntegration(): void {
    NodeType::create(['type' => 'page'])->save();
    $this->drupalLogin($this->createUser([
      'access contextual links',
      // @todo Replace this with proper permission in #3206088.
      // @see https://www.drupal.org/project/simple_block/issues/3206088
      'administer blocks',
      'administer content types',
      'administer node display',
      'administer node fields',
      'configure any layout',
    ]));
    $this->drupalGet('/admin/structure/types/manage/page/display');

    $page = $this->getSession()->getPage();
    $assert = $this->assertSession();

    $this->submitForm(['layout[enabled]' => TRUE], 'Save');
    $this->clickLink('Manage layout');
    $this->clickLink('Add block');
    $assert->assertWaitOnAjaxRequest();
    $assert->linkExists('Create simple block');

    // Create a simple block.
    $page->clickLink('Create simple block');
    $assert->assertWaitOnAjaxRequest();

    $page->findField('Title')->setValue('Just a simple block');
    $this->assertJsCondition('jQuery("html:contains(\'just_a_simple_block\')")');
    $this->createScreenshot('/tmp/a.png');
    $page->findField('Content')->setValue('Lorem ipsum dolor sit amet');
    $page->pressButton('Save');
    $assert->assertWaitOnAjaxRequest();
    $this->drupalGet('/admin/structure/types/manage/page/display/default/layout');
    $assert->pageTextContains('Lorem ipsum dolor sit amet');

    // Edit the simple block using contextual links.
    $this->clickContextualLink('#layout-builder .block-simple-block', 'Configure', TRUE);
    $assert->assertWaitOnAjaxRequest();
    $page->findField('Content')->setValue('You know... the brown fox');
    $page->pressButton('Save');
    $assert->assertWaitOnAjaxRequest();
    $this->drupalGet('/admin/structure/types/manage/page/display/default/layout');
    $assert->pageTextContains('You know... the brown fox');

    // Save the layout and test a node.
    $page->pressButton('Save layout');
    $page->pressButton('Save');
    $this->drupalGet('/node/add/page');
    $this->submitForm(['title[0][value]' => 'Whatever'], 'Save');
    $assert->pageTextContains('Whatever has been created.');
    $assert->pageTextContains('You know... the brown fox');
    $node_url = $this->getSession()->getCurrentUrl();

    // Remove block from layout.
    $this->drupalGet('/admin/structure/types/manage/page/display/default/layout');
    $this->clickContextualLink('#layout-builder .block-simple-block', 'Remove block', TRUE);
    $assert->assertWaitOnAjaxRequest();
    $assert->pageTextContains('Are you sure you want to remove the Just a simple block block?');
    $assert->pageTextContains('This action cannot be undone.');
    $page->pressButton('Remove');
    $assert->assertWaitOnAjaxRequest();
    $this->drupalGet('/admin/structure/types/manage/page/display/default/layout');
    $assert->pageTextNotContains('You know... the brown fox');
    $page->pressButton('Save layout');
    $page->pressButton('Save');
    $this->drupalGet($node_url);
    $assert->pageTextNotContains('You know... the brown fox');
  }

}
