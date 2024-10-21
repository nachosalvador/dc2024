<?php

namespace Drupal\block_weather\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure block weather module settings.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * Config settings.
   */
  const string SETTINGS = 'block_weather.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'block_weather_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTINGS);

    $form['api_key'] = [
      '#type' => 'key_select',
      '#title' => $this->t('API Key'),
      '#description' => $this->t('The API key to authenticate with the Weather API.'),
      '#config_target' => 'block_weather.settings:api_key',
    ];

    $form['city'] = [
      '#type' => 'textfield',
      '#title' => $this->t('City'),
      '#config_target' => 'block_weather.settings:city',
    ];

    return parent::buildForm($form, $form_state);
  }

}
