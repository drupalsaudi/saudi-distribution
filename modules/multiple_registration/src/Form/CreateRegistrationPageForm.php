<?php

namespace Drupal\multiple_registration\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\multiple_registration\Controller\MultipleRegistrationController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\ProxyClass\Routing\RouteBuilder;
use Drupal\Core\Entity\EntityDisplayRepository;

/**
 * Class CreateRegistrationPageForm.
 *
 * @package Drupal\multiple_registration\Form
 */
class CreateRegistrationPageForm extends ConfigFormBase {

  /**
   * Multiple registration.
   *
   * @var \Drupal\multiple_registration\Controller\MultipleRegistrationController
   */
  protected $multipleRegistrationController;

  /**
   * A cache backend interface instance.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cacheRender;

  /**
   * The route building service.
   *
   * @var \Drupal\Core\Routing\RouteBuilderInterface
   */
  protected $routeBuilder;

  /**
   * EntityDisplayRepository service.
   *
   * @var \Drupal\Core\Entity\EntityDisplayRepository
   */
  protected $entityDisplayRepository;

  /**
   * Constructs a CreateRegistrationPageForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\multiple_registration\Controller\MultipleRegistrationController $multipleRegistrationController
   *   The multiple registration controller.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cacheBackend
   *   The cacheBackend service.
   * @param \Drupal\Core\ProxyClass\Routing\RouteBuilder $routerBuilder
   *   The routerBuilder service.
   * @param \Drupal\Core\Entity\EntityDisplayRepository $entityDisplayRepository
   *   EntityDisplayRepository service.
   */
  public function __construct(ConfigFactoryInterface $config_factory, MultipleRegistrationController $multipleRegistrationController, CacheBackendInterface $cacheBackend, RouteBuilder $routerBuilder, EntityDisplayRepository $entityDisplayRepository) {
    parent::__construct($config_factory);
    $this->multipleRegistrationController = $multipleRegistrationController;
    $this->cacheRender = $cacheBackend;
    $this->routeBuilder = $routerBuilder;
    $this->entityDisplayRepository = $entityDisplayRepository;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('multiple_registration.controller_service'),
      $container->get('cache.render'),
      $container->get('router.builder'),
      $container->get('entity_display.repository')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'multiple_registration.create_registration_page_form_config',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'create_registration_page_form';
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
    $config = $this->config('multiple_registration.create_registration_page_form_config');
    $form['multiple_registration_path_' . $rid] = [
      '#type' => 'textfield',
      '#title' => $this->t('Registration page path'),
      '#description' => $this->t('Path for registration page.'),
      '#default_value' => $config->get('multiple_registration_path_' . $rid),
    ];

    $form_modes = $this->entityDisplayRepository->getFormModes('user');
    $form_modes_options = ['default' => $this->t('Default')];
    foreach ($form_modes as $key => $form_mode) {
      $form_modes_options[$key] = $form_mode['label'];
    }

    $form['multiple_registration_form_mode_register_' . $rid] = [
      '#type' => 'select',
      '#title' => $this->t('Form mode to render the Register form'),
      '#default_value' => $config->get('multiple_registration_form_mode_register_' . $rid) ?: 'register',
      '#options' => $form_modes_options,
    ];

    $form['multiple_registration_form_mode_edit_' . $rid] = [
      '#type' => 'select',
      '#title' => $this->t('Form mode to render the Edit form'),
      '#default_value' => $config->get('multiple_registration_form_mode_edit_' . $rid) ?: 'default',
      '#options' => $form_modes_options,
    ];

    $form['multiple_registration_hidden_' . $rid] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Hide registration form tab'),
      '#description' => $this->t('Indicates whether form will be accessible only by url.'),
      '#default_value' => $config->get('multiple_registration_hidden_' . $rid),
    ];

    $form['multiple_registration_url_' . $rid] = [
      '#type' => 'value',
      '#value' => MultipleRegistrationController::MULTIPLE_REGISTRATION_SIGNUP_PATH_PATTERN . $rid,
    ];

    return parent::buildForm($form, $form_state);
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
    parent::submitForm($form, $form_state);
    $rid = $form_state->getValue('rid');
    $source = $form_state->getValue('multiple_registration_url_' . $rid);
    $alias = $form_state->getValue('multiple_registration_path_' . $rid);
    $isHidden = $form_state->getValue('multiple_registration_hidden_' . $rid);
    $formModeRegister = $form_state->getValue('multiple_registration_form_mode_register_' . $rid);
    $formModeEdit = $form_state->getValue('multiple_registration_form_mode_edit_' . $rid);
    $this->config('multiple_registration.create_registration_page_form_config')
      ->set('multiple_registration_path_' . $rid, $alias)
      ->set('multiple_registration_url_' . $rid, $source)
      ->set('multiple_registration_hidden_' . $rid, $isHidden)
      ->set('multiple_registration_form_mode_register_' . $rid, $formModeRegister)
      ->set('multiple_registration_form_mode_edit_' . $rid, $formModeEdit)
      ->save();
    $this->multipleRegistrationController->addRegisterPageAlias($source, '/' . $alias);
    $this->routeBuilder->rebuild();
    $this->cacheRender->invalidateAll();
    $form_state->setRedirect('multiple_registration.multiple_registration_list_index');
  }

}
