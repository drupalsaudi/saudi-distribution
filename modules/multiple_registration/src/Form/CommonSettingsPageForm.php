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
 * Class CommonSettingsPageForm.
 *
 * @package Drupal\multiple_registration\Form
 */
class CommonSettingsPageForm extends ConfigFormBase {

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
   * Constructs a CommonSettingsPageForm object.
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
      'multiple_registration.common_settings_page_form_config',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'common_settings_page_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('multiple_registration.common_settings_page_form_config');

    $form['disable_main_register_page'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Disable main registration page'),
      '#description' => $this->t('Indicates whether main registration page will be accessible to anonymous user.'),
      '#default_value' => $config->get('multiple_registration_disable_main'),
    ];

    $form['enable_redirect_to_user_profile_when_user_logged_in'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable redirect to the user profile page for authenticated users'),
      '#description' => $this->t('Enables redirection to the user profile page when the user is logged in and tries to click on the registration link.'),
      '#default_value' => $config->get('enable_redirect_to_user_profile_when_user_logged_in'),
    ];

    $form['save'] = [
      '#type' => 'submit',
      '#attributes' => [
        'class' => ['button--primary'],
      ],
      '#value' => $this->t('Save'),
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
    $disable_main = $form_state->getValue('disable_main_register_page');
    $enable_redirect_to_user_profile_when_user_logged_in = $form_state->getValue('enable_redirect_to_user_profile_when_user_logged_in');
    $clicked_button = end($form_state->getTriggeringElement()['#parents']);
    switch ($clicked_button) {
      case 'save':
        $this->config('multiple_registration.common_settings_page_form_config')
          ->set('multiple_registration_disable_main', $disable_main)
          ->save();
        $this->config('multiple_registration.common_settings_page_form_config')
          ->set('enable_redirect_to_user_profile_when_user_logged_in', $enable_redirect_to_user_profile_when_user_logged_in)
          ->save();
        $this->routeBuilder->rebuild();
        $this->cacheRender->invalidateAll();
        break;
    }
    $form_state->setRedirect('multiple_registration.multiple_registration_list_index');
  }

}
