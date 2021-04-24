<?php

namespace Drupal\flexslider\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\flexslider\Entity\Flexslider;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Route controller class for the flexslider module options configuration.
 */
class FlexsliderOptionsetController extends ControllerBase {

  /**
   * Enables a Flexslider object.
   *
   * @param \Drupal\flexslider\Entity\Flexslider $flexslider
   *   The Flexslider object to enable.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   A redirect response to the Flexslider optionset listing page.
   */
  public function enable(Flexslider $flexslider) {
    $flexslider->enable()->save();
    return new RedirectResponse($flexslider->toUrl('collection', ['absolute' => TRUE]));
  }

  /**
   * Disables an Flexslider object.
   *
   * @param \Drupal\flexslider\Entity\Flexslider $flexslider
   *   The Flexslider object to disable.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   A redirect response to the Flexslider optionset listing page.
   */
  public function disable(Flexslider $flexslider) {
    $flexslider->disable()->save();
    return new RedirectResponse($flexslider->toUrl('collection', ['absolute' => TRUE]));
  }

}
