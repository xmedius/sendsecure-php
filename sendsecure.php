<?php

include 'config.php';
include 'exception.php';


/*********************************************************************************************/
//
// Client
//
/*********************************************************************************************/

# CLIENT
class Client {

  protected $api_token = null;
  protected $endpoint = null;
  protected $locale = null;
  protected $enterprise_account = null;

  protected $json_client = null;

  # token can be either a user token or a access token
  public function __construct($api_token, $enterprise_account, $endpoint = ENDPOINT, $locale = 'en') {
    $this->api_token = $api_token;
    $this->enterprise_account = $enterprise_account;
    $this->endpoint = $endpoint;
    $this->locale = $locale;
    $this->json_client = new JsonClient($this->api_token, $this->enterprise_account, $this->endpoint, $this->locale);
  }

  # class method used to acquire a user_token to use authenticate the user for a
  # enterprise_account: permalink of the enterprise
  # endpoint: the host of the portal (.com ou .eu), defaults to .co
  public static function get_user_token($enterprise_account, $endpoint = ENDPOINT, $one_time_password = "") {

    // Get portal url
    $query_url = $endpoint . "/services/" . $enterprise_account . "/portal/host";
    $result = Api::get_http_request($query_url, null);

    //Get token
    $application_type = "SendSecure PHP";
    $device_id = "device_id";
    $device_name = "systemtest";

    $payload = array('permalink' => $enterprise_account, 'username' =>  USERNAME, 'password' => PASSWORD, 'application_type' => $application_type, 'device_id' => $device_id, 'device_name' => $device_name, 'otp' => $one_time_password);
    $result = Api::post_http_request($result."api/user_token", json_encode($payload), null);

    $obj = json_decode($result);
    if ($obj->{'result'} === true) {
      $result =  $obj->{'token'};
    } else {
      throw new SendSecureException($obj->{'code'}, $obj->{'message'});
    }

    return $result;
  }


  #SUBMIT_SAFEBOX
  public function submit_safebox($safebox) {
    $this->initialize_safebox($safebox);
    foreach ($safebox->attachments as $attachment) {
      $this->upload_attachment($safebox, $attachment);
    }
    return $this->commit_safebox($safebox);
  }


  #INITIALIZE_SAFEBOX
  public function initialize_safebox($safebox) {
    $json = json_decode($this->json_client->new_safebox($safebox->user_email));
    $safebox->guid = $json->{'guid'};
    $safebox->public_encryption_key = $json->{'public_encryption_key'};
    $safebox->upload_url = $json->{'upload_url'};
  }


  #UPLOAD_ATTACHMENT
  public function upload_attachment($safebox, $attachment) {
    $json = null;
    if ($attachment->stream == null) {
      $json = json_decode($this->json_client->upload_file($safebox->upload_url, $attachment->file_path, $attachment->content_type));
    } else {
      $json = json_decode($this->json_client->upload_file_stream($safebox->upload_url, $attachment->stream, $attachment->content_type, $attachment->file_size));
    }
    $attachment->guid = $json->{'temporary_document'}->{'document_guid'};
    return $attachment;
  }


  #COMMITSAFEBOX
  public function commit_safebox($safebox) {
    $result = json_decode($this->json_client->commit_safebox($safebox->as_json_for_client()));
    return new SafeboxResponse($result->{"guid"},$result->{"preview_url"},$result->{"encryption_key"});
  }


  #DEFAULTSECURITYPROFILE
  public function default_security_profile($user_email) {
    $id = $this->enterprise_settings()->default_security_profile_id;
    foreach ($this->security_profiles($user_email) as $security_profile) {
      if ($security_profile->id == $id) {
        return $security_profile;
        break;
      }
    }
  }


  #SECURITY_PROFILES
  public function security_profiles($user_email) {
    $security_profiles = array();
    $objs = json_decode($this->json_client->get_security_profiles($user_email))->{"security_profiles"};
    foreach ($objs as $obj) {
      array_push($security_profiles, SecurityProfile::from_json($obj));
    }
    return $security_profiles;
  }


  #ENTERPRISE_SETTINGS
  public function enterprise_settings() {
    return EnterpriseSettings::from_json(json_decode($this->json_client->get_enterprise_settings()));
  }


  # UTILS
  public function __toString() {
    return "Token:{$this->api_token}\nEndpoint:{$this->endpoint}\nLocale:{$this->locale}\n";
  }

}


/*********************************************************************************************/
//
// Json Client
//
/*********************************************************************************************/

//JSONCLIENT
class JsonClient {

  protected $api_token = null;
  protected $enterprise_account = null;
  protected $endpoint = null;
  protected $sendsecure_endpoint = null;
  protected $locale = null;

  # token can be either a user token or a access token
  public function __construct($api_token, $enterprise_account, $endpoint = ENDPOINT, $locale = 'en') {
    $this->api_token = $api_token;
    $this->enterprise_account = $enterprise_account;
    $this->endpoint = $endpoint;
    $this->locale = $locale;
  }

  # GET
  public function new_safebox($user_email) {
    $query_url = $this->get_sendsecure_endpoint() . "api/v2/safeboxes/new.json?user_email=".$user_email."&locale=".$this->locale;
    return Api::get_http_request($query_url, $this->api_token);
  }

  # POST
  public function upload_file($upload_url, $file_path, $content_type) {
    return Api::upload_file($upload_url, $file_path, $content_type);
  }

  # POST
  public function upload_file_stream($upload_url, $file_stream, $content_type, $filename, $filesize) {
    return Api::upload_file_stream($upload_url, $file_stream, $content_type, $filename, $filesize);
  }

  # POST
  public function commit_safebox($safebox_json) {
    $query_url = $this->get_sendsecure_endpoint() . "api/v2/safeboxes.json";
    return Api::post_http_request($query_url, $safebox_json, $this->api_token);
  }

  # GET
  public function get_security_profiles($user_email) {
    $query_url = $this->get_sendsecure_endpoint() . "api/v2/enterprises/".$this->enterprise_account."/security_profiles.json?user_email=".$user_email."&locale=".$this->locale;
    return Api::get_http_request($query_url, $this->api_token);
  }

  #GET
  public function get_enterprise_settings() {
    $query_url = $this->get_sendsecure_endpoint() . "api/v2/enterprises/".$this->enterprise_account."/settings.json?locale=".$this->locale;
    return Api::get_http_request($query_url, $this->api_token);
  }

  # PRIVATE

  private function get_sendsecure_endpoint() {
    if ($this->sendsecure_endpoint == null) {
      $query_url = $this->endpoint . "/services/" . $this->enterprise_account . "/sendsecure/server/url";
      $this->sendsecure_endpoint = Api::get_http_request($query_url, $this->api_token);
    }
    return $this->sendsecure_endpoint;
  }

  # UTILS

  public function __toString() {
    return "Token:{$this->api_token}\nEndpoint:{$this->endpoint}\nLocale:{$this->locale}\n";
  }

}


/*********************************************************************************************/
//
// Curl methods
//
/*********************************************************************************************/

#API
class Api {

  public static function get_http_request($query_url, $token) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization-Token: '.$token]);
    curl_setopt($ch, CURLOPT_URL, $query_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, '60');
    $result = trim(curl_exec($ch));

    if (!curl_errno($ch)) {
      switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
        case $http_code < 400:
          break;
        default:
          throw new UnexpectedServerResponseException($http_code);
      }
    }

    curl_close($ch);
    return $result;
  }

  public static function post_http_request($query_url, $payload, $token) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization-Token: '.$token, 'Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_URL, $query_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, '60');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    $result = trim(curl_exec($ch));

    if (!curl_errno($ch)) {
      switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
        case $http_code < 400:
          break;
        default:
          throw new UnexpectedServerResponseException($http_code);
      }
    }

    curl_close($ch);
    return $result;
  }

  public static function upload_file($query_url, $file, $content_type) {
    $ch = curl_init();

    if (function_exists('curl_file_create')) { // php 5.6+
      $c_file = curl_file_create($file);
    } else { //
      $c_file = '@' . realpath($file);
    }

    $post = array('type' => $content_type,'file_contents'=> $c_file);
    curl_setopt($ch, CURLOPT_URL, $query_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, '60');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    $result = curl_exec($ch);

    if (!curl_errno($ch)) {
      switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
        case $http_code < 400:
          break;
        default:
          throw new UnexpectedServerResponseException($http_code);
      }
    }

    curl_close($ch);
    return $result;
  }

  public static function upload_file_stream($query_url, $stream, $content_type, $filename, $filesize) {
    $ch = curl_init();

    global $streamcontent;
    $streamcontent = $stream;

    curl_setopt($ch, CURLOPT_URL, $query_url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, '60');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: '.$content_type.';', 'Content-Disposition: attachment; filename="'.$filename.'"', 'Content-Length: '. $filesize));
    curl_setopt($ch, CURLOPT_READFUNCTION, 'Api::read_stream');
    $result = curl_exec($ch);

    if (!curl_errno($ch)) {
      switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
        case $http_code < 400:
          break;
        default:
          throw new UnexpectedServerResponseException($http_code);
      }
    }

    curl_close($ch);
    return $result;
  }

  private static function read_stream($ch, $fp, $len) {
    static $pos=0; // keep track of position
    // set data
    $data = substr($GLOBALS['streamcontent'], $pos, $len);
    // increment $pos
    $pos += strlen($data);
    // return the data to send in the request
    return $data;
  }

}

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

  public function to_json() {

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

  public function to_json() {

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