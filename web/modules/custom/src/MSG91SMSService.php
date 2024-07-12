<?php

namespace Drupal\msg91;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Logger\LoggerChannelFactory;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;


/**
 * Class MSG91SMSService.
 */
class MSG91SMSService {

  /**
   * Drupal\Core\Config\ConfigFactoryInterface definition.
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

  /**
   * A logger instance.
   *
   * @var Drupal\Core\Logger\LoggerChannelFactory
   */
  protected $logger;

  /**
   * Constructs a MSG91Service.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \GuzzleHttp\ClientInterface $http_client
   *   A Guzzle client object.
   */
  public function __construct(ConfigFactoryInterface $config_factory, ClientInterface $http_client, LoggerChannelFactory $logger) {
    $this->configFactory = $config_factory->get('msg91.settings');
    $this->httpClient = $http_client;
    $this->logger = $logger->get('msg91');
  }

  /**
   * Constructs a MSG91Service.
   *
   * @param $monbile_number
   *   The mobile number of reciever.
   * @param $message
   *   The SMS message content.
   * @param $sender_id
   *   The Sender ID.
   * @param $route
   *   The SMS preffered route.
   */
  public function msg91_send_message($mobile_number, $message, $sender_id, $route) {
    if (empty($mobile_number)) {
      $this->logger->error('Please provide a valid mobile number for sending SMS.');
      return FALSE;
    }

    if (empty($sender_id)) {
      $sender_id = $this->configFactory->get('msg91_senderID');
    }

    if (empty($route)) {
      $route = $this->configFactory->get('msg91_route');
    }

    $auth_key = $this->configFactory->get('msg91_authKey');
    if (empty($auth_key)) {
      $this->logger->error('Please provide a valid authentication key for sending SMS.');
      return FALSE;
    }

    $api_url = 'https://api.msg91.com/api/v2/sendsms';
    $request_data = [
      'sender' => $sender_id,
      'route' => $route,
      'country' => '91',
      'sms' => [
        [
          'message' => $message,
          'to' => [
            $mobile_number
          ]
        ]
      ]
    ];
    $headers = ['authkey' => $auth_key, 'Content-Type' => 'application/json'];
    $request = $this->httpClient->post($api_url, ['body' => json_encode($request_data), 'headers' => $headers]);
    $response = json_decode($request->getBody());
    return $response->type;
  }

}
