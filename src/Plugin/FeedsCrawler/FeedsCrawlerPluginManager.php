<?php

namespace Drupal\feeds_crawler\Plugin\feeds_crawler;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Symfony\Component\DependencyInjection\Container;

/**
 * Plugin type manager for all feeds_crawler plugins.
 *
 * @ingroup feeds_crawler_plugins
 */
class FeedsCrawlerPluginManager extends DefaultPluginManager {

  /**
   * Constructs a FeedsCrawlerPluginManager object.
   *
   * @param string $type
   *   The plugin type, for example filter.
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations,
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct($type, \Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    $type_camelized = Container::camelize($type);
    $annotation_class = "Drupal\feeds_crawler\Annotation\FeedsCrawler{$type_camelized}";
    $interface_class = "Drupal\feeds_crawler\Plugin\feeds_crawler\\{$type}\\{$type_camelized}Interface";
    parent::__construct("Plugin/feeds_crawler/$type", $namespaces, $module_handler, $interface_class, $annotation_class);

    $this->defaults += [
      'parent' => 'parent',
      'plugin_type' => $type,
      'register_theme' => TRUE,
    ];

    $this->alterInfo('feeds_crawler_plugins_' . $type);
    $this->setCacheBackend($cache_backend, "feeds_crawler:$type");
  }

}
