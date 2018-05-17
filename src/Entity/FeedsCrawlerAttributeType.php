<?php

namespace Drupal\feeds_crawler\Entity;

use Drupal\feeds_crawler\FeedsCrawlerTypeInterface;

/**
 * Defines the FeedsCrawler type configuration entity.
 *
 * @ConfigEntityType(
 *   id = "feeds_crawler_attribute_type",
 *   label = @Translation("Feeds Crawler Attribute Type"),
 *   handlers = {
 *     "access" = "Drupal\Core\Entity\EntityAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\feeds_crawler\Form\FeedsCrawlerTypeForm",
 *       "edit" = "Drupal\feeds_crawler\Form\FeedsCrawlerTypeForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     },
 *     "list_builder" = "Drupal\feeds_crawler\ListBuilder\FeedsCrawlerTypeListBuilder",
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   admin_permission = "administer feeds_crawler",
 *   config_prefix = "type",
 *   bundle_of = "feeds_crawler_attribute",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   links = {
 *     "add-form" = "/admin/structure/feeds/crawler/attribute/type/add",
 *     "edit-form" = "/admin/structure/feeds/crawler/attribute/type/{feeds_crawler_attribute_type}",
 *     "delete-form" = "/admin/structure/feeds/crawler/attribute/type/{feeds_crawler_attribute_type}/delete",
 *     "collection" = "/admin/structure/feeds/crawler/attribute/type",
 *   },
 *   config_export = {
 *     "label",
 *     "id",
 *     "description",
 *     "help",
 *     "new_revision"
 *   }
 * )
 */
class FeedsCrawlerAttributeType extends ConfigEntityBundleBase implements FeedsCrawlerTypeInterface {
}
