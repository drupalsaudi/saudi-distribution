<?php

namespace Drupal\simple_block;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a block config entity.
 */
interface SimpleBlockInterface extends ConfigEntityInterface {

  /**
   * Returns the content of the block.
   *
   * @return string[]
   *   The content of the block.
   */
  public function getContent();

}
