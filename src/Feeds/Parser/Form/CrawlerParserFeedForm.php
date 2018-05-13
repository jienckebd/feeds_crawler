<?php

namespace Drupal\feeds_crawler\Feeds\Parser\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\feeds\FeedInterface;
use Drupal\feeds\Plugin\Type\ExternalPluginFormBase;

/**
 * Provides a form on the feed edit page for the CsvParser.
 */
class CrawlerParserFeedForm extends ExternalPluginFormBase {

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state, FeedInterface $feed = NULL) {
    $feed_config = $feed->getConfigurationFor($this->plugin);

    $feeds_crawler_storage = \Drupal::entityTypeManager()
      ->getStorage('feeds_crawler');

    if (!empty($feed_config['crawler_entity_id'])) {
      $entity = $feeds_crawler_storage->load($feed_config['crawler_entity_id']);
    }
    else {
      $entity = $feeds_crawler_storage->create([
        'type' => 'default',
      ]);
    }

    $form['crawler_entity_id'] = [
      '#type' => 'inline_entity_form',
      '#op' => 'add',
      '#entity_type' => 'feeds_crawler',
      '#bundle' => 'default',
      '#form_mode' => 'default',
      '#default_value' => $entity,
      '#save_entity' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state, FeedInterface $feed = NULL) {

    /** @var \Drupal\feeds_crawler\FeedsCrawlerInterface $crawler */
    $crawler = $form['crawler_entity_id']['#entity'];

    $values = $form_state->getValues();
    $values['crawler_entity_id'] = $crawler->id();
    $form_state->setValues($values);

    $feed_config = $form_state->getValues();

    $this->plugin->setConfiguration($feed_config);

    $feed->setConfigurationFor($this->plugin, $feed_config);
  }

}
