<?php

/*********************************************************************************************/
//
// Object models - Helpers
//
/*********************************************************************************************/

# SAFEBOX
class Safebox {

  public $guid = null;
  public $upload_url = null;
  public $public_encryption_key = null;

  public $user_email = null;
  public $subject = null;
  public $message = null;

  public $recipients = array();
  public $attachments = array();

  public $security_profile = null;
  public $notification_language = null;

  public function __construct($user_email) {
    $this->user_email = $user_email;
  }

  public function as_json_for_client() {

    $all_recipients = array();
    foreach ($this->recipients as $recipient) {
      array_push($all_recipients, $recipient->to_json());
    }

    $all_documents = array();
    foreach ($this->attachments as $attachment) {
      array_push($all_documents, $attachment->guid);
    }
    $all_documents = array();
    $safebox = array(
      'safebox' => array(
      'guid' => $this->guid,
      'recipients' => $all_recipients,
      'subject' => $this->subject,
      'message' => $this->message,
      'security_profile_id' => $this->security_profile->id,
      'document_ids' => $all_documents,
      'reply_enabled' => $this->security_profile->reply_enabled->value,
      'group_replies' => $this->security_profile->group_replies->value,
      'expiration_value' => $this->security_profile->expiration_value->value,
      'expiration_unit' => $this->security_profile->expiration_unit->value,
      'retention_period_type' => $this->security_profile->retention_period_type->value,
      'retention_period_value' => $this->security_profile->retention_period_value->value,
      'retention_period_unit' => $this->security_profile->retention_period_unit->value,
      'encrypt_message' => $this->security_profile->encrypt_message->value,
      'double_encryption' => $this->security_profile->double_encryption->value,
      'public_encryption_key' => $this->public_encryption_key,
      'notification_language' => $this->notification_language));

    echo "--------------------------------------- ***";
    var_dump(json_encode($safebox));
    return json_encode($safebox);

  }

}


# SAFEBOXRESPONSE
class SafeboxResponse {

  public $guid = null;
  public $preview_url = null;
  public $encryption_key = null;

  public function __construct($guid, $preview_url, $encryption_key) {
    $this->guid = $guid;
    $this->preview_url = $preview_url;
    $this->encryption_key = $encryption_key;
  }

}

# ATTACHMENT
class Attachment {

  public $guid = null;
  public $filename = null;
  public $content_type = null;
  public $size = null;
  public $file_path = null;
  public $stream = null;

  public function __construct() {

  }

  public static function from_file_path($file_path, $content_type) {
    $attachment = new Attachment();
    $attachment->file_path = $file_path;
    $attachment->content_type = $content_type;
    return $attachment;
  }

  public static function from_file_stream($stream, $filename, $content_type, $size) {
    $attachment = new Attachment();
    $attachment->stream = $stream;
    $attachment->filename = $filename;
    $attachment->content_type = $content_type;
    $attachment->size = $size;
    return $attachment;
  }

}


# RECIPIENT
class Recipient {

  public $email = null;
  public $first_name = null;
  public $last_name = null;
  public $company_name = null;

  public $contact_methods = array();

  public function __construct($email, $first_name, $last_name, $company_name) {
    $this->email = $email;
    $this->first_name = $first_name;
    $this->last_name = $last_name;
    $this->company_name = $company_name;
  }

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


# CONTACTMETHOD
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


# SECURITYPROFILE
class SecurityProfile {

  protected $created_at = null;
  protected $updated_at = null;
  public $id = null;
  public $name = null;
  public $description = null;
  public $allowed_login_attempts = null;
  public $allow_remember_me = null;
  public $allow_sms = null;
  public $allow_voice = null;
  public $allow_email = null;
  public $code_time_limit = null;
  public $code_length = null;
  public $auto_extend_value = null;
  public $auto_extend_unit = null;
  public $two_factor_required = null;
  public $encrypt_attachments = null;
  public $encrypt_message = null;
  public $expiration_value = null;
  public $expiration_unit = null;
  public $reply_enabled = null;
  public $group_replies = null;
  public $double_encryption = array();
  public $retention_period_type = null;
  public $retention_period_value = null;
  public $retention_period_unit = null;

  public function __construct() {

  }

  public static function from_json($json) {
    $security_profile = new SecurityProfile();
    $security_profile->created_at = $json->created_at;
    $security_profile->updated_at = $json->updated_at;
    $security_profile->id = $json->id;
    $security_profile->name = $json->name;
    $security_profile->description = $json->description;
    $security_profile->allowed_login_attempts = new Value($json->allowed_login_attempts->value, $json->allowed_login_attempts->modifiable);
    $security_profile->allow_remember_me = new Value($json->allow_remember_me->value, $json->allow_remember_me->modifiable);
    $security_profile->allow_sms = new Value($json->allow_sms->value, $json->allow_sms->modifiable);
    $security_profile->allow_voice = new Value($json->allow_voice->value, $json->allow_voice->modifiable);
    $security_profile->allow_email = new Value($json->allow_email->value, $json->allow_email->modifiable);
    $security_profile->code_time_limit = new Value($json->code_time_limit->value, $json->code_time_limit->modifiable);
    $security_profile->code_length = new Value($json->code_length->value, $json->code_length->modifiable);
    $security_profile->auto_extend_value = new Value($json->auto_extend_value->value, $json->auto_extend_value->modifiable);
    $security_profile->auto_extend_unit = new Value($json->auto_extend_unit->value, $json->auto_extend_unit->modifiable);
    $security_profile->two_factor_required = new Value($json->two_factor_required->value, $json->two_factor_required->modifiable);
    $security_profile->encrypt_attachments = new Value($json->encrypt_attachments->value, $json->encrypt_attachments->modifiable);
    $security_profile->encrypt_message = new Value($json->encrypt_message->value, $json->encrypt_message->modifiable);
    $security_profile->expiration_value = new Value($json->expiration_value->value, $json->expiration_value->modifiable);
    $security_profile->expiration_unit = new Value($json->expiration_unit->value, $json->expiration_unit->modifiable);
    $security_profile->reply_enabled = new Value($json->reply_enabled->value, $json->reply_enabled->modifiable);
    $security_profile->group_replies = new Value($json->group_replies->value, $json->group_replies->modifiable);
    $security_profile->double_encryption = new Value($json->double_encryption->value, $json->double_encryption->modifiable);
    $security_profile->retention_period_type = new Value($json->retention_period_type->value, $json->retention_period_type->modifiable);
    $security_profile->retention_period_value = new Value($json->retention_period_value->value, $json->retention_period_value->modifiable);
    $security_profile->retention_period_unit = new Value($json->retention_period_unit->value, $json->retention_period_unit->modifiable);
    return $security_profile;
  }

}


# VALUE
class Value {

  public $value = null;
  public $modifiable = null;

  public function __construct($value, $modifiable) {
    $this->value = $value;
    $this->modifiable = $modifiable;
  }

}

abstract class TimeUnit {
  const hours = "hours";
  const days = "days";
  const weeks = "weeks";
  const months = "months";
}

abstract class LongTimeUnit {
  const hours = "hours";
  const days = "days";
  const weeks = "weeks";
  const months = "months";
  const years = "years";
}


abstract class RetentionPeriodType {
  const discard_at_expiration = "discard_at_expiration";
  const retain_at_expiration = "retain_at_expiration";
  const do_not_discard = "do_not_discard";
}

# ENTERPRISESETTING
class EnterpriseSettings {

  protected $created_at = null;
  protected $updated_at = null;
  public $default_security_profile_id = null;
  public $pdf_language = null;
  public $use_pdfa_audit_records = null;
  public $international_dialing_plan = null;
  public $extension_filter = null;
  public $include_users_in_autocomplete = null;
  public $include_favorites_in_autocomplete = null;

  public function __construct() {

  }

  public static function from_json($json) {
    $enterprise_settings = new EnterpriseSettings();
    $enterprise_settings->default_security_profile_id = $json->default_security_profile_id;
    $enterprise_settings->created_at = $json->created_at;
    $enterprise_settings->updated_at = $json->updated_at;
    $enterprise_settings->pdf_language = $json->pdf_language;
    $enterprise_settings->use_pdfa_audit_records = $json->use_pdfa_audit_records;
    $enterprise_settings->international_dialing_plan = $json->international_dialing_plan;
    $enterprise_settings->extension_filter = new ExtensionFilter($json->extension_filter->mode, $json->extension_filter->list);
    $enterprise_settings->include_users_in_autocomplete = $json->include_users_in_autocomplete;
    $enterprise_settings->include_favorites_in_autocomplete = $json->include_favorites_in_autocomplete;
    return $enterprise_settings;
  }

  public function to_json() {

  }

}


# EXTENSIONFILTER
class ExtensionFilter {

  public $mode = null;
  public $list = array();

  public function __construct($mode, $list) {
    $this->mode = $mode;
    $this->setting = $list;
  }

}

?>