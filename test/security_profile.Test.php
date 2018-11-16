<?php

use PHPUnit\Framework\TestCase;

class SecurityProfileTest extends TestCase {

  private $security_profile;

  protected function setUp() {
    $json = '{
      "id": 7,
      "name": "Default",
      "description": null,
      "created_at": "2016-08-29T14:52:26.085Z",
      "updated_at": "2016-08-29T14:52:26.085Z",
      "allowed_login_attempts": {
        "value": 3,
        "modifiable": false
      },
      "allow_remember_me": {
        "value": true,
        "modifiable": false
      },
      "allow_sms": {
        "value": true,
        "modifiable": false
      },
      "allow_voice": {
        "value": true,
        "modifiable": false
      },
      "allow_email": {
        "value": true,
        "modifiable": false
      },
      "code_time_limit": {
        "value": "5",
        "modifiable": false
      },
      "code_length":{
        "value": 4,
        "modifiable": false
      },
      "auto_extend_value":{
        "value": 3,
        "modifiable": false
      },
       "auto_extend_unit":{
        "value": "days",
        "modifiable": false
      },
      "two_factor_required":{
        "value": true,
        "modifiable": false
      },
      "encrypt_attachments":{
        "value": true,
        "modifiable": false
      },
      "encrypt_message":{
        "value": true,
        "modifiable": false
      },
      "expiration_value":{
        "value": 1,
        "modifiable": false
      },
      "expiration_unit":{
        "value": "months",
        "modifiable": false
      },
      "reply_enabled":{
        "value": true,
        "modifiable": false
      },
      "group_replies":{
        "value": true,
        "modifiable": false
      },
      "double_encryption":{
        "value": true,
        "modifiable": true
      },
      "retention_period_type":{
        "value": "do_not_discard",
        "modifiable": false
      },
      "retention_period_value":{
        "value": null,
        "modifiable": false
      },
      "retention_period_unit":{
        "value": null,
        "modifiable": false
      }
    }';
    $this->security_profile = \SendSecure\SecurityProfile::from_json(json_decode($json));
  }

  public function test_security_profile_from_json() {
    $this->assertEquals($this->security_profile->get_id(), 7);
    $this->assertEquals($this->security_profile->get_name(), "Default");
    $this->assertNull($this->security_profile->get_description());
    $this->assertEquals($this->security_profile->get_created_at(), "2016-08-29T14:52:26.085Z");
    $this->assertEquals($this->security_profile->get_updated_at(), "2016-08-29T14:52:26.085Z");
    $this->assertInstanceOf(\SendSecure\Value::class, $this->security_profile->get_allowed_login_attempts());
    $this->assertEquals($this->security_profile->get_allowed_login_attempts()->value, 3);
    $this->assertFalse($this->security_profile->get_allowed_login_attempts()->modifiable);
    $this->assertInstanceOf(\SendSecure\Value::class, $this->security_profile->get_allow_remember_me());
    $this->assertInstanceOf(\SendSecure\Value::class, $this->security_profile->get_allow_sms());
    $this->assertInstanceOf(\SendSecure\Value::class, $this->security_profile->get_allow_voice());
    $this->assertInstanceOf(\SendSecure\Value::class, $this->security_profile->get_allow_email());
    $this->assertInstanceOf(\SendSecure\Value::class, $this->security_profile->get_code_time_limit());
    $this->assertInstanceOf(\SendSecure\Value::class, $this->security_profile->get_code_length());
    $this->assertInstanceOf(\SendSecure\Value::class, $this->security_profile->get_auto_extend_value());
    $this->assertInstanceOf(\SendSecure\Value::class, $this->security_profile->get_auto_extend_unit());
    $this->assertInstanceOf(\SendSecure\Value::class, $this->security_profile->get_two_factor_required());
    $this->assertInstanceOf(\SendSecure\Value::class, $this->security_profile->get_encrypt_attachments());
    $this->assertInstanceOf(\SendSecure\Value::class, $this->security_profile->get_encrypt_message());
    $this->assertInstanceOf(\SendSecure\Value::class, $this->security_profile->get_expiration_value());
    $this->assertInstanceOf(\SendSecure\Value::class, $this->security_profile->get_expiration_unit());
    $this->assertInstanceOf(\SendSecure\Value::class, $this->security_profile->get_reply_enabled());
    $this->assertInstanceOf(\SendSecure\Value::class, $this->security_profile->get_group_replies());
    $this->assertInstanceOf(\SendSecure\Value::class, $this->security_profile->get_double_encryption());
    $this->assertInstanceOf(\SendSecure\Value::class, $this->security_profile->get_retention_period_type());
    $this->assertInstanceOf(\SendSecure\Value::class, $this->security_profile->get_retention_period_value());
    $this->assertInstanceOf(\SendSecure\Value::class, $this->security_profile->get_retention_period_unit());
  }

}

?>