<?php

namespace Drupal\Tests\simple_block\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\block\Functional\AssertBlockAppearsTrait;

/**
 * Tests Simple Block module.
 *
 * @group simple_block
 */
class SimpleBlockTest extends BrowserTestBase {

  use AssertBlockAppearsTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = ['simple_block_test', 'simple_block', 'filter'];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'classy';

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->user = $this->drupalCreateUser(['administer site configuration']);
    $this->drupalLogin($this->user);
  }

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
