<?php

use PHPUnit\Framework\TestCase;

class UserSettingsTest extends TestCase {

  private $user_settings;

  protected function setUp() {
    $json = '{"created_at": "2016-08-15T21:56:45.798Z",
              "updated_at": "2017-04-10T18:58:59.356Z",
              "mask_note": false,
              "open_first_transaction": false,
              "mark_as_read": true,
              "mark_as_read_delay": 5,
              "remember_key": true,
              "default_filter": "everything",
              "recipient_language": null,
              "secure_link": {
                "enabled": true,
                "url": "https://sendsecure.integration.xmedius.com/r/612328d944b842c68418375ffdc87b3f",
                "security_profile_id": 13
              }
            }';
    $this->user_settings = \SendSecure\UserSettings::from_json(json_decode($json));
  }

  public function test_user_settings_from_json() {
    $this->assertEquals($this->user_settings->get_created_at(), "2016-08-15T21:56:45.798Z");
    $this->assertEquals($this->user_settings->get_updated_at(), "2017-04-10T18:58:59.356Z");
    $this->assertFalse($this->user_settings->get_mask_note());
    $this->assertFalse($this->user_settings->get_open_first_transaction());
    $this->assertTrue($this->user_settings->get_mark_as_read());
    $this->assertEquals($this->user_settings->get_mark_as_read_delay(), 5);
    $this->assertTrue($this->user_settings->get_remember_key());
    $this->assertEquals($this->user_settings->get_default_filter(), "everything");
    $this->assertNull($this->user_settings->get_recipient_language());

    $secure_link = $this->user_settings->get_secure_link();
    $this->assertInstanceOf(\SendSecure\PersonnalSecureLink::class, $secure_link);
    $this->assertTrue($secure_link->get_enabled());
    $this->assertEquals($secure_link->get_url(), "https://sendsecure.integration.xmedius.com/r/612328d944b842c68418375ffdc87b3f");
    $this->assertEquals($secure_link->get_security_profile_id(), 13);
  }

}

?>