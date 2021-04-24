<?php

namespace Drupal\simple_block\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\filter\Entity\FilterFormat;
use Drupal\simple_block\SimpleBlockInterface;

/**
 * Defines the block config configuration entity.
 *
 * @ConfigEntityType(
 *   id = "simple_block",
 *   label = @Translation("Simple block"),
 *   config_prefix = "simple_block",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "title"
 *   },
 *   handlers = {
 *     "access" = "Drupal\simple_block\SimpleBlockAccessControlHandler",
 *     "list_builder" = "Drupal\simple_block\SimpleBlockListBuilder",
 *     "form" = {
 *       "add" = "Drupal\simple_block\SimpleBlockEditForm",
 *       "edit" = "Drupal\simple_block\SimpleBlockEditForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     }
 *   },
 *   links = {
 *     "collection" = "/admin/structure/block/simple-block",
 *     "canonical" = "/admin/structure/block/simple-block/manage/{simple_block}",
 *     "edit-form" = "/admin/structure/block/simple-block/manage/{simple_block}/edit",
 *     "delete-form" = "/admin/structure/block/simple-block/manage/{simple_block}/delete",
 *   },
 *   config_export = {
 *     "id",
 *     "title",
 *     "content"
 *   }
 * )
 */
class SimpleBlock extends ConfigEntityBase implements SimpleBlockInterface {

  /**
   * The block ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The block title.
   *
   * @var string
   */
  protected $title;

  /**
   * The block content.
   *
   * Have 'value' and 'format' as keys.
   *
   * @var string[]
   */
  protected $content;

  /**
   * {@inheritdoc}
   */
  public function getContent() {
    return $this->content;
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    parent::calculateDependencies();
    if (!empty($format_id = $this->getContent()['format'])) {
      if ($format = FilterFormat::load($format_id)) {
        $this->addDependency($format->getConfigDependencyKey(), $format->getConfigDependencyName());
      }
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    parent::postSave($storage, $update);
    // Invalidate the block cache to update custom block-based derivatives.
    \Drupal::service('plugin.manager.block')->clearCachedDefinitions();
  }

}
