<?php namespace SendSecure;

class ConsentMessageGroup {

  private $id = null;
  private $name = null;
  private $created_at = null;
  private $updated_at = null;
  private $consent_messages = array(); //Array of ConsentMessage objects.

  public function __construct() {}

  public function get_id() {
    return $this->id;
  }

  public function get_name() {
    return $this->name;
  }

  public function get_created_at() {
    return $this->created_at;
  }

  public function get_updated_at() {
    return $this->updated_at;
  }

  public function get_consent_messages() {
    return $this->consent_messages;
  }

  public static function from_json($json) {
    $consent_message_group = new ConsentMessageGroup();
    foreach ($json as $key => $value) {
      if($key != "consent_messages" && property_exists($consent_message_group, $key)) {
        $consent_message_group->$key = $value;
      }
    }
    foreach ($json->consent_messages as $message) {
      array_push($consent_message_group->consent_messages, ConsentMessage::from_json($message));
    }
    return $consent_message_group;
  }

}

?>