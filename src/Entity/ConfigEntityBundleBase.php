<?php

namespace Drupal\feeds_crawler\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase as Base;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\feeds_crawler\FeedsCrawlerTypeInterface;

/**
 * Class ConfigEntityBundleBase
 * @package Drupal\feeds_crawler\Entity
 */
abstract class ConfigEntityBundleBase extends Base implements FeedsCrawlerTypeInterface {

  /**
   * The machine name of this feeds_crawler type.
   *
   * @var string
   */
  protected $id;

  /**
   * The human-readable name of the feeds_crawler type.
   *
   * @var string
   */
  protected $label;

  /**
   * A brief description of this feeds_crawler type.
   *
   * @var string
   */
  protected $description;

  /**
   * Help information shown to the user when creating a FeedsCrawler of this type.
   *
   * @var string
   */
  protected $help;

  /**
   * Default value of the 'Create new revision' checkbox of this feeds_crawler type.
   *
   * @var bool
   */
  protected $new_revision = TRUE;

  /**
   * Display setting for author and date Submitted by post information.
   *
   * @var bool
   */
  protected $display_submitted = TRUE;

  /**
   * {@inheritdoc}
   */
  public function id() {
    return $this->id;
  }

  /**
   * {@inheritdoc}
   */
  public function isLocked() {
    $locked = \Drupal::state()->get('feeds_crawler.type.locked');
    return isset($locked[$this->id()]) ? $locked[$this->id()] : FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function isNewRevision() {
    return $this->new_revision;
  }

  /**
   * {@inheritdoc}
   */
  public function setNewRevision($new_revision) {
    $this->new_revision = $new_revision;
  }

  /**
   * {@inheritdoc}
   */
  public function getHelp() {
    return $this->help;
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    parent::postSave($storage, $update);
  }

  /**
   * {@inheritdoc}
   */
  public static function postDelete(EntityStorageInterface $storage, array $entities) {
    parent::postDelete($storage, $entities);

    // Clear the feeds_crawler type cache to reflect the removal.
    $storage->resetCache(array_keys($entities));
  }

  /**
   * {@inheritdoc}
   */
  public function shouldCreateNewRevision() {
    return $this->isNewRevision();
  }

}
