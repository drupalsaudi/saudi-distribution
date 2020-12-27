<?php

namespace Drupal\ds_devel\Controller;

use Drupal\Component\Utility\Html;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns responses for DS devel routes.
 */
class DsDevelController extends ControllerBase {

  /**
   * The entity display repository.
   *
   * @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface
   */
  protected $entityDisplayRepository;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * DsDevelController constructor.
   *
   * @param \Drupal\Core\Entity\EntityDisplayRepositoryInterface $displayRepository
   *   The display repository.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   */
  public function __construct(EntityDisplayRepositoryInterface $displayRepository, RendererInterface $renderer) {
    $this->entityDisplayRepository = $displayRepository;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_display.repository'),
      $container->get('renderer')
    );
  }

  /**
   * Lists all instances of fields on any views.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   A RouteMatch object.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   *
   * @return array
   *   The Views fields report page.
   */
  public function entityMarkup(RouteMatchInterface $route_match, Request $request) {

    $parameter_name = $route_match->getRouteObject()
      ->getOption('_devel_entity_type_id');
    $entity = $route_match->getParameter($parameter_name);
    $entity_type_id = $entity->getEntityTypeId();

    $key = $request->query->get('key', 'default');

    $built_entity = $this->entityTypeManager()
      ->getViewBuilder($entity_type_id)
      ->view($entity, $key);
    $markup = $this->renderer->render($built_entity);

    $links = [];
    $active_view_modes = $this->entityDisplayRepository
      ->getViewModeOptionsByBundle($entity_type_id, $entity->bundle());
    foreach ($active_view_modes as $id => $label) {
      $links[] = [
        'title' => $label,
        'url' => Url::fromRoute("entity.$entity_type_id.devel_markup", [$entity_type_id => $entity->id(), 'key' => $id]),
      ];
    }

    $build['links'] = [
      '#theme' => 'links',
      '#links' => $links,
      '#prefix' => '<hr/><div>',
      '#suffix' => '</div><hr />',
    ];
    $build['markup'] = [
      '#markup' => '<code><pre>' . Html::escape($markup) . '</pre></code>',
      '#cache' => [
        'max-age' => 0,
      ],
      '#allowed_tags' => ['code', 'pre'],
    ];

    return $build;
  }

}
