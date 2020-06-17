<?php

namespace Drupal\simple_block\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests Simple Block module.
 *
 * @group simple_block
 */
class SimpleBlockTest extends WebTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['simple_block_test', 'simple_block', 'filter'];

  /**
   * Tests if a block provided through a config entity is showed on the site.
   */
  public function testBlockDisplay() {
    $block = $this->drupalPlaceBlock('simple_block:foo');
    $this->drupalGet('<front>');
    $this->assertBlockAppears($block);
    $this->assertText('Just a simple block...');
  }

}
