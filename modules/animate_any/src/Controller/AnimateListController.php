<?php

/**
 * @file
 * Contains \Drupal\animate_any\Controller\AnimateListController.
 */

namespace Drupal\animate_any\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Database\Connection;
use Drupal\Core\Render\RendererInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AnimateListController extends ControllerBase {

  /**
   * @var \Drupal\Core\Render\RendererInterface
   */
  private $renderer;

  /**
   * @var \Drupal\Core\Database\Connection
   */
  private $database;

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('renderer'),
      $container->get('database')
    );
  }

  /**
   * Class constructor.
   */
  public function __construct(RendererInterface $render, Connection $database) {
    $this->renderer = $render;
    $this->database = $database;
  }

  public function animate_list() {
    $header = $rows = [];
    $header[] = ['data' => $this->t('ID')];
    $header[] = ['data' => $this->t('Parent element')];
    $header[] = ['data' => $this->t('Identifiers')];
    $header[] = ['data' => $this->t('Operation')];

    // Fetch Animate Data.
    $fetch = $this->database->select("animate_any_settings", "a");
    $fetch->fields('a');
    $fetch->orderBy('aid', 'DESC');
    $table_sort = $fetch->extend('Drupal\Core\Database\Query\TableSortExtender')->orderByHeader($header);
    $pager = $table_sort->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit(10);
    $fetch_results = $pager->execute()->fetchAll();
    foreach ($fetch_results as $items) {
      $mini_header = [];
      $mini_header[] = ['data' => $this->t('Section')];
      $mini_header[] = ['data' => $this->t('Event')];
      $mini_header[] = ['data' => $this->t('Animation')];
      $mini_rows = [];
      $data = \json_decode($items->identifier);
      foreach ($data as $value) {
        $mini_rows[] = [$value->section_identity, $value->section_event, $value->section_animation];
      }
      $mini_output = [];
      $mini_output['mini_list'] = [
        '#theme' => 'table',
        '#header' => $mini_header,
        '#rows' => $mini_rows,
      ];

      $identifiers = $this->renderer->render($mini_output);

      $links = [];

      $links['edit'] = [
        'title' => $this->t('Edit'),
        'url' => Url::fromUri('internal:/admin/config/animate_any/edit/' . $items->aid, ['query' => ['destination' => 'admin/config/animate_any/list']]),
      ];

      $links['delete'] = [
        'title' => $this->t('Delete'),
        'url' => Url::fromUri('internal:/admin/config/animate_any/delete/' . $items->aid, ['query' => ['destination' => 'admin/config/animate_any/list']]),
      ];

      $operation = [
        'data' => [
          '#type' => 'operations',
          '#links' => $links,
        ],
      ];

      $rows[] = [
        $items->aid, $items->parent, $identifiers, $operation,
      ];
    }
    $url = Url::fromUri('internal:/admin/config/animate_any', ['attributes' => ['class' => ['button']]]);
    $add = Link::fromTextAndUrl($this->t('Add Animation'), $url)->toString();
    $add_link = '<ul class="action-links"><li>' . $add . '</li></ul>';
    $help_text = t('Identifiers with common parent element is merged in single row.');

    $empty = '<div role="contentinfo" aria-label="Status message" class="messages messages--warning">No record found.</div>';

    $output['animate_list'] = [
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t($empty),
      '#prefix' => $add_link . '<div class="description">' . $help_text . '</div>',
    ];
    $output['pager'] = [
      '#type' => 'pager'
    ];
    return $output;
  }

}
