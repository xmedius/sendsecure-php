<?php namespace SendSecure;

/**
 * Class EnterpriseSettings represent the settings of an Enterprise Account.
 */
class EnterpriseSettings {

  private $created_at = null;
  private $updated_at = null;
  private $default_security_profile_id = null;
  private $pdf_language = null;
  private $use_pdfa_audit_records = null;
  private $international_dialing_plan = null;
  private $extension_filter = null; //ExtensionFilter object
  private $virus_scan_enabled = null;
  private $max_file_size_value = null;
  private $max_file_size_unit = null;
  private $include_users_in_autocomplete = null;
  private $include_favorites_in_autocomplete = null;
  private $users_public_url = null;

  public function __construct() {}

  public function get_created_at() {
    return $this->created_at;
  }

  public function get_updated_at() {
    return $this->updated_at;
  }

  public function get_default_security_profile_id() {
    return $this->default_security_profile_id;
  }

  public function get_pdf_language() {
    return $this->pdf_language;
  }

  public function get_use_pdfa_audit_records() {
    return $this->use_pdfa_audit_records;
  }

  public function get_international_dialing_plan() {
    return $this->international_dialing_plan;
  }

  public function get_extension_filter() {
    return $this->extension_filter;
  }

  public function get_virus_scan_enabled() {
    return $this->virus_scan_enabled;
  }

  public function get_max_file_size_value() {
    return $this->max_file_size_value;
  }

  public function get_max_file_size_unit() {
    return $this->max_file_size_unit;
  }

  public function get_include_users_in_autocomplete() {
    return $this->include_users_in_autocomplete;
  }

  public function get_include_favorites_in_autocomplete() {
    return $this->include_favorites_in_autocomplete;
  }

  public function get_users_public_url() {
    return $this->users_public_url;
  }

  /**
   * @desc builds EnterpriseSettings from json
   * @param json $json, json returned from the Json client
   * @return EnterpriseSettings object
   */
  public static function from_json($json) {
    $enterprise_settings = new EnterpriseSettings();
    foreach ($json as $key => $value) {
      if ('extension_filter' == $key) {
        $enterprise_settings->extension_filter = new ExtensionFilter($value->mode, $value->list);
      } else {
        if(property_exists($enterprise_settings, $key)) {
          $enterprise_settings->$key = $value;
        }
      }
    }
    return $enterprise_settings;
  }
}

?>