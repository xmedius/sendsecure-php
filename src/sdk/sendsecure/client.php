<?php namespace SendSecure;

include 'config.php';
include 'exception.php';
include 'utils/response.php';
include 'utils/client.php';
include 'request.php';
include 'json_client.php';
include 'helpers/attachment.php';
include 'helpers/contact_method.php';
include 'helpers/enterprise_settings.php';
include 'helpers/extension_filter.php';
include 'helpers/recipient.php';
include 'helpers/safebox.php';
include 'helpers/safebox_response.php';
include 'helpers/security_profile.php';
include 'helpers/value.php';

/**
 * Class Client
 */

class Client {

  protected $api_token = null;
  protected $endpoint = null;
  protected $locale = null;
  protected $enterprise_account = null;

  protected $json_client = null; //JsonClient

  /**
   * Client object constructor. Used to make call to create a SendSecure
   *
   * @param api_token
   *            The API Token to be used for authentication with the SendSecure service
   * @param enterprise_account
   *            The SendSecure enterprise account
   * @param endpoint
   *            The URL to the SendSecure service ("https://portal.xmedius.com" will be used by default if empty)
   * @param locale
   *            The locale in which the server errors will be returned ("en" will be used by default if empty)
   */
  public function __construct($api_token, $enterprise_account, $endpoint = ENDPOINT, $locale = 'en') {
    $this->api_token = $api_token;
    $this->enterprise_account = $enterprise_account;
    $this->endpoint = $endpoint;
    $this->locale = $locale;
    $this->json_client = new JsonClient($this->api_token, $this->enterprise_account, $this->endpoint, $this->locale);
  }

  /**
   * Gets an API Token for a specific user within a SendSecure enterprise account.
   *
   * @param enterprise_account
   *            The SendSecure enterprise account
   * @param username
   *            The username of a SendSecure user of the current enterprise account
   * @param password
   *            The password of this user
   * @param device_id
   *            The unique ID of the device used to get the Token
   * @param device_name
   *            The name of the device used to get the Token
   * @param application_type
   *            The type/name of the application used to get the Token ("SendSecure Java" will be used by default if empty)
   * @param one_time_password
   *            The one-time password of this user (if any)
   * @param endpoint
   *            The URL to the SendSecure service ("https://portal.xmedius.com" will be used by default if empty)
   * @return API Token to be used for the specified user
   * @throws SendSecureException
   */
  public static function get_user_token($enterprise_account, $username, $password, $device_id, $device_name, $application_type = "SendSecure PHP", $endpoint = ENDPOINT, $one_time_password = "") {

    // Get portal url
    $query_url = $endpoint . "/services/" . $enterprise_account . "/portal/host";
    $result = Request::get_http_request($query_url, null);

    $payload = array('permalink' => $enterprise_account, 'username' =>  $username, 'password' => $password, 'application_type' => $application_type, 'device_id' => $device_id, 'device_name' => $device_name, 'otp' => $one_time_password);
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
   * This method is a high-level combo that {@link #initializeSafebox initializes} the SafeBox,
   * {@link #uploadAttachment uploads} all attachments and {@link #commitSafebox commits} the SafeBox.
   *
   * @param safebox
   *            A non-initialized Safebox object with security profile, recipient(s), subject, message and attachments
   *            (not yet uploaded) already defined.
   * @return {@link com.xmedius.sendsecure.helper.SafeboxResponse SafeboxResponse}
   * @throws SendSecureException
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
   * Pre-creates a SafeBox on the SendSecure system and initializes the Safebox object accordingly.
   *
   * @param safebox
   *            A Safebox object to be initialized by the SendSecure system
   * @return The updated SafeBox object with the necessary system parameters (GUID, public encryption key, upload URL)
   *         filled out.
   * @throws SendSecureException
   */
  public function initialize_safebox($safebox) {
    $json = json_decode($this->json_client->new_safebox($safebox->user_email));
    $safebox->guid = $json->{'guid'};
    $safebox->public_encryption_key = $json->{'public_encryption_key'};
    $safebox->upload_url = $json->{'upload_url'};
  }


  /**
   * Uploads the specified file as an Attachment of the specified SafeBox.
   *
   * @param safebox
   *            An initialized Safebox object
   * @param attachment
   *            An Attachment object - the file to upload to the SendSecure system
   * @return The updated Attachment object with the GUID parameter filled out.
   * @throws SendSecureException
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
   * Finalizes the creation (commit) of the SafeBox on the SendSecure system. This actually "Sends" the SafeBox with
   * all content and contact info previously specified.
   *
   * @param safebox
   *            A Safebox object already initialized, with security profile, recipient(s), subject and message already
   *            defined, and attachments already uploaded.
   * @return {@link com.xmedius.sendsecure.helper.SafeboxResponse SafeboxResponse}
   * @throws SendSecureException
   */
  public function commit_safebox($safebox) {
    $result = json_decode($this->json_client->commit_safebox($safebox->as_json()));
    return new SafeboxResponse($result->{"guid"},$result->{"preview_url"},$result->{"encryption_key"});
  }


  /**
   * Retrieves the default {@link com.xmedius.sendsecure.helper.SecurityProfile security profile} of the enterprise
   * account for a specific user. A default security profile must have been set in the enterprise account, otherwise
   * the method will return nothing.
   *
   * @param user_email
   *            The email address of a SendSecure user of the current enterprise account
   * @return Default security profile of the enterprise, with all its setting values/properties.
   * @throws SendSecureException
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
   * Retrieves all available security profiles of the enterprise account for a specific user.
   *
   * @param user_email
   *            The email address of a SendSecure user of the current enterprise account
   * @return The list of all security profiles of the enterprise account, with all their setting values/properties.
   * @throws SendSecureException
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
   * Retrieves all the current enterprise account's settings specific to SendSecure Account
   *
   * @return All values/properties of the enterprise account's settings specific to SendSecure.
   * @throws SendSecureException
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