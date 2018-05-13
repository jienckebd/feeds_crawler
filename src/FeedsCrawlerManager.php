<?php

namespace Drupal\feeds_crawler;

use Drupal\cn_core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Cache\CacheBackendInterface;
use Psr\Log\LoggerInterface;
use Drupal\Core\Http\ClientFactory;
use Goutte\Client as Crawler;

/**
 * Class FeedsCrawlerManager
 * @package Drupal\feeds_crawler
 */
class FeedsCrawlerManager implements FeedsCrawlerManagerInterface {

  /**
   * Maximum request time in seconds.
   */
  const HTTP_REQUEST_TIMEOUT = 1800;

  /**
   * @var \Drupal\Core\Http\ClientFactory;
   */
  public $clientFactory;

  /**
   * @var \GuzzleHttp\Client
   */
  public $client;

  /**
   * @var \Goutte\Client
   */
  public $crawler;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   * */
  public $entityTypeManager;

  /**
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  public $entityFieldManager;

  /**
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  public $configFactory;

  /**
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  public $cache;

  /**
   * The current user injected into the service.
   *
   * @var AccountInterface
   */
  public $currentUser;

  /**
   * The JSON API logger channel.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * EntityQueueManager constructor.
   * @param ClientFactory $client_factory
   * @param EntityTypeManagerInterface $entity_type_manager
   * @param EntityFieldManagerInterface $entity_field_manager
   * @param ConfigFactoryInterface $config_factory
   * @param CacheBackendInterface $cache
   * @param AccountInterface $current_user
   */
  public function __construct(ClientFactory $client_factory, EntityTypeManagerInterface $entity_type_manager, EntityFieldManagerInterface $entity_field_manager, ConfigFactoryInterface $config_factory, CacheBackendInterface $cache, AccountInterface $current_user, LoggerInterface $logger) {
    $this->clientFactory = $client_factory;
    $this->entityTypeManager = $entity_type_manager;
    $this->entityFieldManager = $entity_field_manager;
    $this->configFactory = $config_factory;
    $this->cache = $cache;
    $this->currentUser = $current_user;
    $this->logger = $logger;

    $http_client_options = [
      'connect_timeout' => static::HTTP_REQUEST_TIMEOUT,
      'timeout' => static::HTTP_REQUEST_TIMEOUT,
      'read_timeout' => static::HTTP_REQUEST_TIMEOUT,
    ];
    $this->client = $this->clientFactory->fromOptions($http_client_options);

    $this->crawler = new Crawler();
  }

  /**
   * @param $method
   * @param $url
   * @return \Symfony\Component\DomCrawler\Crawler
   */
  public function crawl($method, $url) {
    try {
      $response = $this->crawler->request($method, $url);
      return $response;
    }
    catch (\Exception $e) {
      $this->logger->alert('HTTP crawler request failed.');
    }
  }

}
