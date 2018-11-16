<?php namespace SendSecure;

/**
 * Class DownloadActivityDetail builds an object with all the download activity details for a specific participant of the SafeBox.
 */
class DownloadActivityDetail {
  private $id = null;
  private $documents = array(); //Array of DownloadActivityDocument objects

  public function __construct() {}

  public function get_id() {
    return $this->id;
  }

  public function get_documents() {
    return $this->documents;
  }

  public static function from_json($json) {
    $detail = new DownloadActivityDetail();
    foreach ($json as $key => $value) {
      if($key != "documents" && property_exists($detail, $key)) {
        $detail->$key = $value;
      }
    }

    foreach ($json->documents as $document) {
      array_push($detail->documents, DownloadActivityDocument::from_json($document));
    }

    return $detail;
  }
}

?>