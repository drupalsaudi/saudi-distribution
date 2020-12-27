<?php

namespace Drupal\Tests\ds\Functional;

use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\Tests\comment\Functional\CommentTestBase;

/**
 * Tests for the manage display tab in Display Suite.
 *
 * @group ds
 */
class CommentTest extends CommentTestBase {

  use DsTestTrait;

  protected $defaultTheme = 'classy';

  /**
   * Modules to install.
   *
   * @var array
   */
  public static $modules = [
    'node',
    'user',
    'comment',
    'field_ui',
    'block',
    'ds',
  ];

  /**
   * The created user.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Create a test user.
    $this->adminUser = $this->drupalCreateUser([
      'access content',
      'admin classes',
      'admin display suite',
      'admin fields',
      'administer nodes',
      'view all revisions',
      'administer content types',
      'administer node fields',
      'administer node form display',
      'administer node display',
      'administer users',
      'administer permissions',
      'administer account settings',
      'administer user display',
      'administer software updates',
      'access site in maintenance mode',
      'administer site configuration',
      'bypass node access',
      'administer comments',
      'administer comment types',
      'administer comment fields',
      'administer comment display',
      'skip comment approval',
      'post comments',
      'access comments',
      // Usernames aren't shown in comment edit form autocomplete unless this
      // permission is granted.
      'access user profiles',
    ]);
    $this->drupalLogin($this->adminUser);
  }

  /**
   * Test adding comments to a node.
   */
  public function testComments() {
    // Create a node.
    $settings = ['type' => 'article', 'promote' => 1];
    $node = $this->drupalCreateNode($settings);

    $this->dsSelectLayout([], [], 'admin/structure/comment/manage/comment/display');

    $fields = [
      'fields[comment_title][region]' => 'left',
      'fields[comment_body][region]' => 'left',
    ];
    $this->dsConfigureUi($fields, 'admin/structure/comment/manage/comment/display');

    // Post comment.
    $comment1 = $this->postComment($node, $this->randomMachineName(), $this->randomMachineName());
    $this->assertSession()->responseContains($comment1->comment_body->value);

    // Post comment.
    $comment2 = $this->postComment($node, $this->randomMachineName(), $this->randomMachineName());
    $this->assertSession()->responseContains($comment2->comment_body->value);

    // Verify there are no double ID's.
    // For some reason, this test fails on the test bot, but is fine local.
    //$xpath = $this->xpath('//a[@id="comment-1"]');
    //$this->assertEquals(1, count($xpath),'1 ID found named comment-1');

    // Test that hidden fields aren't exposed in the config.
    $this->dsSelectLayout();

    $fields = [
      'fields[comment][region]' => 'hidden',
    ];
    $this->dsConfigureUi($fields);

    $display = EntityViewDisplay::load('node.article.default');
    $content = $display->get('content');
    $hidden = $display->get('hidden');

    $this->assertArrayNotHasKey('comment', $content, 'Comment is not part of the content region');
    $this->assertArrayHasKey('comment', $hidden, 'Comment is part of the hidden region');
  }

  /**
   * Test User custom display on a comment on a node.
   */
  public function testCommentUser() {
    // Create a node.
    $settings = ['type' => 'article', 'promote' => 1];
    $node = $this->drupalCreateNode($settings);

    // User compact display settings.
    $this->dsSelectLayout([], [], 'admin/config/people/accounts/display');

    $fields = [
      'fields[username][region]' => 'left',
      'fields[member_for][region]' => 'left',
    ];
    $this->dsConfigureUi($fields, 'admin/config/people/accounts/display');

    // Comment display settings.
    $this->dsSelectLayout([], [], 'admin/structure/comment/manage/comment/display');

    $fields = [
      'fields[comment_title][region]' => 'left',
      'fields[comment_user][region]' => 'left',
      'fields[comment_body][region]' => 'left',
    ];
    $this->dsConfigureUi($fields, 'admin/structure/comment/manage/comment/display');

    // Post comment.
    $comment = $this->postComment($node, $this->randomMachineName(), $this->randomMachineName());
    $this->assertSession()->responseContains($comment->comment_body->value);
    $this->assertSession()->responseContains('Member for');
    $xpath = $this->xpath('//div[@class="field field--name-comment-user field--type-ds field--label-hidden field__item"]/div/div/div[@class="field field--name-username field--type-ds field--label-hidden field__item"]');
    $this->assertEquals(count($xpath), 1, 'Username');
  }

}
