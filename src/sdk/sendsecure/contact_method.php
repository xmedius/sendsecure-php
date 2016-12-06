<?php

/*********************************************************************************************/
//
// ContactMethod object
//
/*********************************************************************************************/

class ContactMethod {

  public $destination_type = "cell_phone";
  public $destination = null;

  /**
    * @desc constructor
    * @param string $destination, phone number or email
    *        string $destination_type, destination type
    * @return
  */
  public function __construct($destination, $destination_type = "cell_phone") {
    $this->destination = $destination;
    $this->destination_type = $destination_type;
  }

  /**
    * @desc build attachment from file
    * @param string $file_path, file path
    *        string $content_type, content type
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