<?php

namespace Drupal\feeds_crawler\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Plugin annotation object for feeds_crawler matcher plugins.
 *
 * @ingroup feeds_crawler_plugins
 *
 * @Annotation
 */
class FeedsCrawlerAction extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The plugin title.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $title = '';

}
