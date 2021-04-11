<?php

namespace Drupal\simple_block_layout_builder\Event\Subscriber;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Ajax\AjaxHelperTrait;
use Drupal\Core\Routing\ResettableStackedRouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\layout_builder\SectionStorageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Alters the render array of Layout Builder ChooseBlockController::build().
 *
 * The Layout Builder module considers the Drupal core 'block_content' module,
 * the only block factory in this universe and hardcodes the 'Add custom block'
 * link, without giving a chance to a 3rd-party to add their own 'Add block'
 * link. For this reason we're listening to KernelEvents::VIEW event, intercept
 * the render array produced by ChooseBlockController::build() and add our link.
 *
 * @see https://www.drupal.org/project/drupal/issues/3206770
 * @see \Drupal\layout_builder\Controller\ChooseBlockController::build()
 */
class SimpleBlockAddControllerSubscriber implements EventSubscriberInterface {

  use AjaxHelperTrait;
  use StringTranslationTrait;

  /**
   * Current route match service.
   *
   * @var \Drupal\Core\Routing\ResettableStackedRouteMatchInterface
   */
  protected $routeMatch;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Constructs a new event subscriber service instance.
   *
   * @param \Drupal\Core\Routing\ResettableStackedRouteMatchInterface $route_match
   *   Current route match service.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   */
  public function __construct(ResettableStackedRouteMatchInterface $route_match, AccountInterface $current_user) {
    $this->routeMatch = $route_match;
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::VIEW => [['alterBuild', 50]],
    ];
  }

  /**
   * Alters the build produced by ChooseBlockController::build().
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent $event
   *   The kernel view event.
   */
  public function alterBuild(GetResponseForControllerResultEvent $event): void {
    $route_name = $event->getRequest()->attributes->get('_route');
    if ($route_name !== 'layout_builder.choose_block') {
      return;
    }

    $original_build = $event->getControllerResult();
    if (is_array($original_build)) {
      $section_storage = $this->routeMatch->getParameter('section_storage');
      if (!$section_storage instanceof SectionStorageInterface) {
        throw new \InvalidArgumentException("Parameter 'section_storage' should implement \Drupal\layout_builder\SectionStorageInterface");
      }

      $build = [];
      // Always keep 'add_block' on top, if exists.
      if (isset($original_build['add_block'])) {
        $build['add_block'] = $original_build['add_block'];
        unset($original_build['add_block']);
      }
      $build['add_simple_block'] = [
        '#type' => 'link',
        '#title' => $this->t('Create simple block'),
        '#url' => Url::fromRoute('simple_block_layout_builder.edit_block', [
          'section_storage_type' => $section_storage->getStorageType(),
          'section_storage' => $section_storage->getStorageId(),
          'delta' => $this->routeMatch->getParameter('delta'),
          'region' => $this->routeMatch->getParameter('region'),
          'uuid' => NULL,
          'simple_block' => NULL,
        ]),
        '#attributes' => $this->getAttributes(),
        '#access' => $this->currentUser->hasPermission('administer blocks'),
      ];
      $event->setControllerResult($build + $original_build);
    }
  }

  /**
   * Get links attributes.
   *
   * @return array
   *   The attributes array.
   */
  protected function getAttributes(): array {
    $attributes = [
      'class' => ['inline-block-create-button'],
    ];
    if ($this->isAjax()) {
      $attributes = NestedArray::mergeDeep($attributes, [
        'class' => ['use-ajax'],
        'data-dialog-type' => 'dialog',
        'data-dialog-renderer' => 'off_canvas',
      ]);
    }
    return $attributes;
  }

}
