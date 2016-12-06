<?php

/*********************************************************************************************/
//
// Recipient object
//
/*********************************************************************************************/

class Recipient {

  public $email = null;
  public $first_name = null;
  public $last_name = null;
  public $company_name = null;

  public $contact_methods = array(); //ContactMethod objects

  /**
    * @desc constructor
    * @param string $email, recipient email
    *        string $first_name, recipient first name
    *        string $last_name, recipient last name
    *        string $company_name, recipient company name
    * @return
  */
  public function __construct($email, $first_name, $last_name, $company_name) {
    $this->email = $email;
    $this->first_name = $first_name;
    $this->last_name = $last_name;
    $this->company_name = $company_name;
  }

  /**
    * @desc get json version of this object (JsonClient format)
    * @param
    * @return Json, json structure for JsonClient
  */
  public function to_json() {
    $all_contact_methods = array();
    foreach ($this->contact_methods as $contact_method) {
      array_push($all_contact_methods, $contact_method->to_json());
    }

    return array(
      "first_name" => $this->first_name,
      "last_name" => $this->last_name,
      "company_name" => $this->company_name,
      "email" => $this->email,
      "contact_methods" => $all_contact_methods);
  }

}

?>