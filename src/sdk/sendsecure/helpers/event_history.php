<?php namespace SendSecure;

/**
 * Class EventHistory builds an object with all Event History information of a SafeBox.
 */
class EventHistory {

  private $type = null;
  private $date = null;
  private $metadata = null; //Metadata object
  private $message = null;

  public function __construct() {}

  public function get_type() {
    return $this->type;
  }

  public function get_date() {
    return $this->date;
  }

  public function get_metadata() {
    return $this->metadata;
  }

  public function get_message() {
    return $this->message;
  }

  public static function from_json($json) {
    $event_history = new EventHistory();
    foreach ($json as $key => $value) {
      if($key != "metadata" && property_exists($event_history, $key)) {
        $event_history->$key = $value;
      }
    }
    $event_history->metadata = MetaData::from_json($json->metadata);

    return $event_history;
  }

}

class MetaData {

    private $emails = array();
    private $attachment_count = null;

    public function __construct() {}

    public function get_emails() {
        return $this->emails;
    }

    public function get_attachment_count() {
        return $this->attachment_count;
    }

    public static function from_json($json) {
        $metadata = new Metadata();
        $metadata->emails = $json->emails;
        $metadata->attachment_count = $json->attachment_count;

        return $metadata;
    }
}

?>
