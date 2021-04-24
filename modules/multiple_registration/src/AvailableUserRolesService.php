<?php

namespace Drupal\multiple_registration;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Class AvailableUserRolesService.
 *
 * @package Drupal\multiple_registration
 */
class AvailableUserRolesService {

  /**
   * The role storage used when changing the admin role.
   *
   * @var \Drupal\user\RoleStorageInterface
   */
  protected $entityTypeManager;

  /**
   * Config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * AvailableUserRolesService constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   EntityTypeManager Service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   Config factory.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, ConfigFactoryInterface $configFactory) {
    $this->entityTypeManager = $entityTypeManager;
    $this->configFactory = $configFactory;
  }

  /**
   * Get all roles with ability to create registration page.
   *
   * @return array
   *   Returns avaliable roles array.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getAvailableRoles() {
    $roles = user_role_names();
    $role_storage = $this->entityTypeManager->getStorage('user_role');
    $admin_role = $role_storage->getQuery()
      ->condition('is_admin', TRUE)
      ->execute();
    $admin_role = reset($admin_role);

    $notAvalible = [
      AccountInterface::ANONYMOUS_ROLE => $roles[AccountInterface::ANONYMOUS_ROLE],
      AccountInterface::AUTHENTICATED_ROLE => $roles[AccountInterface::AUTHENTICATED_ROLE],
    ];

    // Building not available roles list depending on selection of admin role.
    if (isset($roles[$admin_role])) {
      $notAvalible[$admin_role] = $roles[$admin_role];
    }

    return array_diff_assoc($roles, $notAvalible);
  }

  /**
   * Get all role ids for whom registration forms was created.
   *
   * @return mixed
   *   If registration forms exists, array of paths.
   *   In other situation - FALSE.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getRegistrationPages() {
    $roles = $this->getAvailableRoles();
    if (!empty($roles)) {
      $pages_config = $this->configFactory->getEditable('multiple_registration.create_registration_page_form_config');
      $reg_pages = [];
      foreach ($roles as $rid => $role_name) {
        if ($url = $pages_config->get('multiple_registration_url_' . $rid)) {
          $isHidden = $pages_config->get('multiple_registration_hidden_' . $rid);
          $formModeRegister = $pages_config->get('multiple_registration_form_mode_register_' . $rid) ?: 'register';
          $formModeEdit = $pages_config->get('multiple_registration_form_mode_edit_' . $rid) ?: 'default';
          $reg_pages[$rid]['url'] = $url;
          $reg_pages[$rid]['role_name'] = $role_name;
          $reg_pages[$rid]['hidden'] = $isHidden;
          $reg_pages[$rid]['form_mode_register'] = $formModeRegister;
          $reg_pages[$rid]['form_mode_edit'] = $formModeEdit;
        }
      }
      return $reg_pages;
    }
    return FALSE;
  }

}
