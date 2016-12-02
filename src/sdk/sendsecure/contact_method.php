<?php

/*********************************************************************************************/
//
// ContactMethod
//
/*********************************************************************************************/

class ContactMethod {

  public $destination_type = "cell_phone";
  public $destination = null;

  public function __construct($destination, $destination_type = "cell_phone") {
    $this->destination = $destination;
    $this->destination_type = $destination_type;
  }

  public function to_json() {
    return array("destination" => $this->destination, "destination_type" => $this->destination_type);
  }

}

abstract class DestinationType {
  const home = "home_phone";
  const cell = "cell_phone";
  const office = "office_phone";
  const other = "other_phone";
}

?>