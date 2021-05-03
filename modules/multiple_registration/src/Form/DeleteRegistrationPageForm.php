<?php

namespace Drupal\multiple_registration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\multiple_registration\Controller\MultipleRegistrationController;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DeleteRegistrationPageForm.
 *
 * @package Drupal\multiple_registration\Form
 */
class DeleteRegistrationPageForm extends FormBase {

  /**
   * Multiple registration.
   *
   * @var \Drupal\multiple_registration\Controller\MultipleRegistrationController
   */
  protected $multipleRegistrationController;

  /**
   * Constructs a DeleteRegistrationPageForm object.
   *
   * @param \Drupal\multiple_registration\Controller\MultipleRegistrationController $multipleRegistrationController
   *   The multiple registration controller.
   */
  public function __construct(MultipleRegistrationController $multipleRegistrationController) {
    $this->multipleRegistrationController = $multipleRegistrationController;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('multiple_registration.controller_service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'delete_registration_page_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $rid = NULL) {

    if (!isset($rid)) {
      return FALSE;
    }
    $roles = user_role_names();
    if (!isset($roles[$rid])) {
      return FALSE;
    }
    $form['rid'] = ['#type' => 'value', '#value' => $rid];
    $form['message'] = [
      '#markup' => '<p>' . $this->t('Are you sure want to delete registration page for %role role?', ['%role' => $roles[$rid]]) . '</p>',
    ];
    $form['dont_remove'] = [
      '#type' => 'submit',
      '#value' => $this->t('No'),
    ];
    $form['remove'] = [
      '#type' => 'submit',
      '#value' => $this->t('Yes'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $rid = $form_state->getValue('rid');
    $clicked_button = end($form_state->getTriggeringElement()['#parents']);

    switch ($clicked_button) {

      case 'remove':
        if ($rid) {
          $this->multipleRegistrationController->removeRegisterPage($rid);

        }
        break;
    }
    $form_state->setRedirect('multiple_registration.multiple_registration_list_index');
  }

}
