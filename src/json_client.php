<?php

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

  # GET
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

?>