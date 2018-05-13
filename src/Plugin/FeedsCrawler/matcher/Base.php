<?php

namespace Drupal\feeds_crawler\Plugin\feeds_crawler\matcher;

use Drupal\Core\Plugin\PluginBase as ComponentPluginBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\feeds_crawler\FeedsCrawlerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\field\FieldStorageConfigInterface;

/**
 * The base plugin to handle feeds_crawler matching.
 */
abstract class Base extends ComponentPluginBase implements MatcherInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\field\FieldConfigStorage
   */
  public $fieldConfigStorage;

  /**
   * @var \Drupal\field\FieldStorageConfigStorage
   */
  public $fieldStorageConfigStorage;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Constructs a new DeleteAction object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   Current user.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager = NULL, AccountInterface $current_user = NULL) {
    $this->entityTypeManager = $entity_type_manager ?: \Drupal::entityTypeManager();
    $this->currentUser = $current_user ?: \Drupal::currentUser();

    $this->fieldConfigStorage = $this->entityTypeManager
      ->getStorage('field_config');

    $this->fieldStorageConfigStorage = $this->entityTypeManager
      ->getStorage('field_storage_config');

    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('current_user')
    );
  }

  /**
   * {inheritdoc}.
   */
  abstract function matchEntity(FeedsCrawlerInterface $feeds_crawler_entity, EntityInterface $target_entity, EntityInterface $entity);

  /**
   * Get a flat array of field values.
   *
   * @param EntityInterface $entity
   * @param string $field_check_name
   * @return array
   */
  public function getEntityFieldValues(EntityInterface $entity, $field_check_name) {
    $main_property = 'target_id';
    $return = [];
    foreach ($entity->get($field_check_name)->getValue() as $field_value) {
      if (!empty($field_value[$main_property])) {
        $return[] = $field_value[$main_property];
      }
    }
    return $return;
  }

  /**
   * Get string field types.
   *
   * @return array
   */
  public function getStringFieldTypes() {
    $string_field_types = [
      'text',
      'text_long',
      'text_with_summary',
    ];
    return $string_field_types;
  }

}
