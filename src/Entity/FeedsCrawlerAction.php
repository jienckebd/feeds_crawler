<?php

namespace Drupal\feeds_crawler\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\feeds_crawler\FeedsCrawlerInterface;

/**
 * @ContentEntityType(
 *   id = "feeds_crawler_action",
 *   label = @Translation("Feeds Crawler Action"),
 *   label_collection = @Translation("Feeds Crawler Action"),
 *   label_singular = @Translation("Feeds Crawler Action"),
 *   label_plural = @Translation("Feeds Crawler Action items"),
 *   label_count = @PluralTranslation(
 *     singular = "@count content item",
 *     plural = "@count content items"
 *   ),
 *   bundle_label = @Translation("Feeds Crawler Type"),
 *   handlers = {
 *     "storage" = "Drupal\Core\Entity\Sql\SqlContentEntityStorage",
 *     "storage_schema" = "Drupal\Core\Entity\Sql\SqlContentEntityStorageSchema",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *       "edit" = "Drupal\Core\ContentEntityForm",
 *       "delete-multiple-confirm" = "Drupal\Core\Entity\Form\DeleteMultipleForm"
 *     },
 *     "inline_form" = "Drupal\feeds_crawler\Form\FeedsCrawlerInlineForm",
 *     "list_builder" = "Drupal\Core\Entity\EntityListBuilder",
 *     "translation" = "Drupal\content_translation\ContentTranslationHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "feeds_crawler_action",
 *   data_table = "feeds_crawler_action_field_data",
 *   revision_table = "feeds_crawler_action_revision",
 *   revision_data_table = "feeds_crawler_action_field_revision",
 *   show_revision_ui = TRUE,
 *   translatable = TRUE,
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "bundle" = "type",
 *     "label" = "label",
 *     "langcode" = "langcode",
 *     "uuid" = "uuid",
 *     "status" = "status",
 *     "published" = "status",
 *     "uid" = "uid",
 *   },
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_uid",
 *     "revision_created" = "revision_timestamp",
 *     "revision_log_message" = "revision_log"
 *   },
 *   bundle_entity_type = "feeds_crawler_action_type",
 *   field_ui_base_route = "entity.feeds_crawler_action_type.edit_form",
 *   common_reference_target = TRUE,
 *   permission_granularity = "bundle",
 *   admin_permission = "administer feeds_crawler",
 *   links = {
 *     "canonical" = "/admin/structure/feeds/crawler/action/{feeds_crawler_action}",
 *     "collection" = "/admin/structure/feeds/crawler/action",
 *     "delete-form" = "/admin/structure/feeds/crawler/action/{feeds_crawler_action}/delete",
 *     "delete-multiple-form" = "/admin/structure/feeds/crawler/action/delete",
 *     "edit-form" = "/admin/structure/feeds/crawler/action/{feeds_crawler_action}/edit",
 *     "add-form" = "/admin/structure/feeds/crawler/action/add/{feeds_crawler_type}",
 *     "version-history" = "/admin/structure/feeds/crawler/action/{feeds_crawler_action}/revisions",
 *     "revision" = "/admin/structure/feeds/crawler/action/{feeds_crawler_action}/revisions/{feeds_crawler_revision}/view",
 *     "add-page" = "/admin/structure/feeds/crawler/action/add",
 *   }
 * )
 */
class FeedsCrawlerAction extends EditorialContentEntityBase implements FeedsCrawlerInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);
    return $fields;
  }

}
