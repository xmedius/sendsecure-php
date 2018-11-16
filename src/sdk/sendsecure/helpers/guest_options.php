<?php namespace SendSecure;

/**
 * Class GuestOptions builds an object to create a subset of additional attributes for the Participant (or retrieve participant information).
 */
class GuestOptions extends BaseHelper {

  public $company_name = null;
  public $locked = null;
  public $contact_methods = array(); //Array of ContactMethod

  private $bounced_email = null;
  private $failed_login_attempts = null;
  private $verified = null;
  private $created_at = null;
  private $updated_at = null;

  public function __construct($company_name, $contact_methods = []) {
    $this->company_name = $company_name;
    foreach ($contact_methods as $contact_method) {
      if(!($contact_method instanceof ContactMethod)) {
        throw new SendSecureException(0, "Invalid contact methods : Should be an array of ContactMethod objects");
      }
    }
    $this->contact_methods = $contact_methods;
  }

  public function add_contact_method($contact_method) {
    if(!($contact_method instanceof ContactMethod)) {
      throw new SendSecureException(0, "Invalid contact method : Should be a ContactMethod object.");
    }
    array_push($this->contact_methods, $contact_method);
  }

  public function get_bounced_email() {
    return $this->bounced_email;
  }

  public function get_failed_login_attempts() {
    return $this->failed_login_attempts;
  }

  public function get_verified() {
    return $this->verified;
  }

  public function get_created_at() {
    return $this->created_at;
  }

  public function get_updated_at() {
    return $this->updated_at;
  }

  public function ignored_keys() {
    return ["verified", "created_at", "updated_at",
            "failed_login_attempts", "bounced_email",
            "contact_methods"];
  }

  public function to_json() {
    $all_contact_methods = array();
    foreach ($this->contact_methods as $contact_method) {
      array_push($all_contact_methods, $contact_method->to_json());
    }
    $properties = parent::to_json();
    $properties->contact_methods = $all_contact_methods;
    return $properties;
  }

  public static function from_json($json) {
    $guest_options = new GuestOptions(null);
    foreach ($json as $key => $value) {
      if($key != "contact_methods" && property_exists($guest_options, $key)) {
        $guest_options->$key = $value;
      }
    }
    foreach ($json->contact_methods as $contact) {
      array_push($guest_options->contact_methods, ContactMethod::from_json($contact));
    }

    return $guest_options;
  }

}

?>
