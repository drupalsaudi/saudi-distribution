<?php

namespace Drupal\ds\Plugin\DsField;

use Drupal\filter\Render\FilteredMarkup;

/**
 * Defines a generic dynamic twig field.
 *
 * @DsField(
 *   id = "dynamic_twig_field",
 *   deriver = "Drupal\ds\Plugin\Derivative\DynamicTwigField"
 * )
 */
class DynamicTwigField extends TokenBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $output = '';
    $content = $this->content();

    $render = [
      '#type' => 'inline_template',
      '#template' => $content,
      '#context' => [
        $this->getEntityTypeId() => $this->entity(),
        'entity' => $this->entity(),
      ]
    ];

    try {
      $output = \Drupal::service('renderer')->render($render);
    }
    catch (\Exception $e) {
      \Drupal::logger('ds_twig_field')->error('Error rendering @field: @message', ['@field' => $this->getDerivativeId(), '@message' => $e->getMessage()]);
    }

    if ($output) {
      return ['#markup' => FilteredMarkup::create($output)];
    }

    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function content() {
    $definition = $this->getPluginDefinition();
    return $definition['properties']['content'];
  }

}
