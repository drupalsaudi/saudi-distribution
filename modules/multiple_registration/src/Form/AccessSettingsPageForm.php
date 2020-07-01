<?php

namespace Drupal\multiple_registration\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\multiple_registration\Controller\MultipleRegistrationController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\ProxyClass\Routing\RouteBuilder;

/**
 * Class AccessSettingsPageForm.
 *
 * @package Drupal\multiple_registration\Form
 */
class AccessSettingsPageForm extends ConfigFormBase {

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
   * Constructs a AccessSettingsPageForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\multiple_registration\Controller\MultipleRegistrationController $multipleRegistrationController
   *   The multiple registration controller.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cacheBackend
   *   The cacheBackend service.
   * @param \Drupal\Core\ProxyClass\Routing\RouteBuilder $routerBuilder
   *   The routerBuilder service.
   */
  public function __construct(ConfigFactoryInterface $config_factory, MultipleRegistrationController $multipleRegistrationController, CacheBackendInterface $cacheBackend, RouteBuilder $routerBuilder) {
    parent::__construct($config_factory);
    $this->multipleRegistrationController = $multipleRegistrationController;
    $this->cacheRender = $cacheBackend;
    $this->routeBuilder = $routerBuilder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('multiple_registration.controller_service'),
      $container->get('cache.render'),
      $container->get('router.builder')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'multiple_registration.access_settings_page_form_config',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'access_settings_page_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('multiple_registration.access_settings_page_form_config');

    $form['multiple_registration_pages_white_list'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Multiple registration pages whitelist'),
      '#description' => $this->t('Select multiple registration pages which will be accessible to anonymous user.'),
      '#default_value' => $config->get('multiple_registration_pages_white_list'),
      '#options' => user_role_names(),
    ];

    // Hide authenticated and anonymous roles from the whitelist form to prevent
    // registration exceptions with service roles.
    $form['multiple_registration_pages_white_list']['anonymous'] = [
      '#access' => FALSE,
    ];
    $form['multiple_registration_pages_white_list']['authenticated'] = [
      '#access' => FALSE,
    ];

    $form['save'] = [
      '#type' => 'submit',
      '#attributes' => [
        'class' => ['button--primary'],
      ],
      '#value' => $this->t('Save access settings'),
    ];

    $form['do_nothing'] = [
      '#type' => 'submit',
      '#value' => $this->t('Cancel'),
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
    $white_list = $form_state->getValue('multiple_registration_pages_white_list');
    $clicked_button = end($form_state->getTriggeringElement()['#parents']);
    switch ($clicked_button) {
      case 'save':
        $this->config('multiple_registration.access_settings_page_form_config')
          ->set('multiple_registration_pages_white_list', $white_list)
          ->save();
        $this->routeBuilder->rebuild();
        $this->cacheRender->invalidateAll();
        break;
    }
    $form_state->setRedirect('multiple_registration.multiple_registration_list_index');
  }

}
