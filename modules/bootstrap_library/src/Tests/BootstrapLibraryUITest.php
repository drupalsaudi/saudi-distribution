<?php

/**
 * @file
 * Contains \Drupal\bootstrap_library\Tests\BootstrapLibraryUITest
 */

namespace Drupal\bootstrap_library\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests that the Bootstrap Library configuration UI exists and stores data correctly.
 *
 * @group bootstrap_library
 */
class BootstrapLibraryUITest extends WebTestBase {

  /**
   * Modules to install.
   *
   * @var array
   */
  public static $modules = array('bootstrap_library');

  /**
   * An administrative user to configure the test environment.
   */
  protected $adminUser;

  protected function setUp() {
    parent::setUp();
    // Create and log in an administrative user.
    $this->adminUser = $this->drupalCreateUser(array(
      'administer site configuration',
    ));
    $this->drupalLogin($this->adminUser);
  }

  /**
   * Test block demo page exists and functions correctly.
   */
  public function testBootstrapLibraryAdminUiPage() {
    $field_id = 'edit-bootstrap';
    $this->drupalGet('admin/config/development/bootstrap_library');
    $this->assertFieldByName('bootstrap');
    $this->assertOptionSelected($field_id, 0);
    $this->assertOptionByText($field_id, 'Load locally');
  }

}
