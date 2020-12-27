<?php

namespace Drupal\ds_extras\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Render\Element;
use Drupal\Core\Render\Markup;

/**
 * Provides the region block plugin.
 *
 * @Block(
 *   id = "ds_region_block",
 *   admin_label = @Translation("Ds region block"),
 *   category = @Translation("Display Suite"),
 *   deriver = "Drupal\ds_extras\Plugin\Derivative\DsRegionBlock"
 * )
 */
class DsRegionBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $id = $this->getDerivativeId();
    $data = drupal_static('ds_block_region');

    if (!empty($data[$id])) {
      $output = '';

      foreach (Element::children($data[$id]) as $key) {
        if (!empty($data[$id][$key])) {
          $output .= \Drupal::service('renderer')->render($data[$id][$key]);
        }
      }

      return ['#markup' => Markup::create($output)];
    }
    else {
      return [];
    }
  }

}
