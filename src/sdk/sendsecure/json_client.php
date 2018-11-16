<?php namespace SendSecure;

/**
 * Class JsonClient
 */

class JsonClient {

  protected $api_token = null;
  protected $enterprise_account = null;
  protected $endpoint = null;
  protected $sendsecure_endpoint = null;
  protected $locale = null;

  /**
   * JsonClient object constructor. Used to make call to create a SendSecure
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
  }

  /**
   * Pre-creates a SafeBox on the SendSecure system and initializes the Safebox object accordingly.
   *
   * @param user_email
   *            The email address of a SendSecure user of the current enterprise account
   * @return The json containing the guid, public encryption key and upload url of the initialize SafeBox
   * @throws SendSecureException
   */
  public function new_safebox($user_email) {
    $query_url = "api/v2/safeboxes/new.json?user_email=".$user_email."&locale=".$this->locale;
    return Request::get_http_request($this->get_sendsecure_endpoint(), $query_url, $this->api_token);
  }

  /**
   * Uploads the specified file as an Attachment of the specified SafeBox.
   *
   * @param upload_url
   *            The url returned by the initializeSafeBox. Can be used multiple time
   * @param file_path
   *            The file to upload
   * @param content_type
   *            The MIME content type of the uploaded file
   * @return The json containing the guid of the uploaded file
   * @throws SendSecureException
   */
  public function upload_file($upload_url, $file_path, $content_type) {
    return Request::upload_file($upload_url, $file_path, $content_type);
  }

  /**
   * Uploads the specified file as an Attachment of the specified SafeBox.
   *
   * @param upload_url
   *            The url returned by the initializeSafeBox. Can be used multiple time
   * @param file_stream
   *            The InputStream containing the file to upload
   * @param content_type
   *            The MIME content type
   * @param filename
   *            The filename (with extension)
   * @param filesize
   *            The size
   * @return The json containing the guid of the uploaded file
   * @throws SendSecureException
   */
  public function upload_file_stream($upload_url, $file_stream, $content_type, $filename, $filesize) {
    return Request::upload_file_stream($upload_url, $file_stream, $content_type, $filename, $filesize);
  }

  /**
   * Finalizes the creation (commit) of the SafeBox on the SendSecure system. This actually "Sends" the SafeBox with
   * all content and contact info previously specified.
   *
   * @param safebox_json
   *            The full json expected by the server
   * @return The json containing the guid, preview url and encryption key of the created SafeBox
   * @throws SendSecureException
   */
  public function commit_safebox($safebox_json) {
    $query_url = "api/v2/safeboxes.json";
    return Request::post_http_request($this->get_sendsecure_endpoint(), $query_url, $safebox_json, $this->api_token);
  }

  /**
   * Retrieves all available security profiles of the enterprise account for a specific user.
   *
   * @param user_email
   *            The email address of a SendSecure user of the current enterprise account
   * @return The json containing a list of Security Profile
   * @throws SendSecureException
   */
  public function get_security_profiles($user_email) {
    $query_url = "api/v2/enterprises/".$this->enterprise_account."/security_profiles.json?user_email=".$user_email."&locale=".$this->locale;
    return Request::get_http_request($this->get_sendsecure_endpoint(), $query_url, $this->api_token);
  }

  /**
   * Get the User Settings of the current user
   *
   * @return The json containing the user settings
   * @throws SendSecureException
   */
  public function get_enterprise_settings() {
    $query_url = "api/v2/enterprises/".$this->enterprise_account."/settings.json?locale=".$this->locale;
    return Request::get_http_request($this->get_sendsecure_endpoint(), $query_url, $this->api_token);
  }

  /**
   * Get the User Settings of the current user account
   *
   * @return The json containing the user settings
   * @throws SendSecureException
   */
  public function get_user_settings() {
    $query_url = "api/v2/enterprises/".$this->enterprise_account."/users/".$this->user_id."/settings.json?locale=".$this->locale;
    return Request::get_http_request($this->get_sendsecure_endpoint(), $query_url, $this->api_token);
  }

  public function get_favorites() {
    $query_url = "api/v2/enterprises/".$this->enterprise_account."/users/".$this->user_id."/favorites.json?locale=".$this->locale;
    return Request::get_http_request($this->get_sendsecure_endpoint(), $query_url, $this->api_token);
  }

  public function create_favorite($favorite_json) {
    $query_url = "api/v2/enterprises/".$this->enterprise_account."/users/".$this->user_id."/favorites.json?locale=".$this->locale;
    return Request::post_http_request($this->get_sendsecure_endpoint(), $query_url, $favorite_json, $this->api_token);
  }

  public function update_favorite($favorite_id, $favorite_json) {
    $query_url = "api/v2/enterprises/".$this->enterprise_account."/users/".$this->user_id."/favorites/".$favorite_id."json?locale=".$this->locale;
    return Request::patch_http_request($this->get_sendsecure_endpoint(), $query_url, $favorite_json, $this->api_token);
  }

  public function delete_favorite($favorite_id) {
    $query_url = "api/v2/enterprises/".$this->enterprise_account."/users/".$this->user_id."/favorites/".$favorite_id."json?locale=".$this->locale;
    return Request::delete_http_request($this->get_sendsecure_endpoint(), $query_url, $this->api_token);
  }

  public function create_participant($safebox_guid, $participant_json) {
    $query_url = "api/v2/safeboxes/".$safebox_guid."/participants.json?locale=".$this->locale;
    return Request::post_http_request($this->get_sendsecure_endpoint(), $query_url, $participant_json, $this->api_token);
  }

  public function update_participant($safebox_guid, $participant_json, $participant_id) {
    $query_url = "api/v2/safeboxes/".$safebox_guid."/participants/".$participant_id.".json?locale=".$this->locale;
    return Request::patch_http_request($this->get_sendsecure_endpoint(), $query_url, $participant_json, $this->api_token);
  }

  public function get_safebox_info($safebox_guid, $sections) {
    $params = "";
    if(!empty($sections)){
      $params = "sections".$sections;
    }
    $query_url = "api/v2/safeboxes/".$safebox_guid."/".$params."?locale=".$this->locale;
    return Request::get_http_request($this->get_sendsecure_endpoint(), $query_url, $this->api_token);
  }

  public function get_safeboxes($url, $search_params) {
    if(empty($url)) {
      $url = "api/v2/safeboxes?".$search_params."?locale=".$this->locale;
    }
    return Request::get_http_request($this->get_sendsecure_endpoint(), $url, $this->api_token);
  }

  public function get_safebox_participants($safebox_guid){
    $query_url = "api/v2/safeboxes/".$safebox_guid."/participants.json?locale=".$this->locale;
    return Request::get_http_request($this->get_sendsecure_endpoint(), $query_url, $this->api_token);
  }

  public function get_safebox_messages($safebox_guid){
    $query_url = "api/v2/safeboxes/".$safebox_guid."/messages.json?locale=".$this->locale;
    return Request::get_http_request($this->get_sendsecure_endpoint(), $query_url, $this->api_token);
  }

  public function get_safebox_security_options($safebox_guid){
    $query_url = "api/v2/safeboxes/".$safebox_guid."/security_options.json?locale=".$this->locale;
    return Request::get_http_request($this->get_sendsecure_endpoint(), $query_url, $this->api_token);
  }

  public function get_safebox_download_activity($safebox_guid){
    $query_url = "api/v2/safeboxes/".$safebox_guid."/download_activity.json?locale=".$this->locale;
    return Request::get_http_request($this->get_sendsecure_endpoint(), $query_url, $this->api_token);
  }

  public function get_safebox_event_history($safebox_guid){
    $query_url = "api/v2/safeboxes/".$safebox_guid."/event_history.json?locale=".$this->locale;
    return Request::get_http_request($this->get_sendsecure_endpoint(), $query_url, $this->api_token);
  }

  public function add_time($safebox_guid, $time_json) {
    $query_url = "api/v2/safeboxes/".$safebox_guid."/add_time.json?locale=".$this->locale;
    return Request::patch_http_request($this->get_sendsecure_endpoint(), $query_url, $time_json, $this->api_token);
  }

  public function close_safebox($safebox_guid) {
    $query_url = "api/v2/safeboxes/".$safebox_guid."/close.json?locale=".$this->locale;
    return Request::patch_http_request($this->get_sendsecure_endpoint(), $query_url, null, $this->api_token);
  }

  public function delete_safebox_content($safebox_guid) {
    $query_url = "api/v2/safeboxes/".$safebox_guid."/delete_content.json?locale=".$this->locale;
    return Request::patch_http_request($this->get_sendsecure_endpoint(), $query_url, null, $this->api_token);
  }

  public function mark_as_read($safebox_guid) {
    $query_url = "api/v2/safeboxes/".$safebox_guid."/mark_as_read.json?locale=".$this->locale;
    return Request::patch_http_request($this->get_sendsecure_endpoint(), $query_url, null, $this->api_token);
  }

  public function mark_as_unread($safebox_guid) {
    $query_url = "api/v2/safeboxes/".$safebox_guid."/mark_as_unread.json?locale=".$this->locale;
    return Request::patch_http_request($this->get_sendsecure_endpoint(), $query_url, null, $this->api_token);
  }

  public function mark_as_read_message($safebox_guid, $message_id) {
    $query_url = "api/v2/safeboxes/".$safebox_guid."/messages/".$message_id."/read.json?locale=".$this->locale;
    return Request::patch_http_request($this->get_sendsecure_endpoint(), $query_url, null, $this->api_token);
  }

  public function mark_as_unread_message($safebox_guid, $message_id) {
    $query_url = "api/v2/safeboxes/".$safebox_guid."/messages/".$message_id."/unread.json?locale=".$this->locale;
    return Request::patch_http_request($this->get_sendsecure_endpoint(), $query_url, null, $this->api_token);
  }

  public function search_recipient($term) {
    $query_url = "api/v2/participants/autocomplete?term=".$term.".json?locale=".$this->locale;
    return Request::get_http_request($this->get_sendsecure_endpoint(), $query_url, $this->api_token);
  }

  public function get_file_url($safebox_guid, $document_guid, $user_email) {
    $query_url = "api/v2/safeboxes/".$safebox_guid."/documents/".$document_guid."/url?user_email=".$user_email."?locale=".$this->locale;
    return Request::get_http_request($this->get_sendsecure_endpoint(), $query_url, $this->api_token);
  }

  public function get_audit_record_url($safebox_guid) {
    $query_url = "api/v2/safeboxes/".$safebox_guid."/audit_record_pdf.json?locale=".$this->locale;
    return Request::get_http_request($this->get_sendsecure_endpoint(), $query_url, $this->api_token);
  }

  public function get_audit_record_pdf($safebox_guid) {
    $query_url = json_decode($this->get_audit_record_url($safebox_guid));
    return Request::get_http_request($query_url->url, null, null);
  }

  public function new_file($safebox_guid, $file_params) {
    $query_url = "api/v2/safeboxes/".$safebox_guid."/uploads.json?locale=".$this->locale;
    return Request::post_http_request($this->get_sendsecure_endpoint(), $query_url, $file_params, $this->api_token);
  }

  public function reply($safebox_guid, $reply_params) {
    $query_url = "api/v2/safeboxes/".$safebox_guid."/messages.json?locale=".$this->locale;
    return Request::post_http_request($this->get_sendsecure_endpoint(), $query_url, $reply_params, $this->api_token);
  }

  public function get_consent_group_messages($consent_group_id) {
    $query_url = "api/v2/enterprises/".$this->enterprise_account."/consent_message_groups/".$consent_group_id."?locale=".$this->locale;
    return Request::get_http_request($this->get_sendsecure_endpoint(), $query_url, $this->api_token);
  }

  # PRIVATE
  private function get_sendsecure_endpoint() {
    if ($this->sendsecure_endpoint == null) {
      $query_url = "/services/" . $this->enterprise_account . "/sendsecure/server/url";
      $this->sendsecure_endpoint = Request::get_http_request($this->endpoint, $query_url, $this->api_token);
    }
    return $this->sendsecure_endpoint;
  }

  # UTILS
  public function __toString() {
    return "Token:{$this->api_token}\nEndpoint:{$this->endpoint}\nLocale:{$this->locale}\n";
  }

}

?>
