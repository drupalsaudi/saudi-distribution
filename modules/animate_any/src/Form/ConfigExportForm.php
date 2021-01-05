<?php

namespace Drupal\animate_any\Form;

use Drupal\Core\Database\Connection;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a confirmation form for export Animation data.
 */
class ConfigExportForm extends ConfirmFormBase {

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
    return 'animate_export_form';
  }

  public function getQuestion() {
    return t('Export configurations from database');
  }

  public function getCancelUrl() {
    return new Url('animate_any.animate_any_form');
  }

  public function getDescription() {
    $output = '';
    $output .= '<p>' . t('Note: Animate any settings will be exported from database to active configurations not to your sync directory.') . '</p>';
    $output .= '<ol>';
    $output .= '<li>' . t('This will export your db table "animate_any_settings" to active configuration of "animate_any.settings"') . '</li>';
    $output .= '<li>' . t('After this configuration export, please export active configuration from "Configuration synchronisation" to your sync directory.') . '</li>';
    $output .= '</ol>';
    return $output;
  }

  public function getConfirmText() {
    return $this->t('Export');
  }

  public function getCancelText() {
    return $this->t('Cancel');
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Delete active configurations for animate any.
    \Drupal::configFactory()->getEditable('animate_any.settings')->delete();
    // Get all data from table.
    $fetch = $this->database->select("animate_any_settings", "a");
    $fetch->fields('a', ['parent', 'identifier']);
    $results = $fetch->execute()->fetchAll();
    if (!empty($results)) {
      foreach ($results as $data) {
        $this->setConfigValue($data->parent, $data->identifier);
      }
      $this->messenger()->addMessage($this->t('Configuration setting exported successfully to active configuration.'));
    }
    else {
      $this->messenger()->addWarning($this->t('No configurations found in database.'));
    }
  }

  /**
   * Set animation values in configuration.
   * @param $parent
   * @param $identifiers
   */
  public function setConfigValue($parent, $identifiers) {
    $parent_key = str_replace('.', '::', $parent);
    $animate_data = ['parent' => $parent, 'identifier' => $identifiers];
    \Drupal::service('config.factory')->getEditable('animate_any.settings')
      ->set($parent_key, $animate_data)->save();
  }
}
