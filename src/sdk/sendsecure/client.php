<?php

include 'config.php';
include 'exception.php';
include 'request.php';
include 'json_client.php';
include 'attachment.php';
include 'contact_method.php';
include 'enterprise_settings.php';
include 'extension_filter.php';
include 'recipient.php';
include 'safebox.php';
include 'safebox_response.php';
include 'security_profile.php';
include 'value.php';

/*********************************************************************************************/
//
// Client object
//
/*********************************************************************************************/

class Client {

  protected $api_token = null;
  protected $endpoint = null;
  protected $locale = null;
  protected $enterprise_account = null;

  protected $json_client = null; //JsonClient

  /**
    * @desc constructor
    * @param string $api_token, api token
    *        string $enterprise_account, enterprise account
    *        string $endpoint, endpoint url
    *        string $locale, language
    * @return
  */
  public function __construct($api_token, $enterprise_account, $endpoint = ENDPOINT, $locale = 'en') {
    $this->api_token = $api_token;
    $this->enterprise_account = $enterprise_account;
    $this->endpoint = $endpoint;
    $this->locale = $locale;
    $this->json_client = new JsonClient($this->api_token, $this->enterprise_account, $this->endpoint, $this->locale);
  }

  /**
    * @desc constructor
    * @param string $enterprise_account, enterprise account
    *        string $device_id, device id
    *        string $device_name, device name
    *        string $application_type, application type
    *        string $endpoint, endpoint url
    *        string $one_time_password, one time password
    * @return
  */
  public static function get_user_token($enterprise_account, $device_id, $device_name, $application_type = "SendSecure PHP", $endpoint = ENDPOINT, $one_time_password = "") {

    // Get portal url
    $query_url = $endpoint . "/services/" . $enterprise_account . "/portal/host";
    $result = Request::get_http_request($query_url, null);

    $payload = array('permalink' => $enterprise_account, 'username' =>  USERNAME, 'password' => PASSWORD, 'application_type' => $application_type, 'device_id' => $device_id, 'device_name' => $device_name, 'otp' => $one_time_password);
    $result = Request::post_http_request($result."api/user_token", json_encode($payload), null);

    $obj = json_decode($result);
    if ($obj->{'result'} === true) {
      $result =  $obj->{'token'};
    } else {
      throw new SendSecureException($obj->{'code'}, $obj->{'message'});
    }

    return $result;
  }


  /**
    * @desc submit the SafeBox
    * @param Safebox, the safebox to submit
    * @return SafeboxResponse
  */
  public function submit_safebox($safebox) {
    $this->initialize_safebox($safebox);
    foreach ($safebox->attachments as $attachment) {
      $this->upload_attachment($safebox, $attachment);
    }
    if($safebox->security_profile == null){
      if($this->default_security_profile() == null){
        throw new SendSecureException(0, "No Security Profile configured");
      } else {
        $safebox->security_profile = $this->default_security_profile();
      }
    }
    return $this->commit_safebox($safebox);
  }


  /**
    * @desc initialize the SafeBox
    * @param Safebox, the safebox to attach to
    * @return
  */
  public function initialize_safebox($safebox) {
    $json = json_decode($this->json_client->new_safebox($safebox->user_email));
    $safebox->guid = $json->{'guid'};
    $safebox->public_encryption_key = $json->{'public_encryption_key'};
    $safebox->upload_url = $json->{'upload_url'};
  }


  /**
    * @desc commit the SafeBox
    * @param Safebox, the safebox to attach to
    *        Attachment, the attachment
    * @return Attachment
  */
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


  /**
    * @desc commit the SafeBox
    * @param Safebox, the safebox to commit
    * @return SafeboxResponse
  */
  public function commit_safebox($safebox) {
    $result = json_decode($this->json_client->commit_safebox($safebox->as_json_for_client()));
    return new SafeboxResponse($result->{"guid"},$result->{"preview_url"},$result->{"encryption_key"});
  }


  /**
    * @descthe default SecurityProfile
    * @param string $user_email, user email
    * @return SecurityProfile
  */
  public function default_security_profile($user_email) {
    $id = $this->enterprise_settings()->default_security_profile_id;
    foreach ($this->security_profiles($user_email) as $security_profile) {
      if ($security_profile->id == $id) {
        return $security_profile;
        break;
      }
    }
  }


  /**
    * @desc get all the SecurityProfile
    * @param string $user_email, user email
    * @return Array of SecurityProfile
  */
  public function security_profiles($user_email) {
    $security_profiles = array();
    $objs = json_decode($this->json_client->get_security_profiles($user_email))->{"security_profiles"};
    foreach ($objs as $obj) {
      array_push($security_profiles, SecurityProfile::from_json($obj));
    }
    return $security_profiles;
  }


  /**
    * @desc get the EnterpriseSettings
    * @param
    * @return EnterpriseSettings
  */
  public function enterprise_settings() {
    return EnterpriseSettings::from_json(json_decode($this->json_client->get_enterprise_settings()));
  }


  # UTILS
  public function __toString() {
    return "Token:{$this->api_token}\nEndpoint:{$this->endpoint}\nLocale:{$this->locale}\n";
  }

}

?>