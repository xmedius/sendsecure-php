<?php namespace SendSecure;

/**
 * Class DownloadActivityDocument builds an object with all the download activity informations for a specific document regarding a specific participant of the SafeBox.
 */
class DownloadActivityDocument {

  private $id = null;
  private $downloaded_bytes = null;
  private $download_date = null;

  public function __construct() {}

  public function get_id() {
    return $this->id;
  }

  public function get_downloaded_bytes() {
    return $this->downloaded_bytes;
  }

  public function get_download_date() {
    return $this->download_date;
  }

  public static function from_json($json) {
    $document = new DownloadActivityDocument();
    foreach ($json as $key => $value) {
      if(property_exists($document, $key)) {
        $document->$key = $value;
      }
    }
    return $document;
  }

}

?>