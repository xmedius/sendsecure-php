<?php namespace SendSecure;

/**
 * Class ContactMethod builds an object to create a phone number destination
*/

class ContactMethod {

  public $destination_type = null;
  public $destination = null;

  /**
    * @desc constructor
    * @param string $destination, phone number or email
    * @param string $destination_type, destination type
  */
  public function __construct($destination, $destination_type = DestinationType::cell) {
    $this->destination = $destination;
    $this->destination_type = $destination_type;
  }

  /**
    * @desc build attachment from file
    * @param string $file_path, file path
    * @param string $content_type, content type
    * @return Attachment
  */
  public function to_json() {
    return array("destination" => $this->destination, "destination_type" => $this->destination_type);
  }

}

//Destination type list
abstract class DestinationType {
  const home = "home_phone";
  const cell = "cell_phone";
  const office = "office_phone";
  const other = "other_phone";
}

?>