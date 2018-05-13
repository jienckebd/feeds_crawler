<?php

namespace Drupal\feeds_crawler;

use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Provides an interface defining a feeds_crawler entity.
 */
interface FeedsCrawlerInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface, RevisionLogInterface, EntityPublishedInterface {

  /**
   * Denotes that the feeds_crawler is not published.
   */
  const NOT_PUBLISHED = 0;

  /**
   * Denotes that the feeds_crawler is published.
   */
  const PUBLISHED = 1;

  /**
   * Gets the feeds_crawler type.
   *
   * @return string
   *   The feeds_crawler type.
   */
  public function getType();

  /**
   * Gets the feeds_crawler label.
   *
   * @return string
   *   Label of the feeds_crawler.
   */
  public function getLabel();

  /**
   * Sets the feeds_crawler label.
   *
   * @param string $label
   *   The feeds_crawler label.
   *
   * @return \Drupal\feeds_crawler\FeedsCrawlerInterface
   *   The called feeds_crawler entity.
   */
  public function setLabel($label);

  /**
   * Gets the feeds_crawler creation timestamp.
   *
   * @return int
   *   Creation timestamp of the feeds_crawler.
   */
  public function getCreatedTime();

  /**
   * Sets the feeds_crawler creation timestamp.
   *
   * @param int $timestamp
   *   The feeds_crawler creation timestamp.
   *
   * @return \Drupal\feeds_crawler\FeedsCrawlerInterface
   *   The called feeds_crawler entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the feeds_crawler revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the feeds_crawler revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\feeds_crawler\FeedsCrawlerInterface
   *   The called feeds_crawler entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the feeds_crawler revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   *
   * @deprecated in Drupal 8.2.0, will be removed before Drupal 9.0.0. Use
   *   \Drupal\Core\Entity\RevisionLogInterface::getRevisionUser() instead.
   */
  public function getRevisionAuthor();

  /**
   * Sets the feeds_crawler revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\feeds_crawler\FeedsCrawlerInterface
   *   The called feeds_crawler entity.
   *
   * @deprecated in Drupal 8.2.0, will be removed before Drupal 9.0.0. Use
   *   \Drupal\Core\Entity\RevisionLogInterface::setRevisionUserId() instead.
   */
  public function setRevisionAuthorId($uid);

}
