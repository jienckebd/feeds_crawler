<?php

namespace Drupal\feeds_crawler\Feeds\Parser;

use Drupal\feeds\Component\CsvParser as CsvFileParser;
use Drupal\feeds\Exception\EmptyFeedException;
use Drupal\feeds\FeedInterface;
use Drupal\feeds\Feeds\Item\DynamicItem;
use Drupal\feeds\Plugin\Type\Parser\ParserInterface;
use Drupal\feeds\Plugin\Type\PluginBase;
use Drupal\feeds\Result\FetcherResultInterface;
use Drupal\feeds\Result\ParserResult;
use Drupal\feeds\StateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Goutte\Client;
use Drupal\Core\Http\ClientFactory;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Defines a CSV feed parser.
 *
 * @FeedsParser(
 *   id = "crawler",
 *   title = "Crawler",
 *   description = @Translation("Crawl the document."),
 *   form = {
 *     "configuration" = "Drupal\feeds_crawler\Feeds\Parser\Form\CrawlerParserForm",
 *     "feed" = "Drupal\feeds_crawler\Feeds\Parser\Form\CrawlerParserFeedForm",
 *   },
 * )
 */
class CrawlerParser extends PluginBase implements ParserInterface {

  /**
   * Maximum request time in seconds.
   */
  const HTTP_REQUEST_TIMEOUT = 1800;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  public $entityTypeManager;

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
   * @var string
   */
  public $mimeType;

  /**
   * Constructs a PluginBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, EntityTypeManagerInterface $entity_type_manager = NULL, ClientFactory $client_factory = NULL) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->entityTypeManager = $entity_type_manager ?: \Drupal::entityTypeManager();
    $this->clientFactory = $client_factory ?: \Drupal::service('http_client_factory');

    // Crawler.
    $this->crawler = new Client();
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
      $container->get('http_client_factory')
    );
  }

  /**
   * Set the client.
   */
  public function setClient() {
    $http_client_options = [
      'connect_timeout' => static::HTTP_REQUEST_TIMEOUT,
      'timeout' => static::HTTP_REQUEST_TIMEOUT,
      'read_timeout' => static::HTTP_REQUEST_TIMEOUT,
    ];
    $this->client = $this->clientFactory->fromOptions($http_client_options);
  }

  /**
   * {@inheritdoc}
   */
  public function parse(FeedInterface $feed, FetcherResultInterface $fetcher_result, StateInterface $state) {

    $sources = [];
    foreach ($feed->getType()->getMappingSources() as $key => $info) {
      if (!empty($info['value'])) {
        $sources[$info['value']] = $key;
      }
    }

    $feed_config = $feed->getConfigurationFor($this);
    $file_path = $fetcher_result->getFilePath();

    if (!filesize($file_path)) {
      throw new EmptyFeedException();
    }

    $mime_type = mime_content_type($file_path);
    $local_uri = file_build_uri($file_path);
    $content = file_get_contents($file_path);

    if ($mime_type == 'text/plain') {
      $content = $this->jsonToXml($content);
      $mime_type = 'text/xml';
    }

    $crawler = new Crawler(null, $local_uri);
    $crawler->addContent($content, $mime_type);
    $this->mimeType = $mime_type;

    // Get crawler config entity.
    $crawler_storage = $this->entityTypeManager->getStorage('feeds_crawler');

    $crawler_entity = $crawler_storage->load($feed_config['crawler_entity_id']);

    // $tag_selector = '.facet_concept-tagsraw .jstree-node';
    $tag_selector = $crawler_entity->field_selector->value;
    $tags = $crawler->filter($tag_selector)->each(function (Crawler $node, $i) {
      if ($this->mimeType == "text/xml") {
        return $node->text();
      }
      else {
        return $node->extract(['path']);
      }
    });

    $result = new ParserResult();

    foreach ($tags as $result_data) {
      $item = new DynamicItem();

      foreach ($this->getMappingSources() as $map_item_name => $map_item_data) {
        $item->set($map_item_name, is_array($result_data) ? $result_data[0] : $result_data);
      }
      $result->addItem($item);
    }

    return $result;
  }

  /**
   * Converts a JSON string to an array.
   *
   * @param $json
   * @return string
   */
  public function jsonToXml($json) {
    $a = json_decode($json);
    $d = new \DOMDocument();
    $c = $d->createElement("root");
    $d->appendChild($c);
    $t = function($v) {
      $type = gettype($v);
      switch($type) {
        case 'integer': return 'number';
        case 'double':  return 'number';
        default: return strtolower($type);
      }
    };
    $f = function($f,$c,$a,$s=false) use ($t,$d) {
      $c->setAttribute('type', $t($a));
      if ($t($a) != 'array' && $t($a) != 'object') {
        if ($t($a) == 'boolean') {
          $c->appendChild($d->createTextNode($a?'true':'false'));
        } else {
          $c->appendChild($d->createTextNode($a));
        }
      } else {
        foreach($a as $k=>$v) {
          if ($k == '__type' && $t($a) == 'object') {
            $c->setAttribute('__type', $v);
          } else {
            if ($t($v) == 'object') {
              $ch = $c->appendChild($d->createElementNS(null, $s ? 'item' : $k));
              $f($f, $ch, $v);
            } else if ($t($v) == 'array') {
              $ch = $c->appendChild($d->createElementNS(null, $s ? 'item' : $k));
              $f($f, $ch, $v, true);
            } else {
              $va = $d->createElementNS(null, $s ? 'item' : $k);
              if ($t($v) == 'boolean') {
                $va->appendChild($d->createTextNode($v?'true':'false'));
              } else {
                $va->appendChild($d->createTextNode($v));
              }
              $ch = $c->appendChild($va);
              $ch->setAttribute('type', $t($v));
            }
          }
        }
      }
    };
    $f($f,$c,$a,$t($a)=='array');
    return $d->saveXML($d->documentElement);
  }

  /**
   * {@inheritdoc}
   */
  public function getMappingSources() {
    $mapping['name'] = [
      'label' => $this->t('Tag Name'),
      'description' => $this->t('The tag name.'),
    ];
    return $mapping;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultFeedConfiguration() {
    return [
      'crawler_entity_id' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'crawler_entity_id' => '',
    ];
  }

}
