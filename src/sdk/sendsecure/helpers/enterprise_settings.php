<?php namespace SendSecure;

/**
 * Class EnterpriseSettings represent the settings of an Enterprise Account
*/

class EnterpriseSettings {

  protected $created_at = null;
  protected $updated_at = null;
  public $default_security_profile_id = null;
  public $pdf_language = null;
  public $use_pdfa_audit_records = null;
  public $international_dialing_plan = null;
  public $extension_filter = null; //ExtensionFilter object
  public $include_users_in_autocomplete = null;
  public $include_favorites_in_autocomplete = null;

  public function __construct() {

  }

  /**
    * @desc build EnterpriseSettings from json
    * @param json $json, json returned from the Json client=
    * @return EnterpriseSettings
  */
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

}

?>