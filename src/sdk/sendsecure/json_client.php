<?php

/*********************************************************************************************/
//
// Json Client object
//
/*********************************************************************************************/

//JSONCLIENT
class JsonClient {

  protected $api_token = null;
  protected $enterprise_account = null;
  protected $endpoint = null;
  protected $sendsecure_endpoint = null;
  protected $locale = null;

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
  }
  /**
    * @desc get json of new safebox
    * @param string $user_email, user email
    * @return json, request json result
  */
  public function new_safebox($user_email) {
    $query_url = $this->get_sendsecure_endpoint() . "api/v2/safeboxes/new.json?user_email=".$user_email."&locale=".$this->locale;
    return Request::get_http_request($query_url, $this->api_token);
  }

  /**
    * @desc upload a file
    * @param string $upload_url, upload url
    *        string $file_path, file path
    *        string $content_type, file content type
    * @return json, request json result
  */
  public function upload_file($upload_url, $file_path, $content_type) {
    return Request::upload_file($upload_url, $file_path, $content_type);
  }

  /**
    * @desc upload a stream
    * @param string $upload_url, upload url
    *        string $file_stream, file stream
    *        string $content_type, file content type
    *        string $filename, file name
    *        string $filesize, rfile size
    * @return json, request json result
  */
  public function upload_file_stream($upload_url, $file_stream, $content_type, $filename, $filesize) {
    return Request::upload_file_stream($upload_url, $file_stream, $content_type, $filename, $filesize);
  }
  /**
    * @desc commit a safebox
    * @param string $safebox_json, json format of the safebox
    * @return json, request json result
  */
  public function commit_safebox($safebox_json) {
    $query_url = $this->get_sendsecure_endpoint() . "api/v2/safeboxes.json";
    return Request::post_http_request($query_url, $safebox_json, $this->api_token);
  }

  /**
    * @desc get all the security profiles
    * @param string $email, user email
    * @return json, request json result
  */
  public function get_security_profiles($user_email) {
    $query_url = $this->get_sendsecure_endpoint() . "api/v2/enterprises/".$this->enterprise_account."/security_profiles.json?user_email=".$user_email."&locale=".$this->locale;
    return Request::get_http_request($query_url, $this->api_token);
  }

  /**
    * @desc get the enterprise setting
    * @param
    * @return json, request json result
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