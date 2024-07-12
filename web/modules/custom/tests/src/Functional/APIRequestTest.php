<?php

namespace Drupal\Tests\msg91\Functional;

use Drupal\Core\Url;
use Drupal\Tests\BrowserTestBase;
use GuzzleHttp\Exception\RequestException;

/**
 * API request tests.
 *
 * @group msg91
 */
class APIRequestTest extends BrowserTestBase {
  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['msg91'];

  /**
   * A user with permission to administer site configuration.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $user;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->user = $this->drupalCreateUser(['administer msg91']);
    $this->drupalLogin($this->user);
  }


  /**
   * Test API Connection with MSG91.
   */
  public function testMsg91API() {
    //Configuration of MSG91 Settings Form.
    $this->drupalGet(Url::fromRoute('msg91.settings'));
    $configuration_data = [
      'msg91_authKey' => 'Test Authentication Key',
      'msg91_senderID' => 'TESTID',
      'msg91_route' => 4,
      'msg91_country_code' => 91,
      'msg91_auth_url' => 'https://control.msg91.com/api/validate.php?authkey='
    ];
    $this->drupalPostForm(NULL, $configuration_data, 'Save configuration');

    //Initiate message sending service.
    $send_sms = \Drupal::service('msg91.default');
    try {
      $send_sms->msg91_send_message('1234567890', 'Test SMS Message', 'TESTID', 4);
    }
    catch(RequestException $exception) {
      $message = $exception->getMessage();
      $this->assertStringContainsString('400 Bad Request', $message, 'Invalid authentication key.');
    }
  }

}
