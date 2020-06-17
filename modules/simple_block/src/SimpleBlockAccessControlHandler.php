<?php

namespace Drupal\simple_block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the simple block entity type.
 *
 * @see \Drupal\simple_block\Entity\SimpleBlock
 */
class SimpleBlockAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    return AccessResult::allowedIf($account->hasPermission('administer blocks'));
  }

}
