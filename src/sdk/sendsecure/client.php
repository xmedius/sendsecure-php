<?php namespace SendSecure;

include 'exception.php';
include 'request.php';
include 'json_client.php';
include 'helpers/helpers.php';

/**
 * Class Client
 */
class Client {

  protected $api_token = null;
  protected $endpoint = null;
  protected $locale = null;
  protected $enterprise_account = null;
  protected $user_id = null;
  protected $json_client = null; //JsonClient

  /**
   * Client object constructor. Used to make call to create a SendSecure
   *
   * @param api_token
   *            The API Token to be used for authentication with the SendSecure service
   * @param user_id
   *            The user id
   * @param enterprise_account
   *            The SendSecure enterprise account
   * @param endpoint
   *            The URL to the SendSecure service ("https://portal.xmedius.com" will be used by default if empty)
   * @param locale
   *            The locale in which the server errors will be returned ("en" will be used by default if empty)
   */
  public function __construct($api_token, $user_id, $enterprise_account, $endpoint = ENDPOINT, $locale = 'en') {
    $this->api_token = $api_token;
    $this->user_id = $user_id;
    $this->enterprise_account = $enterprise_account;
    $this->endpoint = $endpoint;
    $this->locale = $locale;
    $this->json_client = new JsonClient($this->api_token, $this->user_id, $this->enterprise_account, $this->endpoint, $this->locale);
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
    $query_url = "/services/" . $enterprise_account . "/portal/host";
    $result = Request::get_http_request($endpoint, $query_url, null);

    $payload = array('permalink' => $enterprise_account, 'username' =>  $username, 'password' => $password, 'application_type' => $application_type, 'device_id' => $device_id, 'device_name' => $device_name, 'otp' => $one_time_password);
    $result = Request::post_http_request($result, "api/user_token", json_encode($payload), null);

    $obj = json_decode($result);
    if ($obj->{'result'} === true) {
      return $obj;
    } else {
      throw new SendSecureException($obj->{'code'}, $obj->{'message'});
    }
  }


  /**
   * This method is a high-level combo that initializes the SafeBox,
   * uploads all attachments and commits the SafeBox.
   *
   * @param safebox
   *            A non-initialized Safebox object with security profile, recipient(s), subject, message and attachments
   *            (not yet uploaded) already defined.
   * @return The updated safebox
   * @throws SendSecureException
   */
  public function submit_safebox($safebox) {
    $this->initialize_safebox($safebox);
    foreach ($safebox->attachments as $attachment) {
      $this->upload_attachment($safebox, $attachment);
    }
    if($safebox->security_profile_id == null){
      $default_security_profile = $this->default_security_profile($safebox->user_email);
      if($default_security_profile == null){
        throw new SendSecureException(0, "No Security Profile configured");
      } else {
        $safebox->security_profile_id = $default_security_profile->get_id();
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

  public function upload_reply_attachment($url, $attachment) {
    $json = null;
    if ($attachment->stream == null) {
      $json = json_decode($this->json_client->upload_file($url, $attachment->file_path, $attachment->content_type));
    } else {
      $json = json_decode($this->json_client->upload_file_stream($url, $attachment->stream, $attachment->content_type, $attachment->file_size));
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
   * @return The updated Safebox object.
   * @throws SendSecureException
   */
  public function commit_safebox($safebox) {
    if($safebox->guid == null) {
      throw new SendSecureException(0, "SafeBox GUID cannot be null");
    }
    if(empty($safebox->participants)) {
      throw new SendSecureException(0, "Participants cannot be empty");
    }
    if($safebox->security_profile_id == null) {
      throw new SendSecureException(0, "No Security Profile configured");
    }

    $result = json_decode($this->json_client->commit_safebox($safebox->to_json()));
    return $safebox->update_after_commit($result);
  }


  /**
   * Retrieves the default security profile of the enterprise
   * account for a specific user. A default security profile must have been set in the enterprise account, otherwise
   * the method will return nothing.
   *
   * @param user_email
   *            The email address of a SendSecure user of the current enterprise account
   * @return Default SecurityProfile of the enterprise, with all its setting values/properties.
   * @throws SendSecureException
   */
  public function default_security_profile($user_email) {
    $id = $this->enterprise_settings()->get_default_security_profile_id();
    foreach ($this->security_profiles($user_email) as $security_profile) {
      if ($security_profile->get_id() == $id) {
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

  /**
   * Retrieves all the current user's settings specific to SendSecure Account
   *
   * @return All values/properties of the user's settings specific to SendSecure.
   * @throws SendSecureException
   */
  public function user_settings() {
    return UserSettings::from_json(json_decode($this->json_client->get_user_settings()));
  }

  public function get_favorites() {
    $favorites = array();
    $response = json_decode($this->json_client->get_favorites())->{'favorites'};
    foreach ($response as $favorite) {
      array_push($favorites, Favorite::from_json($favorite));
    }
    return $favorites;
  }

  public function create_favorite($favorite) {
    $response = json_decode($this->json_client->create_favorite($favorite->to_json()));
    return Favorite::from_json($response);
  }

  public function update_favorite($favorite) {
    $response = json_decode($this->json_client->update_favorite($favorite->get_id(), $favorite->to_json()));
    return Favorite::from_json($response);
  }

  public function delete_favorite($favorite) {
    return $this->json_client->delete_favorite($favorite->get_id());
  }

  public function delete_favorite_contact_methods($favorite, $contact_method_ids) {
    $favorite->prepare_to_destroy_contact_methods($contact_method_ids);
    return $this->update_favorite($favorite);
  }

  public function create_participant($safebox, $participant) {
    if($safebox->guid == null) {
      throw new SendSecureException(0, "SafeBox GUID cannot be null");
    }
    $response = json_decode($this->json_client->create_participant($safebox->guid, $participant->to_json()));
    return Participant::from_json($response);
  }

  public function update_participant($safebox, $participant) {
    if($safebox->guid == null) {
      throw new SendSecureException(0, "SafeBox GUID cannot be null");
    }
    if($participant->get_id() == null) {
      throw new SendSecureException(0, "Participant ID cannot be null");
    }
    $response = json_decode($this->json_client->update_participant($safebox->guid, $participant->to_json(), $participant->get_id()));
    //TODO: Instead of returning a new participant object, update the existing $participant
    return Participant::from_json($response);
  }

  public function delete_participant_contact_methods($safebox, $participant, $contact_method_ids) {
    if($safebox->guid == null) {
      throw new SendSecureException(0, "SafeBox GUID cannot be null");
    }
    if($participant->get_id() == null) {
      throw new SendSecureException(0, "Participant ID cannot be null");
    }
    $participant->prepare_to_destroy_contact_methods($contact_method_ids);
    return $this->update_participant($safebox, $participant);
  }

  public function get_safebox_info($safebox, $sections = []) {
    if($safebox->guid == null) {
      throw new SendSecureException(0, "SafeBox GUID cannot be null");
    }
    $response = json_decode($this->json_client->get_safebox_info($safebox->guid, implode(",", $sections)));
    return Safebox::from_json($response->{'safebox'});
  }

  public function get_safeboxes($url, $search_params=[]) {
    $params = implode(',',
      array_map(function ($v, $k) { return $k.'='.$v; },
      $search_params,
      array_keys($search_params)
    ));
    return json_decode($this->json_client->get_safeboxes($url, $params));
  }

  public function get_safebox_participants($safebox){
    if($safebox->guid == null) {
      throw new SendSecureException(0, "SafeBox GUID cannot be null");
    }
    $participants = array();
    $response = json_decode($this->json_client->get_safebox_participants($safebox->guid))->{'participants'};
    foreach ($response as $participant) {
      array_push($participants, Participant::from_json($participant));
    }
    return $participants;
  }

  public function get_safebox_messages($safebox){
    if($safebox->guid == null) {
      throw new SendSecureException(0, "SafeBox GUID cannot be null");
    }
    $messages = array();
    $response = json_decode($this->json_client->get_safebox_messages($safebox->guid))->{'messages'};
    foreach ($response as $message) {
      array_push($messages, Message::from_json($message));
    }
    return $messages;
  }

  public function get_safebox_security_options($safebox){
    if($safebox->guid == null) {
      throw new SendSecureException(0, "SafeBox GUID cannot be null");
    }
    $response = json_decode($this->json_client->get_safebox_security_options($safebox->guid))->{'security_options'};
    return SecurityOptions::from_json($response);
  }

  public function get_safebox_download_activity($safebox){
    if($safebox->guid == null) {
      throw new SendSecureException(0, "SafeBox GUID cannot be null");
    }
    $response = json_decode($this->json_client->get_safebox_download_activity($safebox->guid))->{'download_activity'};
    return DownloadActivity::from_json($response);
  }

  public function get_safebox_event_history($safebox){
    if($safebox->guid == null) {
      throw new SendSecureException(0, "SafeBox GUID cannot be null");
    }
    $event_history = array();
    $response = json_decode($this->json_client->get_safebox_event_history($safebox->guid))->{'event_history'};
    foreach ($response as $event) {
      array_push($event_history, EventHistory::from_json($event));
    }
    return $event_history;
  }

  public function add_time($safebox, $value, $time_unit) {
    if($safebox->guid == null) {
      throw new SendSecureException(0, "SafeBox GUID cannot be null");
    }
    $params = '{"safebox":{"add_time_value":'.$value.',"add_time_unit":"'.$time_unit.'"}}';
    echo $params;
    $response = json_decode($this->json_client->add_time($safebox->guid, $params));
    $safebox->expiration = $response->new_expiration;
    return $response;
  }

  public function close_safebox($safebox) {
    if($safebox->guid == null) {
      throw new SendSecureException(0, "SafeBox GUID cannot be null");
    }
    return json_decode($this->json_client->close_safebox($safebox->guid));
  }

  public function delete_safebox_content($safebox) {
    if($safebox->guid == null) {
      throw new SendSecureException(0, "SafeBox GUID cannot be null");
    }
    return json_decode($this->json_client->delete_safebox_content($safebox->guid));
  }

  public function mark_as_read($safebox) {
    if($safebox->guid == null) {
      throw new SendSecureException(0, "SafeBox GUID cannot be null");
    }
    return json_decode($this->json_client->mark_as_read($safebox->guid));
  }

  public function mark_as_unread($safebox) {
    if($safebox->guid == null) {
      throw new SendSecureException(0, "SafeBox GUID cannot be null");
    }
    return json_decode($this->json_client->mark_as_unread($safebox->guid));
  }

  public function mark_as_read_message($safebox, $message) {
    if($safebox->guid == null) {
      throw new SendSecureException(0, "SafeBox GUID cannot be null");
    }
    if($message->get_id() == null) {
      throw new SendSecureException(0, "Message ID cannot be null");
    }
    return json_decode($this->json_client->mark_as_read_message($safebox->guid, $message->get_id()));
  }

  public function mark_as_unread_message($safebox, $message) {
    if($safebox->guid == null) {
      throw new SendSecureException(0, "SafeBox GUID cannot be null");
    }
    if($message->get_id() == null) {
      throw new SendSecureException(0, "Message ID cannot be null");
    }
    return json_decode($this->json_client->mark_as_unread_message($safebox->guid, $message->get_id()));
  }

  public function search_recipient($term) {
    return json_decode($this->json_client->search_recipient($term));
  }

  public function get_file_url($safebox, $document) {
    if($safebox->guid == null) {
      throw new SendSecureException(0, "SafeBox GUID cannot be null");
    }
    if($document->guid == null) {
      throw new SendSecureException(0, "Document GUID cannot be null");
    }
    if($safebox->user_email == null) {
      throw new SendSecureException(0, "SafeBox user email cannot be null");
    }
    $response = json_decode($this->json_client->get_file_url($safebox->guid, $document->guid, $safebox->user_email));
    return $response->url;
  }

  public function get_audit_record_url($safebox) {
    if($safebox->guid == null) {
      throw new SendSecureException(0, "SafeBox GUID cannot be null");
    }
    $response = json_decode($this->json_client->get_audit_record_url($safebox->guid));
    return $response->url;
  }

  public function get_audit_record_pdf($safebox) {
    if($safebox->guid == null) {
      throw new SendSecureException(0, "SafeBox GUID cannot be null");
    }
    return $this->json_client->get_audit_record_pdf($safebox->guid);
  }


  public function reply($safebox, $reply) {
    if($safebox->guid == null) {
      throw new SendSecureException(0, "SafeBox GUID cannot be null");
    }
    foreach ($reply->attachments as $attachment) {
      $file_params = $safebox->temporary_document($attachment->size);
      $file_response = json_decode($this->json_client->new_file($safebox->guid, $file_params));
      $this->upload_reply_attachment($file_response->upload_url, $attachment);
      array_push($reply->document_ids, $attachment->guid);
    }
    return json_decode($this->json_client->reply($safebox->guid, $reply->to_json()));
  }

  public function get_consent_group_messages($consent_group_id) {
    $response = json_decode($this->json_client->get_consent_group_messages($consent_group_id))->consent_message_group;
    return ConsentMessageGroup::from_json($response);
  }


  # UTILS
  public function __toString() {
    return "Token:{$this->api_token}\nEndpoint:{$this->endpoint}\nLocale:{$this->locale}\n";
  }

}

?>
