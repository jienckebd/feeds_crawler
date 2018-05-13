<?php

namespace Drupal\feeds_crawler;

use Drupal\Component\Plugin\Discovery\CachedDiscoveryInterface;
use Drupal\Component\Plugin\PluginManagerInterface;

/**
 * Provides an interface for entity type managers.
 */
interface FeedsCrawlerManagerInterface {

  /**
   * @param $method
   * @param $url
   * @return mixed
   */
  public function crawl($method, $url);

}
