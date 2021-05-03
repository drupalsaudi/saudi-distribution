<?php

namespace Drupal\animate_any\Form;

use Drupal\Core\Database\Connection;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a confirmation form for import Animation data.
 */
class ConfigImportForm extends ConfirmFormBase {

  private $id;

  /**
   * @var \Drupal\Core\Database\Connection
   */
  private $database;

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * Class constructor.
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  public function getFormId() {
    return 'animate_import_form';
  }

  public function getQuestion() {
    return t('Import configurations to database');
  }

  public function getCancelUrl() {
    return new Url('animate_any.animate_any_form');
  }

  public function getDescription() {
    $output = '';
    $output .= '<p>' . t('Note: Animate any settings will be imported to database from active configurations not from your sync directory') . '</p>';
    $output .= '<ol>';
    $output .= '<li>' . t('Make sure you have imported the configuration from your sync directory to active configuration.') . '</li>';
    $output .= '<li>' . t('"animate_any_settings" table will be truncate.') . '</li>';
    $output .= '<li>' . t('This will import all your active configuration of "animate_any.settings" to db table "animate_any_settings".') . '</li>';
    $output .= '</ol>';
    return $output;
  }

  public function getConfirmText() {
    return $this->t('Import');
  }

  public function getCancelText() {
    return $this->t('Cancel');
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    try {
      // Get active configurations.
      $config = \Drupal::config('animate_any.settings');
      $config_data = $config->getRawData();
      if (!empty($config_data)) {
        // Empty db table animate_any_settings.
        $this->database->truncate('animate_any_settings')->execute();
        // Insert every single data.
        foreach ($config_data as $data) {
          $this->database->insert('animate_any_settings')
            ->fields([
              'parent' => $data['parent'],
              'identifier' => $data['identifier']
            ])->execute();
        }
        $this->messenger()->addMessage($this->t('Configuration setting imported successfully to database.'));
      }
      else {
        $this->messenger()->addWarning($this->t('Active configurations are empty.'));
      }
    } catch (\Exception $e) {
      $this->messenger()->addError($this->t($e->getMessage()));
    }
  }
}
