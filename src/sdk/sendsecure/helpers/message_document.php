<?php namespace SendSecure;

/**
 * Class MessageDocument builds an object to retrieve all information of a specific document.
 */
class MessageDocument {

  private $id = null;
  private $name = null;
  private $sha = null;
  private $size = null;
  private $url = null;

  public function __construct() {}

  public function get_id() {
    return $this->id;
  }

  public function get_name() {
    return $this->name;
  }

  public function get_sha() {
    return $this->sha;
  }

  public function get_size() {
    return $this->size;
  }

  public function get_url() {
    return $this->url;
  }

  public static function from_json($json) {
    $document = new MessageDocument();
    foreach ($json as $key => $value) {
      if(property_exists($document, $key)) {
        $document->$key = $value;
      }
    }

    return $document;
  }

}

?>