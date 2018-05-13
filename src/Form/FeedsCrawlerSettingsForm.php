<?php

namespace Drupal\feeds_crawler\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Path\AliasManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Path\PathValidatorInterface;
use Drupal\Core\Routing\RequestContext;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Configure site information settings for this site.
 *
 * @internal
 */
class FeedsCrawlerSettingsForm extends ConfigFormBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The request context.
   *
   * @var \Drupal\Core\Routing\RequestContext
   */
  protected $requestContext;

  /**
   * Constructs a SiteInformationForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Path\AliasManagerInterface $alias_manager
   *   The path alias manager.
   * @param \Drupal\Core\Path\PathValidatorInterface $path_validator
   *   The path validator.
   * @param \Drupal\Core\Routing\RequestContext $request_context
   *   The request context.
   */
  public function __construct(ConfigFactoryInterface $config_factory, EntityTypeManagerInterface $entity_type_manager, RequestContext $request_context) {
    parent::__construct($config_factory);
    $this->entityTypeManager = $entity_type_manager;
    $this->requestContext = $request_context;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('entity_type.manager'),
      $container->get('router.request_context')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'feeds_crawler_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['feeds_crawler.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $settings = $this->config('feeds_crawler.settings');

    $form['general'] = [
      '#type' => 'details',
      '#title' => t('General Settings'),
      '#open' => TRUE,
    ];
    $form['general']['status'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Activate'),
      '#default_value' => $settings->get('status'),
      '#description' => $this->t('Activate or deactivate auto referencing.'),
    ];

    $form['entity'] = [
      '#type' => 'details',
      '#title' => t('Entity API'),
      '#open' => TRUE,
    ];

    $options_entity_type = [];
    foreach ($this->entityTypeManager->getDefinitions() as $key => $entity_type) {
      $options_entity_type[$entity_type->id()] = $entity_type->getLabel() . ' (' . $entity_type->id() . ')';
    }

    $form['entity']['entity_type'] = [
      '#type' => 'checkboxes',
      '#title' => t('Entity Types'),
      '#options' => $options_entity_type,
      '#default_value' => $settings->get('entity_type') ?: [],
      '#description' => $this->t('Configure the entity types that will have their references auto set.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('feeds_crawler.settings')
      ->set('status', $form_state->getValue('status'))
      ->set('entity_type', $form_state->getValue('entity_type'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
