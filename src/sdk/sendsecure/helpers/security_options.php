<?php namespace SendSecure;

/**
 * Class SecurityOptions builds an object to specify the security options of a SafeBox.
 */
class SecurityOptions extends BaseHelper {

  public $reply_enabled = null;
  public $group_replies = null;
  public $retention_period_type = null;
  public $retention_period_value = null;
  public $retention_period_unit = null;
  public $encrypt_message = null;
  public $double_encryption = null;
  public $expiration_value = null;
  public $expiration_unit = null;
  public $expiration_date = null;
  public $expiration_time = null;
  public $expiration_time_zone = null;

  private $security_code_length = null;
  private $code_time_limit = null;
  private $allowed_login_attempts = null;
  private $allow_remember_me = null;
  private $allow_sms = null;
  private $allow_voice = null;
  private $allow_email = null;
  private $two_factor_required = null;
  private $auto_extend_value = null;
  private $auto_extend_unit = null;
  private $allow_manual_delete = null;
  private $allow_manual_close = null;
  private $encrypt_attachments = null;
  private $consent_group_id = null;

  public function __construct() {}

  public function get_security_code_length() {
    return $this->security_code_length;
  }

  public function get_code_time_limit() {
    return $this->code_time_limit;
  }

  public function get_allowed_login_attempts() {
    return $this->allowed_login_attempts;
  }

  public function get_allow_remember_me() {
    return $this->allow_remember_me;
  }

  public function get_allow_sms() {
    return $this->allow_sms;
  }

  public function get_allow_voice() {
    return $this->allow_voice;
  }

  public function get_allow_email() {
    return $this->allow_email;
  }

  public function get_two_factor_required() {
    return $this->two_factor_required;
  }

  public function get_auto_extend_value() {
    return $this->auto_extend_value;
  }

  public function get_auto_extend_unit() {
    return $this->auto_extend_unit;
  }

  public function get_allow_manual_delete() {
    return $this->allow_manual_delete;
  }

  public function get_allow_manual_close() {
    return $this->allow_manual_close;
  }

  public function get_encrypt_attachments() {
    return $this->encrypt_attachments;
  }

  public function get_consent_group_id() {
    return $this->consent_group_id;
  }

  public function ignored_keys() {
    $keys = ["security_code_length", "code_time_limit", "allowed_login_attempts",
            "allow_remember_me", "allow_sms", "allow_voice", "allow_email",
            "two_factor_required", "auto_extend_value", "auto_extend_unit",
            "allow_manual_delete", "allow_manual_close", "encrypt_attachments",
            "consent_group_id"];
    if(isset($expiration_date)) {
      array_push("expiration_value", "expiration_unit");
    }
    return $keys;
  }

  public static function from_json($json) {
    $security_options = new SecurityOptions();
    foreach ($json as $key => $value) {
      if(property_exists($security_options, $key)) {
        $security_options->$key = $value;
      }
    }
    return $security_options;
  }

}

?>