<?php namespace SendSecure;

/**
 * Class Recipient builds an object to create a recipient to the SafeBox
*/

class Recipient {

  public $email = null;
  public $first_name = null;
  public $last_name = null;
  public $company_name = null;

  public $contact_methods = array(); //ContactMethod objects

  /**
    * @desc constructor
    * @param string $email, recipient email
    * @param string $first_name, recipient first name
    * @param string $last_name, recipient last name
    * @param string $company_name, recipient company name
  */
  public function __construct($email, $first_name, $last_name, $company_name, $contact_methods = []) {
    $this->email = $email;
    $this->first_name = $first_name;
    $this->last_name = $last_name;
    $this->company_name = $company_name;
    $this->contact_methods = $contact_methods;
  }

  /**
    * @desc get json version of this object (JsonClient format)
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