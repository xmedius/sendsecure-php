<?php namespace SendSecure;

/**
 * Class Participant builds an object to create a participant for the SafeBox (or retrieve participant information).
 */
class Participant extends BaseHelper
{
    public $email = null;
    public $first_name = null;
    public $last_name = null;
    public $privileged = null;
    public $guest_options = null; //GuestOptions object

    protected $id = null;
    private $type = null;
    private $role = null;
    private $message_read_count = null;
    private $message_total_count = null;

    public function __construct($email, $first_name, $last_name, $guest_options = null)
    {
        $this->email = $email;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        if (isset($guest_options) && !($guest_options instanceof GuestOptions)) {
            throw new SendSecureException(0, "Invalid guest options : Should be a GuestOptions object.");
        }
        $this->guest_options = $guest_options;
    }

    public function ignored_keys()
    {
        return ["id", "type", "role", "guest_options",
            "message_read_count", "message_total_count"];
    }

    public function get_id()
    {
        return $this->id;
    }

    public function get_type()
    {
        return $this->type;
    }

    public function get_role()
    {
        return $this->role;
    }

    public function get_message_read_count()
    {
        return $this->message_read_count;
    }

    public function get_message_total_count()
    {
        return $this->message_total_count;
    }

    public function prepare_to_destroy_contact_methods($contact_method_ids)
    {
        foreach ($this->guest_options->contact_methods as $contact) {
            if (in_array($contact->get_id(), $contact_method_ids)) {
                $contact->_destroy = true;
            }
        }
    }

    public function as_recipient()
    {
        $attributes = parent::to_json();
        if (isset($this->guest_options)) {
            $attributes->contact_methods = array();
            foreach ($this->guest_options->contact_methods as $contact_method) {
                array_push($attributes->contact_methods, $contact_method->to_json());
            }
        }
        return $attributes;
    }

    public function to_json()
    {
        $participant = parent::to_json();
        $participant->id = $this->id;
        $guest_options = $this->guest_options->to_json();
        return $participant = (object) array_merge((array)$participant, (array)$guest_options);
    }

    public static function from_json($json)
    {
        $participant = new Participant(null, null, null);
        foreach ($json as $key => $value) {
            if ($key != "guest_options" && property_exists($participant, $key)) {
                $participant->$key = $value;
            }
        }

        if (property_exists($json, "guest_options")) {
            $participant->guest_options = GuestOptions::from_json($json->guest_options);
        }

        return $participant;
    }
}
