<?php

namespace Drupal\feeds_crawler\Plugin\feeds_crawler\matcher;

use Drupal\Core\Entity\EntityInterface;
use Drupal\feeds_crawler\FeedsCrawlerInterface;

/**
 * Matches entity based on common entities with criteria.
 *
 * @FeedsCrawlerMatcher(
 *   id = "common_entity",
 *   title = @Translation("Common Entity"),
 *   help = @Translation("Matches based on having a common entity on a designated field.")
 * )
 */
class CommonEntity extends Base implements MatcherInterface {

  /**
   * {inheritdoc}.
   */
  public function matchEntity(FeedsCrawlerInterface $feeds_crawler_entity, EntityInterface $target_entity, EntityInterface $entity) {
    foreach ($feeds_crawler_entity->field_name_check->getValue() as $target_field_check_data) {
      $field_check = $this->fieldStorageConfigStorage->load($target_field_check_data['target_id']);
      $field_check_name = $field_check->getName();

      // Verify that the entity has the field we're checking.
      if (!$entity->hasField($field_check_name) || !$target_entity->hasField($field_check_name)) {
        continue;
      }

      if (array_intersect($this->getEntityFieldValues($entity, $field_check_name), $this->getEntityFieldValues($target_entity, $field_check_name))) {
        return TRUE;
      }
    }
    return FALSE;
  }

}
