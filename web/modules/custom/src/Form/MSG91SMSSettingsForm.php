<?php

namespace Drupal\msg91\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Presents the module settings form.
 */
class MSG91SMSSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'msg91_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['msg91.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('msg91.settings');

    $form['msg91_authKey'] = [
      '#type' => 'textfield',
      '#title' => $this->t('MSG91 Authentication ID'),
      '#default_value' => $config->get('msg91_authKey'),
      '#required' => TRUE,
    ];

    $form['msg91_senderID'] = [
      '#type' => 'textfield',
      '#title' => $this->t('MSG91 Sender ID'),
      '#default_value' => $config->get('msg91_senderID'),
      '#description' => 'SMS received will have this Sender ID',
      '#required' => TRUE,
    ];

    $form['msg91_route'] = [
      '#type' => 'textfield',
      '#title' => $this->t('MSG91 Route'),
      '#default_value' => $config->get('msg91_route'),
      '#description' => 'Use number from 1-6 route for Transactional,Promotional SMS',
      '#required' => TRUE,
    ];

    $form['msg91_country_code'] = [
      '#type' => 'textfield',
      '#title' => $this->t('MSG91 Country Code'),
      '#default_value' => $config->get('msg91_country_code'),
      '#description' => 'Use a valid country code.',
      '#required' => TRUE,
    ];

    $form['msg91_auth_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('MSG91 Auth URL'),
      '#default_value' => $config->get('msg91_auth_url'),
      '#description' => 'Enter Auth URL',
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);

  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $route = $form_state->getValue('msg91_route');
    $auth_key = $form_state->getValue('msg91_authKey');
    $msg91auth_url = $form_state->getValue('msg91_auth_url');
    $country_code = $form_state->getValue('msg91_country_code');
    $sender_id = $form_state->getValue('msg91_senderID');
    if ($route < 1 || $route > 6) {
      $form_state->setErrorByName('msg91_route', $this->t('Route: Please enter number between 1 and 6.'));
    }
    if ($country_code != 0 && $country_code != 1 && $country_code!= 91) {
      $form_state->setErrorByName('msg91_country_code', $this->t('Please enter a valid country code i.e 0, 1 or 91.'));
    }
    $client = \Drupal::httpClient();
    $request = $client->get($msg91auth_url . $auth_key);
    $response = $request->getBody();
    $validate_response = gettext($response);
    if ($validate_response != 'Valid') {
      $form_state->setErrorByName('msg91_authKey', $this->t('Authentication key you have entered does not validate.'));
    }
    if (strlen($sender_id) < 6) {
      $form_state->setErrorByName('msg91_senderID', $this->t('Please provide a sender ID value greater than 6.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('msg91.settings')
      ->set('msg91_authKey', $form_state->getValue('msg91_authKey'))
      ->set('msg91_senderID', $form_state->getValue('msg91_senderID'))
      ->set('msg91_route', $form_state->getValue('msg91_route'))
      ->set('msg91_country_code', $form_state->getValue('msg91_country_code'))
      ->set('msg91_auth_url', $form_state->getValue('msg91_auth_url'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
