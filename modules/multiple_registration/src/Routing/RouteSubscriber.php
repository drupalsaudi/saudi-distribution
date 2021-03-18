<?php

namespace Drupal\multiple_registration\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouteCollection;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a Drupal\multiple_registration\Routing\RouteSubscriber object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory')
    );
  }

  /**
   * Altering route for the user.register.
   */
  protected function alterRoutes(RouteCollection $collection) {
    // Access denied to '/user/register' when corresponded option is enabled.
    if ($route = $collection->get('user.register')) {
      $disableMain = $this->configFactory->get('multiple_registration.common_settings_page_form_config')
        ->get('multiple_registration_disable_main');
      if ($disableMain === 1) {
        $route->setRequirement('_access', 'FALSE');
      }
    }
  }

}
