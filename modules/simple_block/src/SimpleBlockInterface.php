<?php

namespace Drupal\simple_block;

/**
 * Provides an interface defining a block config entity.
 */
interface SimpleBlockInterface {

  /**
   * Returns the content of the block.
   *
   * @return string[]
   *   The content of the block.
   */
  public function getContent();

}
