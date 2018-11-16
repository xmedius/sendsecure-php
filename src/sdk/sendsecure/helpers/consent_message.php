<?php namespace SendSecure;

class ConsentMessage {

  private $locale = null;
  private $value = null;
  private $created_at = null;
  private $updated_at = null;

  public function __construct() {}

  public function get_locale() {
    return $this->locale;
  }

  public function get_value() {
    return $this->value;
  }

  public function get_created_at() {
    return $this->created_at;
  }

  public function get_updated_at() {
    return $this->updated_at;
  }

  public static function from_json($json) {
    $consent_message = new ConsentMessage();
    foreach ($json as $key => $value) {
      if(property_exists($consent_message, $key)) {
        $consent_message->$key = $value;
      }
    }
    return $consent_message;
  }

}

?>