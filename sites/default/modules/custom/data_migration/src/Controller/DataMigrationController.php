<?php

namespace Drupal\data_migration\Controller;

use Drupal\node\Entity\Node;
use GuzzleHttp\Exception\RequestException;
use Drupal\Component\Serialization\Json;

/**
 * Updates backlinks from WP API
 */
class DataMigrationController{

  private $apiGetUrl;
  private $apiPostUrl;
  private $apiTaxonomyUrl;
  private $postDefaultValues;
  private $defaultConfig;
  private $uid;

  /**
   * @return mixed
   */

  public function __construct()
  {
    $this->loadDefaultConfig();
  }

  public function migrateData() : bool
  {
    $results = $this->getDataFromApi();
    return $this->upsertBacklinks($results);
  }

  /***
   * Combines default values with the data from the post entry
   * @param array $data post entry
   * @return array of combined default and fetched entry
   */
  private function processData(array $data) : array
  {
    $processedData = $this->postDefaultValues + $this->mapData($data);
    return $processedData;
  }

  /***
   * Upserts the nodes of respective backlinks.
   * If a post is already existing, update it with new values.
   * Otherwise, create new node.
   * @param array $results
   * @return bool
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  private function upsertBacklinks(array $results) : bool
  {
    foreach ($results as $data) {
      $nodeData = $this->processData($data);

      $node = \Drupal::entityTypeManager()->getStorage('node')->loadRevision($data['vid']);

      if (! $node) {
        $node = Node::create($nodeData);
        $node->save();
      }
    }

    return true;
  }

  /***
   * Gets json data from the WP endpoint and decodes for processing
   * @return array
   */
  private function getDataFromApi() : array
  {
    $response = $this->getResponse();
    $data = Json::decode($response);

    return $data;
  }

  /***
   * Loads default config from data_migration.settings.yml
   * and loads default values for the post entries.
   */
  private function loadDefaultConfig() : void
  {
    $this->defaultConfig = \Drupal::config('data_migration.settings');
    $endpoints = $this->getUrls();
    $this->apiGetUrl = $endpoints['posts_url'] ?? "";
    $this->uid = $endpoints['uid'] ?? "";
    $this->postDefaultValues = [
      'uid' => $this->uid,
      'type' => 'backlinks_related_articles',
      'status' => 1,
      'body_format' => 'full_html'
    ];
  }

  /**
   * Gets json response from the API
   * @return string
   */
  private function getResponse() : string
  {
    try {
      $options = array(
        'method' => 'GET',
        'timeout' => 15,
        'headers' => array('Content-Type' => 'application/x-www-form-urlencoded'),
      );

      $response = \Drupal::httpClient()->get($this->apiGetUrl, $options);
      $json = (string) $response->getBody();
      if (! $json) {
        return false;
      }
    }
    catch (RequestException $e) {
      return false;
    }

    return $json;
  }

  /**
   * Maps the fetched data into keys recognizable by drupal db
   * @param $data
   * @return array
   */
  private function mapData($data) : array
  {
    $mappings = $this->defaultConfig->get('mappings');
    $mapping = $mappings['touristsecrets'];

    $mappedData = [];

    foreach ($mapping as $key => $value) {
      $mappedData[$value] = $data[$key];
    }

    return $mappedData;
  }

  /**
   * Gets url from config file data_migration.settings.yml
   * @return array
   */
  private function getUrls(): array {
    return [
      'posts_url' => $this->defaultConfig->get('posts_url'),
      'uid' => $this->defaultConfig->get('default_uid')
    ];
  }
}
