<?php

namespace Drupal\feeds_crawler\Plugin\feeds_crawler\matcher;

use Drupal\Component\Plugin\DerivativeInspectionInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\feeds_crawler\FeedsCrawlerInterface;

/**
 * Provides an interface for all feeds_crawler plugins.
 */
interface MatcherInterface extends PluginInspectionInterface, DerivativeInspectionInterface {

  /**
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *
   * @return mixed
   */
  public function matchEntity(FeedsCrawlerInterface $feeds_crawler_entity, EntityInterface $target_entity, EntityInterface $entity);

}
