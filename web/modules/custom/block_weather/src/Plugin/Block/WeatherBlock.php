<?php

namespace Drupal\block_weather\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the Weather Block.
 */

#[Block(
  id: "hello_block",
  admin_label: new TranslatableMarkup("Weather block"),
  category: new TranslatableMarkup("Weather")
)]

class WeatherBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The config factory for retrieving required config settings.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The HTTP client to fetch the feed data with.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    ConfigFactoryInterface $config_factory,
    ClientInterface $http_client
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $config_factory;
    $this->httpClient = $http_client;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('http_client')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'block_weather',
      '#data' => $this->getData(),
    ];
  }

  /**
   * Fetches the data from the API.
   *
   * @return mixed
   */
  protected function getData() {
    $client = $this->httpClient;
    $uri = $this->getUri();
    $response = $client->get($uri);

    return json_decode($response->getBody());
  }

  /**
   * Returns the URI to fetch the data from.
   *
   * @return string
   */
  protected function getUri() {
    $endpoint = 'https://api.weatherapi.com/v1/current.json';
    $config = $this->configFactory->get('block_weather.settings');
    $query = [
      'q' => $config->get('city'),
      'lang' => 'es',
      'key' => $config->get('api_key'),
    ];

    return $endpoint . '?' . http_build_query($query);
  }

}
