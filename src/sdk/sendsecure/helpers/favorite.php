<?php namespace SendSecure;

class Favorite extends BaseHelper
{
    public $first_name = null;
    public $last_name = null;
    public $email = null;
    public $order_number = null;
    public $company_name = null;
    public $contact_methods = array(); //Array of ContactMethod objects

    private $id = null;
    private $created_at = null;
    private $updated_at = null;

    public function __construct($email, $first_name, $last_name, $company_name, $contact_methods = [])
    {
        $this->email = $email;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->company_name = $company_name;
        $this->contact_methods = $contact_methods;
    }

    public function add_contact_method($contact_method)
    {
        if (!($contact_method instanceof ContactMethod)) {
            throw new SendSecureException(0, "Invalid contact method : Should be a ContactMethod object.");
        }
        array_push($this->contact_methods, $contact_method);
    }

    public function get_id()
    {
        return $this->id;
    }

    public function get_created_at()
    {
        return $this->created_at;
    }

    public function get_updated_at()
    {
        return $this->updated_at;
    }

    public function update_attributes($json)
    {
        foreach ($json as $key => $value) {
            if ("contact_methods" == $key) {
                foreach ($json->contact_methods as $contact_method) {
                    if (!$this->update_contact_method($contact_method)) {
                        array_push($this->contact_methods, $contact_method);
                    }
                }
            } elseif (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }

        //reset array keys;
        $this->contact_methods = array_values($this->contact_methods);
    }

    private function update_contact_method($contact_method)
    {
        $delete_flag = false;
        if (property_exists($contact_method, '_destroy') && $contact_method->_destroy) {
            $delete_flag = true;
        }

        foreach ((array) $this->contact_methods as $key => &$old_contact_method) {
            if ($contact_method->get_id() != null && $contact_method->get_id() == $old_contact_method->get_id()) {
                if ($delete_flag) {
                    unset($this->contact_methods[$key]);
                } else {
                    $old_contact_method->update_attributes($contact_method);
                }
                return true;
            }
        }
        return false;
    }

    public function ignored_keys()
    {
        return ["created_at", "updated_at", "contact_methods"];
    }

    public function prepare_to_destroy_contact_methods($contact_method_ids)
    {
        foreach ($this->contact_methods as $contact) {
            if (in_array($contact->get_id(), $contact_method_ids)) {
                $contact->_destroy = true;
            }
        }
    }

    public function to_json()
    {
        $all_contact_methods = array();
        foreach ($this->contact_methods as $contact_method) {
            array_push($all_contact_methods, $contact_method->to_json());
        }
        $properties = parent::to_json();
        $properties->contact_methods = $all_contact_methods;
        $favorite = new \stdClass;
        $favorite->favorite = $properties;
        return $favorite;
    }
    public static function from_json($json)
    {
        $favorite = new Favorite(null, null, null, null);
        foreach ($json as $key => $value) {
            if ($key != "contact_methods" && property_exists($favorite, $key)) {
                $favorite->$key = $value;
            }
        }
        foreach ($json->contact_methods as $contact) {
            array_push($favorite->contact_methods, ContactMethod::from_json($contact));
        }

        return $favorite;
    }
}
