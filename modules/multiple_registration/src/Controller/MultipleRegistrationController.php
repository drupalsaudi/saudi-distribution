<?php

namespace Drupal\multiple_registration\Controller;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\path_alias\AliasManager;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Link;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\user\Access\RegisterAccessCheck;
use Drupal\multiple_registration\AvailableUserRolesService;
use Drupal\Core\Messenger\Messenger;

/**
 * Class MultipleRegistrationController.
 *
 * @package Drupal\multiple_registration\Controller
 */
class MultipleRegistrationController extends ControllerBase {

  /**
   * Path pattern.
   */
  const MULTIPLE_REGISTRATION_SIGNUP_PATH_PATTERN = '/user/register/';

  /**
   * Registration id.
   */
  const MULTIPLE_REGISTRATION_GENERAL_REGISTRATION_ID = 'authenticated';

  /**
   * Pages config.
   *
   * @var \Drupal\Core\Config\Config
   */
  public $regPagesConfig;

  /**
   * Available user roles service.
   *
   * @var \Drupal\multiple_registration\AvailableUserRolesService
   */
  protected $availableUserRolesService;

  /**
   * The alias manager that caches alias lookups based on the request.
   *
   * @var \Drupal\path_alias\AliasManagerInterface
   */
  protected $aliasManager;

  /**
   * The route match service.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messengerService;

  /**
   * MultipleRegistrationController constructor.
   *
   * @param \Drupal\multiple_registration\AvailableUserRolesService $availableUserRolesService
   *   AvailableUserRoles Service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   Config factory.
   * @param \Drupal\path_alias\AliasManager $aliasManager
   *   Alias manager.
   * @param \Drupal\Core\Routing\CurrentRouteMatch $routeMatch
   *   RouteMatch service.
   * @param \Drupal\Core\Messenger\Messenger $messengerService
   *   Messenger service.
   */
  public function __construct(AvailableUserRolesService $availableUserRolesService, ConfigFactoryInterface $configFactory, AliasManager $aliasManager, CurrentRouteMatch $routeMatch, Messenger $messengerService) {
    $this->regPagesConfig = $configFactory->getEditable('multiple_registration.create_registration_page_form_config');
    $this->availableUserRolesService = $availableUserRolesService;
    $this->aliasManager = $aliasManager;
    $this->routeMatch = $routeMatch;
    $this->messengerService = $messengerService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('multiple_registration.service'),
      $container->get('config.factory'),
      $container->get('path_alias.manager'),
      $container->get('current_route_match'),
      $container->get('messenger')
    );
  }

  /**
   * Checks access for register page.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   *
   * @return \Drupal\Core\Access\AccessResult
   *   Returns access result.
   */
  public function access(AccountInterface $account) {
    $registerAccessCheck = new RegisterAccessCheck();
    $clear_roles_list = [];
    $page_access_config = $this->config('multiple_registration.access_settings_page_form_config');
    $page_access_roles_whitelist = $page_access_config->get('multiple_registration_pages_white_list');
    if (is_array($page_access_roles_whitelist)) {
      foreach ($page_access_roles_whitelist as $role) {
        if (!empty($role)) {
          $clear_roles_list[] = $role;
        }
      }
    }
    // Allow user to access this page if user is authenticated.
    if ($account->isAuthenticated()) {
      return AccessResult::allowed();
    }
    // Disabling non-whitelisted roles registration pages.
    $rid = $this->routeMatch->getParameter('rid');
    if ($rid !== NULL && !\in_array($rid, $clear_roles_list, TRUE)) {
      return AccessResult::forbidden();
    }
    // Check access by permissions system.
    return AccessResult::allowedIf(
         $account->hasPermission('administer multiple_registration')
         || $registerAccessCheck->access($account)->isAllowed()
    );
  }

  /**
   * Redirects the current user to its profile page if user has logged in.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Current user account object.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse|null
   *   Returns RedirectResponse object or NULL.
   */
  public function authenticatedUserRedirect(AccountInterface $account) {
    $is_redirect_enabled = FALSE;
    $config = $this->config('multiple_registration.common_settings_page_form_config');
    if (($config !== NULL) && ($config !== $config->isNew())) {
      $is_redirect_enabled = $config->get('enable_redirect_to_user_profile_when_user_logged_in');
    }
    if ($account->isAuthenticated() && $is_redirect_enabled) {
      return $this->redirect('user.page', ['user' => $account->id()])->send();
    }
    return NULL;
  }

  /**
   * Page with registration pages list.
   *
   * @return array
   *   Returns index.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function index() {
    $regPages = $this->availableUserRolesService->getRegistrationPages();
    if ($regPages) {
      foreach ($regPages as $rid => $role) {
        $row = [];
        $row[] = $role['role_name'];

        $path_alias = $this->aliasManager->getAliasByPath($role['url']);
        $row[] = $path_alias;

        if ($role['hidden'] === 1) {
          $isHiddenLabel = $this->t('Yes');
        }
        else {
          $isHiddenLabel = $this->t('No');
        }
        $row[] = $isHiddenLabel;
        $row[] = $role['form_mode_register'];
        $row[] = $role['form_mode_edit'];

        $edit_url = Url::fromRoute('multiple_registration.create_registration_page_form', ['rid' => $rid], [
          'attributes' => [
            'class' => 'use-ajax',
            'data-accepts' => 'application/vnd.drupal-modal',
            'data-dialog-type' => 'modal',
            'data-dialog-options' => '{"width": "50%"}',
          ],
        ]);
        $remove_url = Url::fromRoute('multiple_registration.delete_registration_page_form', ['rid' => $rid], [
          'attributes' => [
            'class' => 'use-ajax',
            'data-accepts' => 'application/vnd.drupal-modal',
            'data-dialog-type' => 'modal',
            'data-dialog-options' => '{"width": "50%"}',
          ],
        ]);
        $row[] = [
          'data' => [
            '#type' => 'dropbutton',
            '#links' => [
              'edit' => [
                'title' => $this->t('Edit'),
                'url' => $edit_url,
              ],
              'remove' => [
                'title' => $this->t('Remove'),
                'url' => $remove_url,
              ],
            ],
          ],
        ];
        $rows[] = ['data' => $row];
      }

      $header = [
        $this->t('Role'),
        $this->t('Registration page path'),
        $this->t('Hidden'),
        $this->t('Register form mode'),
        $this->t('Edit form mode'),
        ['data' => $this->t('Operations')],
      ];
      $output = [
        '#theme' => 'table',
        '#header' => $header,
        '#rows' => $rows,
        '#attributes' => ['id' => 'user-roles-reg-pages'],
        '#attached' => [
          'library' => [
            'core/drupal.dialog.ajax',
          ],
        ],
        '#empty' => $this->t('No custom Role registration pages defined'),
      ];
    }
    else {
      $add_reg_pages_link = Link::fromTextAndUrl($this->t('here'), Url::fromRoute('entity.user_role.collection'))->toString();
      $output = [
        '#markup' => $this->t('There are no additional registration pages created yet. You can add new pages %here', ['%here' => $add_reg_pages_link]),
      ];
    }

    $common_settings_url = Url::fromRoute('multiple_registration.common_settings_page_form', [], [
      'attributes' => [
        'class' => 'use-ajax',
        'data-accepts' => 'application/vnd.drupal-modal',
        'data-dialog-type' => 'modal',
        'data-dialog-options' => '{"width": "50%"}',
      ],
    ]
    );

    $access_settings_url = Url::fromRoute('multiple_registration.access_settings_page_form', [], [
      'attributes' => [
        'class' => 'use-ajax',
        'data-accepts' => 'application/vnd.drupal-modal',
        'data-dialog-type' => 'modal',
        'data-dialog-options' => '{"width": "50%"}',
      ],
    ]
    );
    // Adding reminder for configuring multiple registration pages access.
    $access_settings_reminder = Markup::create("Don't forget to configure " . Link::fromTextAndUrl($this->t('Multiple Registration pages access settings'), $access_settings_url)->toString() . ' before start using this module.');
    $this->messenger()->addWarning($access_settings_reminder);
    // Adding multiple registration setting links.
    $output['#suffix'] = '<p>' . Link::fromTextAndUrl($this->t('Common settings'), $common_settings_url)->toString() . '</p>';
    $output['#suffix'] .= '<p>' . Link::fromTextAndUrl($this->t('Multiple Registration pages access settings'), $access_settings_url)->toString() . '</p>';
    $output['#suffix'] .= '<p>' . Link::fromTextAndUrl($this->t('Go to Roles managing page'), Url::fromRoute('entity.user_role.collection'))->toString() . '</p>';

    return $output;
  }

  /**
   * Adds alias for registration page.
   *
   * @param string $source
   *   Source string.
   * @param string $alias
   *   Path alias string.
   *
   * @throws \Exception
   */
  public function addRegisterPageAlias($source, $alias) {
    $path_alias = $this->entityTypeManager()->getStorage('path_alias')->create([
      'path' => $source,
      'alias' => $alias,
      'langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED,
    ]);
    $path_alias->save();
  }

  /**
   * Removes registration page alias for role.
   *
   * @param int $rid
   *   Role ID.
   */
  public function removeRegisterPageAlias($rid) {
    $pages_config = $this->regPagesConfig;
    $source = $pages_config->get('multiple_registration_url_' . $rid);
    $path_alias_storage = $this->entityTypeManager()->getStorage('path_alias');
    $entities = $path_alias_storage->loadByProperties([
       'path' => $source,
       'langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED,
    ]);
    $path_alias_storage->delete($entities);
  }

  /**
   * Removes registration page for role.
   *
   * @param int $rid
   *   Role ID.
   */
  public function removeRegisterPage($rid) {
    $pages_config = $this->regPagesConfig;
    if ($pages_config->get('multiple_registration_url_' . $rid)) {
      $this->removeRegisterPageAlias($rid);
      $pages_config->clear('multiple_registration_path_' . $rid)->clear('multiple_registration_url_' . $rid)->save();
      $this->messengerService->addMessage($this->t('Registration page has been removed.'));
    }
    else {
      $this->messengerService->addError($this->t('Registration page has not been removed. There are no pages for this role.'));
    }
  }

  /**
   * Check is field available for role.
   *
   * @param array $fieldRoles
   *   Array with assigned roles for the fields.
   *
   * @return bool
   *   Returns access result.
   */
  public static function checkFieldAccess(array $fieldRoles) {
    $routeMatch = \Drupal::routeMatch();
    $roles = [];
    switch ($routeMatch->getRouteName()) {
      // Role page registration.
      case 'multiple_registration.role_registration_page':
        $roles = [$routeMatch->getParameter('rid')];
        break;

      // Default registration.
      case 'user.register':
        $roles = [self::MULTIPLE_REGISTRATION_GENERAL_REGISTRATION_ID];
        break;

      // User edit page.
      case 'entity.user.edit_form':
        $roles = $routeMatch->getParameter('user')->getRoles();
        if (!static::useRegistrationPage($roles)) {
          // Fall back to 'General registered users' if user does not have any
          // special role.
          $roles = [self::MULTIPLE_REGISTRATION_GENERAL_REGISTRATION_ID];
        }
        break;
    }

    $extractKeys = array_intersect($roles, $fieldRoles);

    if (!empty($extractKeys)) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Gets the title for registration page.
   */
  public function getRegisterPageTitle(RouteMatchInterface $route) {
    $role = $route->getRawParameter('rid');
    $roles = user_role_names();
    if (isset($roles[$role])) {
      return $this->t('Create new @role account', ['@role' => $roles[$role]]);
    }
    else {
      return $this->t('Role @role not found, you can use default registration page.', ['@role' => ucfirst($role)]);
    }
  }

  /**
   * Checks whether there're special registration pages for any of given roles.
   *
   * @param array $roles
   *   Array of role IDs.
   *
   * @return bool
   *   Whether there is a special registration form available for at least one
   *   of given roles.
   */
  protected static function useRegistrationPage(array $roles) {
    $pages_config = \Drupal::configFactory()->get('multiple_registration.create_registration_page_form_config');

    foreach ($roles as $rid) {
      if ($pages_config->get('multiple_registration_url_' . $rid)) {
        return TRUE;
      }
    }

    return FALSE;
  }

}
