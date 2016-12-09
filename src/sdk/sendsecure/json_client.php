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
    $query_url = $this->get_sendsecure_endpoint() . "api/v2/safeboxes/new.json?user_email=".$user_email."&locale=".$this->locale;
    return Request::get_http_request($query_url, $this->api_token);
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
    $query_url = $this->get_sendsecure_endpoint() . "api/v2/safeboxes.json";
    return Request::post_http_request($query_url, $safebox_json, $this->api_token);
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
    $query_url = $this->get_sendsecure_endpoint() . "api/v2/enterprises/".$this->enterprise_account."/security_profiles.json?user_email=".$user_email."&locale=".$this->locale;
    return Request::get_http_request($query_url, $this->api_token);
  }

  /**
   * Get the Enterprise Settings of the current enterprise account
   *
   * @return The json containing the enterprise settings
   * @throws SendSecureException
   */
  public function get_enterprise_settings() {
    $query_url = $this->get_sendsecure_endpoint() . "api/v2/enterprises/".$this->enterprise_account."/settings.json?locale=".$this->locale;
    return Request::get_http_request($query_url, $this->api_token);
  }

  # PRIVATE
  private function get_sendsecure_endpoint() {
    if ($this->sendsecure_endpoint == null) {
      $query_url = $this->endpoint . "/services/" . $this->enterprise_account . "/sendsecure/server/url";
      $this->sendsecure_endpoint = Request::get_http_request($query_url, $this->api_token);
    }
    return $this->sendsecure_endpoint;
  }

  # UTILS
  public function __toString() {
    return "Token:{$this->api_token}\nEndpoint:{$this->endpoint}\nLocale:{$this->locale}\n";
  }

}

?>