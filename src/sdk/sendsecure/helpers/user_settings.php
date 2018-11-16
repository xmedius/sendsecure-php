<?php namespace SendSecure;

/**
 * Class UserSettings represents all the settings of a user.
 */
class UserSettings {

  private $created_at = null;
  private $updated_at = null;
  private $mask_note = null;
  private $open_first_transaction = null;
  private $mark_as_read = null;
  private $mark_as_read_delay = null;
  private $remember_key = null;
  private $default_filter = null; //[everything, in_progress, closed, content_deleted, unread]
  private $recipient_language = null;
  private $secure_link = null; //PersonnalSecureLink object

  public function __construct() {}

  public function get_created_at() {
    return $this->created_at;
  }

  public function get_updated_at() {
    return $this->updated_at;
  }

  public function get_mask_note() {
    return $this->mask_note;
  }

  public function get_open_first_transaction() {
    return $this->open_first_transaction;
  }

  public function get_mark_as_read() {
    return $this->mark_as_read;
  }

  public function get_mark_as_read_delay() {
    return $this->mark_as_read_delay;
  }

  public function get_remember_key() {
    return $this->remember_key;
  }

  public function get_default_filter() {
    return $this->default_filter;
  }

  public function get_recipient_language() {
    return $this->recipient_language;
  }

  public function get_secure_link() {
    return $this->secure_link;
  }

  public function from_json($json) {
    $user_settings = new UserSettings();
    foreach ($json as $key => $value) {
      if ('secure_link' == $key) {
        $user_settings->secure_link = new PersonnalSecureLink($value->enabled, $value->url, $value->security_profile_id);
      } else {
        if(property_exists($user_settings, $key)) {
          $user_settings->$key = $value;
        }
      }
    }
    return $user_settings;
  }

}

?>