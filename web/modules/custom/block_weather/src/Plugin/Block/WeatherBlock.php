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
    $data = $this->getData();
    return [
      '#markup' => $this->t('The weather in Elche is: ') . $data->current->condition->text . ' and the temperature is ' . $data->current->temp_c . 'ºC',
    ];
  }

  protected function getData() {
    $client = $this->httpClient;
    $config = $this->configFactory->get('block_weather.settings');
    $response = $client->get('https://api.weatherapi.com/v1/current.json?q=' . $config->get('city') . '&lang=es&key=' . $config->get('api_key'));
    return json_decode($response->getBody());
  }
}
