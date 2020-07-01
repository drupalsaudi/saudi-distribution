<?php

namespace Drupal\multiple_registration\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\multiple_registration\AvailableUserRolesService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines dynamic local tasks.
 */
class MultipleRegistrationLocalTasks extends DeriverBase implements ContainerDeriverInterface {

  use StringTranslationTrait;

  /**
   * Available user roles service.
   *
   * @var \Drupal\multiple_registration\AvailableUserRolesService
   */
  protected $availableUserRolesService;

  /**
   * The base plugin ID.
   *
   * @var string
   */
  protected $basePluginId;

  /**
   * MultipleRegistrationLocalTasks constructor.
   *
   * @param \Drupal\multiple_registration\AvailableUserRolesService $availableUserRolesService
   *   AvailableUserRolesService service.
   * @param string $base_plugin_id
   *   Base plugin id.
   */
  public function __construct(AvailableUserRolesService $availableUserRolesService, $base_plugin_id) {
    $this->availableUserRolesService = $availableUserRolesService;
    $this->base_plugin_id = $base_plugin_id;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
        $container->get('multiple_registration.service'), $base_plugin_id
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $regPages = $this->availableUserRolesService->getRegistrationPages();
    if (!empty($regPages)) {
      foreach ($regPages as $rid => $role) {
        if ($role['hidden'] === 1) {
          continue;
        }
        $this->derivatives[$rid] = [];
        $this->derivatives[$rid]['title'] = $this->t('Create new @role account', ['@role' => $role['role_name']]);
        $this->derivatives[$rid]['base_route'] = 'user.page';
        $this->derivatives[$rid]['route_name'] = 'multiple_registration.role_registration_page';
        $this->derivatives[$rid]['route_parameters'] = ['rid' => $rid];
      }
    }

    return parent::getDerivativeDefinitions($base_plugin_definition);
  }

}
