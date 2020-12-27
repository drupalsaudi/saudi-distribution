<?php

namespace Drupal\ds\Plugin\Derivative;

use Drupal\ds\Form\TwigFieldForm;

/**
 * Retrieves dynamic twig field plugin definitions.
 */
class DynamicTwigField extends DynamicField {

  /**
   * {@inheritdoc}
   */
  protected function getType() {
    return TwigFieldForm::TYPE;
  }

}
