<?php

namespace Drupal\simple_block\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Retrieves block plugin definitions for all custom config blocks.
 */
class SimpleBlock extends DeriverBase implements ContainerDeriverInterface {

  /**
   * The custom config block storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $blockConfigStorage;

  /**
   * Constructs a BlockContent object.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $block_content_storage
   *   The custom block storage.
   */
  public function __construct(EntityStorageInterface $block_content_storage) {
    $this->blockConfigStorage = $block_content_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    $entity_type_manager = $container->get('entity_type.manager');
    return new static(
      $entity_type_manager->getStorage('simple_block')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    /** @var $simple_block \Drupal\simple_block\Entity\SimpleBlock */
    foreach ($this->blockConfigStorage->loadMultiple() as $simple_block) {
      $this->derivatives[$simple_block->id()] = $base_plugin_definition;
      $this->derivatives[$simple_block->id()]['admin_label'] = $simple_block->label();
      $this->derivatives[$simple_block->id()]['config_dependencies'][$simple_block->getConfigDependencyKey()] = [
        $simple_block->getConfigDependencyName()
      ];
    }
    return parent::getDerivativeDefinitions($base_plugin_definition);
  }

}
