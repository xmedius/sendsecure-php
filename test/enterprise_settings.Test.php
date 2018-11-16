<?php

use PHPUnit\Framework\TestCase;

class EnterpriseSettingsTest extends TestCase {

  private $enterprise_settings;

  protected function setUp() {
    $json = '{"created_at": "2016-09-08T18:54:43.018Z",
              "updated_at": "2017-03-23T16:12:09.411Z",
              "default_security_profile_id": 14,
              "pdf_language": "en",
              "use_pdfa_audit_records": false,
              "international_dialing_plan": "ca",
              "extension_filter": {
                "mode": "forbid",
                "list": [
                  "bat",
                  "bin"]},
              "virus_scan_enabled": false,
              "max_file_size_value": null,
              "max_file_size_unit": null,
              "include_users_in_autocomplete": true,
              "include_favorites_in_autocomplete": true,
              "users_public_url": true
            }';
    $this->enterprise_settings = \SendSecure\EnterpriseSettings::from_json(json_decode($json));
  }

  public function test_enterprise_settings_from_json() {
    $this->assertEquals($this->enterprise_settings->get_created_at(), "2016-09-08T18:54:43.018Z");
    $this->assertEquals($this->enterprise_settings->get_updated_at(), "2017-03-23T16:12:09.411Z");
    $this->assertEquals($this->enterprise_settings->get_default_security_profile_id(), 14);
    $this->assertEquals($this->enterprise_settings->get_pdf_language(), "en");
    $this->assertFalse($this->enterprise_settings->get_use_pdfa_audit_records());
    $this->assertEquals($this->enterprise_settings->get_international_dialing_plan(), "ca");

    $extension_filter = $this->enterprise_settings->get_extension_filter();
    $this->assertInstanceOf(\SendSecure\ExtensionFilter::class, $extension_filter);
    $this->assertEquals($extension_filter->get_mode(), "forbid");
    $this->assertSame($extension_filter->get_list(), ["bat","bin"]);

    $this->assertFalse($this->enterprise_settings->get_virus_scan_enabled());
    $this->assertNull($this->enterprise_settings->get_max_file_size_unit());
    $this->assertNull($this->enterprise_settings->get_max_file_size_value());
    $this->assertTrue($this->enterprise_settings->get_include_users_in_autocomplete());
    $this->assertTrue($this->enterprise_settings->get_include_favorites_in_autocomplete());
    $this->assertTrue($this->enterprise_settings->get_users_public_url());
  }

}

?>