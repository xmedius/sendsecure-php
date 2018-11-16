<?php namespace SendSecure;

/**
 * Class ContactMethod builds an object to create a phone number destination
 */
class ContactMethod extends BaseHelper
{
    public $destination_type = null;
    public $destination = null;
    public $_destroy = null;

    private $verified = null;
    private $created_at = null;
    private $updated_at = null;
    protected $id = null;

    /**
      * @desc constructor
      * @param string $destination, phone number or email
      * @param string $destination_type, destination type
      */
    public function __construct($destination, $destination_type = DestinationType::cell)
    {
        $this->destination = $destination;
        $this->destination_type = $destination_type;
    }

    public function ignored_keys()
    {
        return ["verified", "created_at", "updated_at"];
    }

    public function get_verified()
    {
        return $this->verified;
    }

    public function get_created_at()
    {
        return $this->created_at;
    }

    public function get_updated_at()
    {
        return $this->updated_at;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function update_attributes($json)
    {
        foreach ($json as $prop => $val) {
            if (property_exists($this, $prop)) {
                $this->$prop = $val;
            }
        }
    }

    public static function from_json($json)
    {
        $contact_method = new ContactMethod(null, null);
        foreach ($json as $key => $value) {
            if (property_exists($contact_method, $key)) {
                $contact_method->$key = $value;
            }
        }
        return $contact_method;
    }
}

//Destination type list
abstract class DestinationType
{
    const home = "home_phone";
    const cell = "cell_phone";
    const office = "office_phone";
    const other = "other_phone";
}
