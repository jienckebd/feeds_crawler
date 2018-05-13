<?php

namespace Drupal\feeds_crawler;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\RevisionableEntityBundleInterface;

/**
 * Provides an interface defining a node type entity.
 */
interface FeedsCrawlerTypeInterface extends ConfigEntityInterface, RevisionableEntityBundleInterface {

  /**
   * Determines whether the node type is locked.
   *
   * @return string|false
   *   The module name that locks the type or FALSE.
   */
  public function isLocked();

  /**
   * Gets whether a new revision should be created by default.
   *
   * @return bool
   *   TRUE if a new revision should be created by default.
   *
   * @deprecated in Drupal 8.3.0 and will be removed before Drupal 9.0.0. Use
   *   Drupal\Core\Entity\RevisionableEntityBundleInterface::shouldCreateNewRevision()
   *   instead.
   */
  public function isNewRevision();

  /**
   * Sets whether a new revision should be created by default.
   *
   * @param bool $new_revision
   *   TRUE if a new revision should be created by default.
   */
  public function setNewRevision($new_revision);

  /**
   * Gets the help information.
   *
   * @return string
   *   The help information of this node type.
   */
  public function getHelp();

  /**
   * Gets the description.
   *
   * @return string
   *   The description of this node type.
   */
  public function getDescription();

}
