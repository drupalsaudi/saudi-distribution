<?php

namespace Drupal\flexslider;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Flexslider optionset entities.
 */
interface FlexsliderInterface extends ConfigEntityInterface {

  /**
   * Returns the array of flexslider library options.
   *
   * @param bool $strict
   *   Use strict typecasting, as defined by the flexslider library.
   *   This fixes the typecasting of options that we defined
   *   differently in the schema.
   *
   * @return array
   *   The array of options.
   */
  public function getOptions($strict = FALSE);

  /**
   * Returns the value of a flexslider library option.
   *
   * @param string $name
   *   The option name.
   *
   * @return mixed
   *   The option value.
   */
  public function getOption($name);

  /**
   * Sets the flexslider library options array.
   *
   * @param array $options
   *   New/updated array of options.
   */
  public function setOptions(array $options);

}
