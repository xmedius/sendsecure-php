<?php namespace SendSecure;

/**
 * Class MessageDocument builds an object to retrieve a specific message from an existing SafeBox.
 */
class Message {

  private $id = null;
  private $note = null;
  private $note_size = null;
  private $read = null;
  private $author_id = null;
  private $author_type = null;
  private $created_at = null;
  private $documents = array(); # Array of MessageDocument objects

  public function __construct() {}

  public function get_id() {
    return $this->id;
  }

  public function get_note() {
    return $this->note;
  }

  public function get_note_size() {
    return $this->note_size;
  }

  public function get_read() {
    return $this->read;
  }

  public function get_author_id() {
    return $this->author_id;
  }

  public function get_author_type() {
    return $this->author_type;
  }

  public function get_created_at() {
    return $this->created_at;
  }

  public function get_documents() {
    return $this->documents;
  }

  public static function from_json($json) {
    $message = new Message();
    foreach ($json as $key => $value) {
      if($key != "documents" && property_exists($message, $key)) {
        $message->$key = $value;
      }
    }

    foreach ($json->documents as $document) {
      array_push($message->documents, MessageDocument::from_json($document));
    }

    return $message;
  }

}

?>