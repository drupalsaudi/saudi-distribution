<?php

namespace Drupal\simple_block;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Defines a class to build a listing of simple blocks.
 *
 * @see \Drupal\simple_block\Entity\SimpleBlock
 */
class SimpleBlockListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    return [
      'id' => $this->t('ID'),
      'block' => $this->t('Block description'),

    ] + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
      return [
        'id' => $entity->id(),
        'block' => $entity->label(),
      ] + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $entity) {
    $operations = parent::getDefaultOperations($entity);
    if (isset($operations['edit'])) {
      $operations['edit']['query']['destination'] = $entity->toUrl('collection')
        ->toString();
    }
    return $operations;
  }

}
