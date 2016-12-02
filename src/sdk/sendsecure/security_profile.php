<?php

/*********************************************************************************************/
//
// SecurityProfile
//
/*********************************************************************************************/

class SecurityProfile {

  protected $created_at = null;
  protected $updated_at = null;
  public $id = null;
  public $name = null;
  public $description = null;
  public $allowed_login_attempts = null;
  public $allow_remember_me = null;
  public $allow_sms = null;
  public $allow_voice = null;
  public $allow_email = null;
  public $code_time_limit = null;
  public $code_length = null;
  public $auto_extend_value = null;
  public $auto_extend_unit = null;
  public $two_factor_required = null;
  public $encrypt_attachments = null;
  public $encrypt_message = null;
  public $expiration_value = null;
  public $expiration_unit = null;
  public $reply_enabled = null;
  public $group_replies = null;
  public $double_encryption = array();
  public $retention_period_type = null;
  public $retention_period_value = null;
  public $retention_period_unit = null;

  public function __construct() {

  }

  public static function from_json($json) {
    $security_profile = new SecurityProfile();
    $security_profile->created_at = $json->created_at;
    $security_profile->updated_at = $json->updated_at;
    $security_profile->id = $json->id;
    $security_profile->name = $json->name;
    $security_profile->description = $json->description;
    $security_profile->allowed_login_attempts = new Value($json->allowed_login_attempts->value, $json->allowed_login_attempts->modifiable);
    $security_profile->allow_remember_me = new Value($json->allow_remember_me->value, $json->allow_remember_me->modifiable);
    $security_profile->allow_sms = new Value($json->allow_sms->value, $json->allow_sms->modifiable);
    $security_profile->allow_voice = new Value($json->allow_voice->value, $json->allow_voice->modifiable);
    $security_profile->allow_email = new Value($json->allow_email->value, $json->allow_email->modifiable);
    $security_profile->code_time_limit = new Value($json->code_time_limit->value, $json->code_time_limit->modifiable);
    $security_profile->code_length = new Value($json->code_length->value, $json->code_length->modifiable);
    $security_profile->auto_extend_value = new Value($json->auto_extend_value->value, $json->auto_extend_value->modifiable);
    $security_profile->auto_extend_unit = new Value($json->auto_extend_unit->value, $json->auto_extend_unit->modifiable);
    $security_profile->two_factor_required = new Value($json->two_factor_required->value, $json->two_factor_required->modifiable);
    $security_profile->encrypt_attachments = new Value($json->encrypt_attachments->value, $json->encrypt_attachments->modifiable);
    $security_profile->encrypt_message = new Value($json->encrypt_message->value, $json->encrypt_message->modifiable);
    $security_profile->expiration_value = new Value($json->expiration_value->value, $json->expiration_value->modifiable);
    $security_profile->expiration_unit = new Value($json->expiration_unit->value, $json->expiration_unit->modifiable);
    $security_profile->reply_enabled = new Value($json->reply_enabled->value, $json->reply_enabled->modifiable);
    $security_profile->group_replies = new Value($json->group_replies->value, $json->group_replies->modifiable);
    $security_profile->double_encryption = new Value($json->double_encryption->value, $json->double_encryption->modifiable);
    $security_profile->retention_period_type = new Value($json->retention_period_type->value, $json->retention_period_type->modifiable);
    $security_profile->retention_period_value = new Value($json->retention_period_value->value, $json->retention_period_value->modifiable);
    $security_profile->retention_period_unit = new Value($json->retention_period_unit->value, $json->retention_period_unit->modifiable);
    return $security_profile;
  }

}

?>