<?php namespace SendSecure;

/**
 * Class Safebox builds an object to create a new SafeBox or get all information of an existing SafeBox.
 */
class Safebox extends BaseHelper
{
    public $guid = null;
    public $public_encryption_key = null;
    public $upload_url = null;
    public $user_email = null;
    public $participants = array();
    public $subject = null;
    public $message = null;
    public $attachments = array();
    public $notification_language = "en";
    public $security_profile_id = null;
    public $email_notification_enabled = null;
    public $expiration = null;

    private $user_id = null;
    private $enterprise_id = null;
    private $status = null;
    private $security_profile_name = null;
    private $unread_count = null;
    private $double_encryption_status = null;
    private $audit_record_pdf = null;
    private $secure_link = null;
    private $secure_link_title = null;
    private $preview_url = null;
    private $encryption_key = null;
    private $created_at = null;
    private $updated_at = null;
    private $assigned_at = null;
    private $latest_activity = null;
    private $closed_at = null;
    private $content_deleted_at = null;
    private $messages = array(); //Array of Message objects
    private $download_activity = null; //DownloadActivity object
    private $event_history = array(); //Array of EventHistory objects
    public $security_options = null; //SecurityOptions object

  /**
   * @desc constructor
   * @param string $user_email, user email
   */
    public function __construct($user_email)
    {
        $this->user_email = $user_email;
    }

    public function get_user_id()
    {
        return $this->user_id;
    }

    public function get_enterprise_id()
    {
        return $this->enterprise_id;
    }

    public function get_status()
    {
        return $this->status;
    }

    public function get_security_profile_name()
    {
        return $this->security_profile_name;
    }

    public function get_unread_count()
    {
        return $this->unread_count;
    }

    public function get_double_encryption_status()
    {
        return $this->double_encryption_status;
    }

    public function get_audit_record_pdf()
    {
        return $this->audit_record_pdf;
    }

    public function get_secure_link()
    {
        return $this->secure_link;
    }

    public function get_secure_link_title()
    {
        return $this->secure_link_title;
    }

    public function get_preview_url()
    {
        return $this->preview_url;
    }

    public function get_encryption_key()
    {
        return $this->encryption_key;
    }

    public function get_created_at()
    {
        return $this->created_at;
    }

    public function get_updated_at()
    {
        return $this->updated_at;
    }

    public function get_assigned_at()
    {
        return $this->assigned_at;
    }

    public function get_latest_activity()
    {
        return $this->latest_activity;
    }

    public function get_closed_at()
    {
        return $this->closed_at;
    }

    public function get_content_deleted_at()
    {
        return $this->content_deleted_at;
    }

    public function get_messages()
    {
        return $this->messages;
    }

    public function get_download_activity()
    {
        return $this->download_activity;
    }

    public function get_event_history()
    {
        return $this->event_history;
    }

    public function ignored_keys()
    {
        return ["participants", "attachments", "expiration", "user_id",
            "enterprise_id", "status", "security_profile_name", "unread_count",
            "double_encryption_status", "audit_record_pdf", "secure_link",
            "secure_link_title", "preview_url", "encryption_key", "created_at",
            "updated_at", "assigned_at", "latest_activity", "closed_at", "messages",
            "content_deleted_at", "security_options", "download_activity", "event_history"];
    }

    /**
     * @desc Serializes a Safebox object for commit
     * @return Json, json structure
     */
    public function to_json()
    {
        $safebox = parent::to_json();

        $all_recipients = array();
        foreach ($this->participants as $participant) {
            array_push($all_recipients, $participant->as_recipient());
        }

        if (isset($this->attachments[0])) {
            $all_documents = array();
            foreach ($this->attachments as $attachment) {
                array_push($all_documents, $attachment->guid);
            }
            $safebox->document_ids = $all_documents;
        }

        if (isset($this->security_options)) {
            $safebox = (object) array_merge((array)$safebox, (array) $this->security_options->to_json());
        }

        $safebox->recipients = $all_recipients;

        return $safebox;
    }

    /**
     * @desc builds a Safebox object from Json
     * @param $json json
     * @return Json, json structure
     */
    public static function from_json($json)
    {
        $safebox = new Safebox(null);
        $properties = ["security_options", "messages", "download_activity", "event_history", "participants"];
        foreach ($json as $key => $value) {
            if (!in_array($key, $properties) && property_exists($safebox, $key)) {
                $safebox->$key = $value;
            }
        }

        if (isset($json->security_options)) {
            $safebox->security_options = SecurityOptions::from_json($json->security_options);
        }


        if (isset($json->download_activity)) {
            $safebox->download_activity = DownloadActivity::from_json($json->download_activity);
        }

        if (isset($json->messages)) {
            foreach ($json->messages as $message) {
                array_push($safebox->messages, Message::from_json($message));
            }
        }

        if (isset($json->participants)) {
            foreach ($json->participants as $participant) {
                array_push($safebox->participants, Participant::from_json($participant));
            }
        }

        if (isset($json->event_history)) {
            foreach ($json->event_history as $event_history) {
                array_push($safebox->event_history, EventHistory::from_json($event_history));
            }
        }

        return $safebox;
    }

    /**
     * @desc Updates the existing Safebox object with JsonClient::commit_safebox($safebox) response
     * @param $json json returned by commit_safebox($safebox)
     * @return The updated Safebox
     */
    public function update_after_commit($json)
    {
        $security_options = array();
        foreach ($json as $key => $value) {
            if (in_array($key, $this->security_options_keys())) {
                $security_options[$key] = $value;
            } else {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        }
        $this->security_options = SecurityOptions::from_json($security_options);
        return $this;
    }

    public function temporary_document($file_size)
    {
        if ($this->public_encryption_key == null) {
            return '{"temporary_document":{"document_file_size":'.$file_size.'},"multipart":false}';
        }
        return '{"temporary_document":{"document_file_size":'.$file_size.
          '},"multipart":false,"public_encryption_key":'
          .$this->public_encryption_key.'}';
    }

    public function security_options_keys()
    {
        return ["reply_enabled", "group_replies", "retention_period_type",
            "retention_period_value", "retention_period_unit", "encrypt_message",
            "double_encryption", "expiration_value", "expiration_unit", "security_code_length",
            "code_time_limit", "allowed_login_attempts", "allow_remember_me", "allow_sms",
            "allow_voice", "allow_email", "two_factor_required", "auto_extend_value", "auto_extend_unit",
            "allow_manual_delete", "allow_manual_close", "encrypt_attachments", "consent_group_id"];
    }
}
