<?php namespace SendSecure;

/**
 * Class SecurityProfile represents the settings of a Security Profile.
 */
class SecurityProfile {

  private $created_at = null;
  private $updated_at = null;
  private $id = null;
  private $name = null;
  private $description = null;
  private $allowed_login_attempts = null; //Value object
  private $allow_remember_me = null; //Value object
  private $allow_sms = null; //Value object
  private $allow_voice = null; //Value object
  private $allow_email = null; //Value object
  private $code_time_limit = null; //Value object
  private $code_length = null; //Value object
  private $auto_extend_value = null; //Value object
  private $auto_extend_unit = null; //Value object
  private $two_factor_required = null; //Value object
  private $encrypt_attachments = null; //Value object
  private $encrypt_message = null; //Value object
  private $expiration_value = null; //Value object
  private $expiration_unit = null; //Value object
  private $reply_enabled = null; //Value object
  private $group_replies = null; //Value object
  private $double_encryption = null; //Value object
  private $retention_period_type = null; //Value object
  private $retention_period_value = null; //Value object
  private $retention_period_unit = null; //Value object
  private $allow_manual_delete = null; //Value object
  private $allow_manual_close = null; //Value object
  private $allow_for_secure_links = null; //Value object
  private $use_captcha = null; //Value object
  private $verify_email = null; //Value object
  private $distribute_key = null; //Value object
  private $consent_group_id = null; //Value object

  public function __construct() {}

  public function get_created_at() {
    return $this->created_at;
  }

  public function get_updated_at() {
    return $this->updated_at;
  }

  public function get_description() {
    return $this->description;
  }

  public function get_name() {
    return $this->name;
  }

  public function get_id() {
    return $this->id;
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

  public function get_code_time_limit() {
    return $this->code_time_limit;
  }

  public function get_code_length() {
    return $this->code_length;
  }

  public function get_auto_extend_value() {
    return $this->auto_extend_value;
  }

  public function get_auto_extend_unit() {
    return $this->auto_extend_unit;
  }

  public function get_two_factor_required() {
    return $this->two_factor_required;
  }

  public function get_encrypt_attachments() {
    return $this->encrypt_attachments;
  }

  public function get_encrypt_message() {
    return $this->encrypt_message;
  }

  public function get_expiration_value() {
    return $this->expiration_value;
  }

  public function get_expiration_unit() {
    return $this->expiration_unit;
  }

  public function get_reply_enabled() {
    return $this->reply_enabled;
  }

  public function get_group_replies() {
    return $this->group_replies;
  }

  public function get_double_encryption() {
    return $this->double_encryption;
  }

  public function get_retention_period_type() {
    return $this->retention_period_type;
  }

  public function get_retention_period_value() {
    return $this->retention_period_value;
  }

  public function get_retention_period_unit() {
    return $this->retention_period_unit;
  }

  public function get_allow_manual_delete() {
    return $this->allow_manual_delete;
  }

  public function get_allow_manual_close() {
    return $this->allow_manual_close;
  }

  public function get_allow_for_secure_links() {
    return $this->allow_for_secure_links;
  }

  public function get_use_captcha() {
    return $this->use_captcha;
  }

  public function get_verify_email() {
    return $this->verify_email;
  }

  public function get_distribute_key() {
    return $this->distribute_key;
  }

  public function get_consent_group_id() {
    return $this->consent_group_id;
  }

  /**
   * @desc builds SecurityProfile object from Json
   * @param json $json
   * @return SecurityProfile
   */
  public static function from_json($json) {
    $security_profile = new SecurityProfile();
    foreach ($json as $key => $value) {
      if (in_array($key, ["created_at", "updated_at", "id", "name", "description"])) {
        $security_profile->$key = $value;
      } else {
        if(property_exists($security_profile, $key)) {
          $security_profile->$key = new Value($value->value, $value->modifiable);
        }
      }
    }
    return $security_profile;
  }

}

?>