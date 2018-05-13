<?php

namespace Drupal\feeds_crawler\Feeds\Fetcher;

use Drupal\feeds\Plugin\Type\ClearableInterface;
use Drupal\feeds\Plugin\Type\Fetcher\FetcherInterface;
use Drupal\feeds\Feeds\Fetcher\HttpFetcher;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\File\FileSystemInterface;
use GuzzleHttp\ClientInterface;
use Drupal\feeds_crawler\FeedsCrawlerManagerInterface;
use Drupal\feeds\Exception\EmptyFeedException;
use Drupal\feeds\FeedInterface;
use Drupal\feeds\Result\HttpFetcherResult;
use Drupal\feeds\StateInterface;
use Drupal\feeds\Utility\Feed;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\Response;

/**
 * Defines an HTTP fetcher.
 *
 * @FeedsFetcher(
 *   id = "crawler",
 *   title = @Translation("Crawler"),
 *   description = @Translation("Downloads data from a URL using Drupal's HTTP request handler."),
 *   form = {
 *     "configuration" = "Drupal\feeds\Feeds\Fetcher\Form\HttpFetcherForm",
 *     "feed" = "Drupal\feeds_crawler\Feeds\Fetcher\Form\CrawlerFetcherFeedForm",
 *   },
 *   arguments = {"@http_client", "@cache.feeds_download", "@file_system", "@feeds_crawler.manager"}
 * )
 */
class CrawlerFetcher extends HttpFetcher implements ClearableInterface, FetcherInterface {

  public $feedsCrawlerManager;

  /**
   * Constructs an UploadFetcher object.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin id.
   * @param array $plugin_definition
   *   The plugin definition.
   * @param \GuzzleHttp\ClientInterface $client
   *   The Guzzle client.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   *   The cache backend.
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   The Drupal file system helper.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, ClientInterface $client, CacheBackendInterface $cache, FileSystemInterface $file_system, FeedsCrawlerManagerInterface $feeds_crawler_manager) {
    $this->client = $client;
    $this->cache = $cache;
    $this->fileSystem = $file_system;
    parent::__construct($configuration, $plugin_id, $plugin_definition, $client, $cache, $file_system);

    $this->feedsCrawlerManager = $feeds_crawler_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function fetch(FeedInterface $feed, StateInterface $state) {
    $sink = $this->fileSystem->tempnam('temporary://', 'feeds_http_fetcher');
    $sink = $this->fileSystem->realpath($sink);

    $response = $this->get($feed->getSource(), $sink, $this->getCacheKey($feed));
    // @todo Handle redirects.
    // $feed->setSource($response->getEffectiveUrl());

    // 304, nothing to see here.
    if ($response->getStatusCode() == Response::HTTP_NOT_MODIFIED) {
      $state->setMessage($this->t('The feed has not been updated.'));
      throw new EmptyFeedException();
    }

    return new HttpFetcherResult($sink, $response->getHeaders());
  }

  /**
   * Performs a GET request.
   *
   * @param string $url
   *   The URL to GET.
   * @param string $cache_key
   *   (optional) The cache key to find cached headers. Defaults to false.
   *
   * @return \Guzzle\Http\Message\Response
   *   A Guzzle response.
   *
   * @throws \RuntimeException
   *   Thrown if the GET request failed.
   */
  protected function get($url, $sink, $cache_key = FALSE) {
    $url = Feed::translateSchemes($url);

    $options = [RequestOptions::SINK => $sink];

    // Add cached headers if requested.
    if ($cache_key && ($cache = $this->cache->get($cache_key))) {
      if (isset($cache->data['etag'])) {
        $options[RequestOptions::HEADERS]['If-None-Match'] = $cache->data['etag'];
      }
      if (isset($cache->data['last-modified'])) {
        $options[RequestOptions::HEADERS]['If-Modified-Since'] = $cache->data['last-modified'];
      }
    }

    try {
      // $response = $this->feedsCrawlerManager->crawl('get', $url, $options);
      $response = $this->client->get($url, $options);
    }
    catch (RequestException $e) {
      $args = ['%site' => $url, '%error' => $e->getMessage()];
      throw new \RuntimeException($this->t('The feed from %site seems to be broken because of error "%error".', $args));
    }

    if ($cache_key) {
      // $this->cache->set($cache_key, array_change_key_case($response->getHeaders()));
    }

    return $response;
  }

}
