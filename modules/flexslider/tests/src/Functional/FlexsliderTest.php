<?php

namespace Drupal\Tests\flexslider\Functional;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\flexslider\Entity\Flexslider;
use Drupal\flexslider\FlexsliderDefaults;
use Drupal\Tests\BrowserTestBase;

/**
 * Test the FlexSlider presets, configuration options and permission controls.
 *
 * @group flexslider
 */
class FlexsliderTest extends BrowserTestBase {
  use StringTranslationTrait;

  /**
   * Our module dependencies.
   *
   * In Drupal 8's SimpleTest, we declare module dependencies in a public
   * static property called $modules. WebTestBase automatically enables these
   * modules for us.
   *
   * @var array
   */
  public static $modules = ['flexslider', 'flexslider_library_test'];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * User with permission to admin flexslider.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * User with permission to access administration pages.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $anyUser;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    // Create users.
    $this->adminUser = $this->drupalCreateUser(['administer flexslider'], NULL, TRUE);
    $this->anyUser = $this->drupalCreateUser(['access administration pages']);
  }

  /**
   * Admin Access test.
   */
  public function testAdminAccess() {

    // Login as the admin user.
    $this->drupalLogin($this->adminUser);

    // Load admin page.
    $this->drupalGet('admin/config/media/flexslider');
    $this->assertResponse(200);

    // Logout as admin user.
    $this->drupalLogout();

    // Login as any user.
    $this->drupalLogin($this->anyUser);

    // Attempt to load admin page.
    $this->drupalGet('admin/config/media/flexslider');
    $this->assertResponse(403);

  }

  /**
   * Test managing the optionset.
   */
  public function testOptionSetCrud() {
    // Login as the admin user.
    $this->drupalLogin($this->adminUser);
    $testsets = ['testset', 'testset2'];

    foreach ($testsets as $name) {
      // Create a new optionset with default settings.
      /** @var \Drupal\flexslider\Entity\Flexslider $optionset */
      $optionset = Flexslider::create(['id' => $name, 'label' => $name]);
      $this->assertNotEmpty($optionset->id() == $name, $this->t('Optionset object created: @name', ['@name' => $optionset->id()]));
      $this->assertNotEmpty($optionset->getOptions(), $this->t('Create optionset works.'));

      // Save the optionset to the database.
      $optionset->save();

      $this->assertNotEmpty($optionset, $this->t('Optionset saved to database.'));

      // Read the values from the database.
      $optionset = Flexslider::load($name);

      $this->assertIsObject($optionset, $this->t('Loaded option set.'));
      $this->assertEqual($name, $optionset->id(), $this->t('Loaded name matches: @name', ['@name' => $optionset->id()]));

      /** @var \Drupal\flexslider\Entity\Flexslider $default_optionset */
      $default_optionset = Flexslider::create();
      foreach ($default_optionset->getOptions() as $key => $value) {
        $this->assertEqual($value, $optionset->getOptions()[$key], $this->t('Option @option matches saved value.', ['@option' => $key]));
      }

    }

    // Load all optionsets.
    $optionsets = Flexslider::loadMultiple();
    $this->assertIsArray($optionsets, $this->t('Array of optionsets loaded'));
    $this->assertNotEmpty(count($optionsets) == 3, $this->t('Proper number of optionsets loaded (two created, one default): 3'));

    // Ensure they all loaded correctly.
    foreach ($optionsets as $optionset) {
      $this->assertNotEmpty($optionset->id(), $this->t('Loaded optionsets have a defined machine name'));
      $this->assertNotEmpty($optionset->label(), $this->t('Loaded optionsets have a defined human readable name (label)'));
      $this->assertTrue(!empty($optionset->getOptions()), $this->t('Loaded optionsets have a defined array of options'));
    }

    // Update the optionset.
    $test_options = $this->getTestOptions();
    $test_options = $test_options['valid'];

    // Load one of the test option sets.
    $optionset = Flexslider::load($testsets[0]);

    // Change the settings.
    $optionset->setOptions($test_options['set2'] + $optionset->getOptions());

    // Save the updated values.
    $saved = $optionset->save();

    $this->assertEqual($saved, SAVED_UPDATED, $this->t('Saved updates to optionset to database.'));

    // Load the values from the database again.
    $optionset = Flexslider::load($testsets[0]);

    // Compare settings to the test options.
    foreach ($test_options['set2'] as $key => $value) {
      $this->assertEqual($optionset->getOptions()[$key], $value, $this->t('Saved value matches set value: @key', ['@key' => $key]));
    }

    // Delete the optionset.
    $this->assertIsObject($optionset, $this->t('Optionset exists and is ready to be deleted.'));
    try {
      $optionset->delete();
      // Ensure the delete is successful.
      $this->pass($this->t('Optionset successfully deleted: @name', ['@name' => $optionset->id()]));
    }
    catch (\Exception $e) {
      $this->fail($this->t('Caught exception: @msg', ['@msg' => $e->getMessage()]));
    }

  }

  /**
   * Test the option set form.
   */
  public function testOptionSetForm() {

    // Login with admin user.
    $this->drupalLogin($this->adminUser);

    // ------------ Test Option Set Add ------------ //
    // Load create form.
    $this->drupalGet('admin/config/media/flexslider/add');
    $this->assertResponse(200);

    // Save new optionset.
    $optionset = [];
    $optionset['label'] = $this->t('testset');
    $optionset['id'] = 'testset';
    $this->drupalPostForm('admin/config/media/flexslider/add', $optionset, $this->t('Save'));

    $this->assertResponse(200);
    $this->assertText('Created the testset FlexSlider optionset.');

    // Attempt to save option set of the same name again.
    $this->drupalPostForm('admin/config/media/flexslider/add', $optionset, $this->t('Save'));
    $this->assertResponse(200);
    $this->assertText('The machine-readable name is already in use. It must be unique.');

    // ------------ Test Option Set Edit ------------ //
    // Attempt to save each option value.
    $options = $this->getTestOptions();

    foreach ($options['valid'] as $testset) {
      $this->drupalPostForm('admin/config/media/flexslider/default', $testset, $this->t('Save'));
      $this->assertResponse(200);

      // Test saved values loaded into form.
      $this->drupalGet('admin/config/media/flexslider/default');
      $this->assertResponse(200);
      foreach ($testset as $key => $option) {
        $this->assertFieldByName($key, $option);
      }
    }

    // ------------ Test Option Set Delete ------------ //.
    $testset = Flexslider::load('testset');

    // Test the delete workflow.
    $this->drupalGet("admin/config/media/flexslider/{$testset->id()}/delete");
    $this->assertResponse(200);
    $this->assertText("Are you sure you want to delete {$testset->label()}?");
    $this->drupalPostForm("admin/config/media/flexslider/{$testset->id()}/delete", [], $this->t('Delete'));
    $this->assertResponse(200);
    $this->assertText("Deleted the {$testset->label()} FlexSlider optionset.");

  }

  /**
   * Test settings and their affect on loading FlexSlider assets.
   *
   * This works since aggregation is off by default in SimpleTest.
   */
  public function testSettings() {

    // Login with admin user.
    $this->drupalLogin($this->adminUser);

    // Debug flag initially off.
    $this->assertRaw('libraries/flexslider/jquery.flexslider-min.js');

    // Change the debug settings.
    $this->drupalGet('admin/config/media/flexslider/advanced');
    $settings['flexslider_debug'] = TRUE;
    $this->drupalPostForm('admin/config/media/flexslider/advanced', $settings, $this->t('Save configuration'));

    $this->assertResponse(200);
    $this->assertText('The configuration options have been saved.');

    $this->drupalGet('user/' . $this->adminUser->id());

    $this->assertRaw('libraries/flexslider/jquery.flexslider.js');

    // Test the css settings.
    // Show that the css files are originally loaded.
    $this->assertRaw('libraries/flexslider/flexslider.css');
    $this->assertRaw('flexslider/assets/css/flexslider_img.css');

    // Turn off the css.
    $this->drupalGet('admin/config/media/flexslider/advanced');
    $settings = [
      'flexslider_css' => FALSE,
      'integration_css' => FALSE,
    ];
    $this->drupalPostForm('admin/config/media/flexslider/advanced', $settings, $this->t('Save configuration'));

    $this->drupalGet('user/' . $this->adminUser->id());

    // Show css is not loaded when flags are off.
    $this->assertNoRaw('libraries/flexslider/flexslider.css');
    $this->assertNoRaw('flexslider/assets/css/flexslider_img.css');
  }

  /**
   * Get the test configuration options.
   *
   * @return array
   *   Returns an array of options to test saving.
   */
  protected function getTestOptions() {
    // Valid option set data.
    $valid = [
      'set1' => FlexsliderDefaults::defaultOptions(),
      'set2' => [
        'animation' => 'slide',
        'startAt' => 4,
        // @todo add more option tests
      ],
    ];

    // Invalid edge cases.
    $error = [];

    return ['valid' => $valid, 'error' => $error];
  }

}
