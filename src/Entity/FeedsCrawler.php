<?php

namespace Drupal\feeds_crawler\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\feeds_crawler\FeedsCrawlerInterface;

/**
 * @ContentEntityType(
 *   id = "feeds_crawler",
 *   label = @Translation("Feeds Crawler"),
 *   label_collection = @Translation("Feeds Crawler"),
 *   label_singular = @Translation("feeds_crawler"),
 *   label_plural = @Translation("feeds_crawler items"),
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
 *     "access" = "Drupal\feeds_crawler\Access\FeedsCrawlerEntityAccessControlHandler",
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
 *   base_table = "feeds_crawler",
 *   data_table = "feeds_crawler_field_data",
 *   revision_table = "feeds_crawler_revision",
 *   revision_data_table = "feeds_crawler_field_revision",
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
 *   bundle_entity_type = "feeds_crawler_type",
 *   field_ui_base_route = "entity.feeds_crawler_type.edit_form",
 *   common_reference_target = TRUE,
 *   permission_granularity = "bundle",
 *   admin_permission = "administer feeds_crawler",
 *   links = {
 *     "canonical" = "/admin/structure/feeds/crawler/{feeds_crawler}",
 *     "collection" = "/admin/structure/feeds/crawler",
 *     "delete-form" = "/admin/structure/feeds/crawler/{feeds_crawler}/delete",
 *     "delete-multiple-form" = "/admin/structure/feeds/crawler/delete",
 *     "edit-form" = "/admin/structure/feeds/crawler/{feeds_crawler}/edit",
 *     "add-form" = "/admin/structure/feeds/crawler/add/{feeds_crawler_type}",
 *     "version-history" = "/admin/structure/feeds/crawler/{feeds_crawler}/revisions",
 *     "revision" = "/admin/structure/feeds/crawler/{feeds_crawler}/revisions/{feeds_crawler_revision}/view",
 *     "add-page" = "/admin/structure/feeds/crawler/add",
 *   }
 * )
 */
class FeedsCrawler extends EditorialContentEntityBase implements FeedsCrawlerInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['weight'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Weight'))
      ->setDescription(t('The weight of this feeds_crawler entity. Auto referencing will be performed starting at lower weights'))
      ->setDefaultValue(0)
      ->setDisplayConfigurable('form', TRUE);

    $fields['children'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Children'))
      ->setDescription(t('Set the fields that will be populated with auto references.'))
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED)
      ->setSetting('target_type', 'feeds_crawler')
      ->setDisplayConfigurable('form', TRUE);

    return $fields;
  }

}
