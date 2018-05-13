<?php

namespace Drupal\feeds_crawler\Plugin\feeds_crawler\matcher;

use Drupal\Core\Entity\EntityInterface;
use Drupal\feeds_crawler\FeedsCrawlerInterface;

/**
 * Matches entity based on strings on specified fields.
 *
 * @FeedsCrawlerMatcher(
 *   id = "string_match",
 *   title = @Translation("String Match"),
 *   help = @Translation("Matches based on having a matching string on specified fields.")
 * )
 */
class StringMatch extends Base implements MatcherInterface {

  /**
   * {inheritdoc}.
   */
  public function matchEntity(FeedsCrawlerInterface $feeds_crawler_entity, EntityInterface $target_entity, EntityInterface $entity) {

    $paragraph_storage = $this->entityTypeManager->getStorage('paragraph');
    $string_field_types = $this->getStringFieldTypes();

    if (!$feeds_crawler_entity->hasField('field_string')) {
      return;
    }

    $feeds_crawler_string_match = $feeds_crawler_entity->get('field_string')->getValue();

    // Check if field_name is empty or feeds_crawler_empty allows adding to populated entities.
    foreach ($feeds_crawler_entity->field_name_check->getValue() as $target_field_check_data) {

      $target_field_check = $this->fieldStorageConfigStorage->load($target_field_check_data['target_id']);

      if ($target_field_check->getType() == 'entity_reference_revisions') {
        foreach ($entity->get($target_field_check->getName())->getValue() as $paragraph_data) {
          $paragraph = $paragraph_storage->load($paragraph_data['target_id']);
          foreach ($paragraph->getFieldDefinitions() as $paragraph_field) {
            if (in_array($paragraph_field->getType(), $string_field_types)) {
              foreach ($paragraph->get($paragraph_field->getName())->getValue() as $field_values) {

                // Check string matches.
                foreach ($feeds_crawler_string_match as $string_data) {
                  if (strpos($field_values['value'], $string_data['value']) !== FALSE) {
                    return TRUE;
                  }
                }
              }
            }
          }
        }
      }
    }

    return FALSE;
  }

}
