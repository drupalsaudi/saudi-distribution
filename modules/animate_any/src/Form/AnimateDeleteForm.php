<?php

namespace Drupal\animate_any\Form;

use Drupal\Core\Database\Connection;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a confirmation form for deleting Animation data from Animation list.
 */
class AnimateDeleteForm extends ConfirmFormBase {

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
    return 'animate_delete_form';
  }

  public function getQuestion() {
    return t('Are you sure want to delete this record?');
  }

  public function getCancelUrl() {
    return new Url('animate_any.animate_list');
  }

  public function getDescription() {
    return t('This action cannot be undone.');
  }

  public function getConfirmText() {
    return $this->t('Delete');
  }

  public function getCancelText() {
    return $this->t('Cancel');
  }

  public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
    $this->id = $id;
    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $aid = $this->id;
    if (is_numeric($aid)) {
      // Delete configuration for parent.
      $this->deleteConfigValue($aid);
      // Delete database entry.
      $delete = $this->database->delete('animate_any_settings')->condition('aid', $aid)->execute();
      if ($delete) {
        $this->messenger()->addMessage($this->t('Record deleted successfully.'));
      }
    }
  }

  /**
   * Helper function to clear the configuration key value.
   * @param $aid
   */
  public function deleteConfigValue($aid) {
    // Get parent data to delete from configurations.
    $fetch = $this->database->select("animate_any_settings", "a");
    $fetch->fields('a', ['parent']);
    $fetch->condition('a.aid', $aid);
    $fetch_results = $fetch->execute()->fetchAssoc();
    $parent = $fetch_results['parent'];
    $parent_key = str_replace('.', '::', $parent);
    // Clear configuration.
    \Drupal::service('config.factory')->getEditable('animate_any.settings')
      ->clear($parent_key)->save();
  }

}
