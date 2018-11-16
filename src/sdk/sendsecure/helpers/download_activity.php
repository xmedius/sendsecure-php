<?php namespace SendSecure;

/**
 * Class DownloadActivity builds an object with all download activity information of all participants of an existing SafeBox.
 */
class DownloadActivity {

  private $guests = array(); //Array of DownloadActivityDetail objects
  private $owner = null; //DownloadActivityDetail object

  public function __construct() {}

  public function get_guests() {
    return $this->guests;
  }

  public function get_owner() {
    return $this->owner;
  }

  public static function from_json($json) {
    $download_activity = new DownloadActivity();

    foreach ($json->guests as $guest) {
      array_push($download_activity->guests, DownloadActivityDetail::from_json($guest));
    }

    $download_activity->owner = DownloadActivityDetail::from_json($json->owner);

    return $download_activity;
  }

}

?>