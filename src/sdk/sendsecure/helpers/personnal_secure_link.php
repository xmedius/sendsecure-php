<?php namespace SendSecure;

/**
 * Class PersonnalSecureLink.
 */
class PersonnalSecureLink {

  private $enabled = null;
  private $url = null;
  private $security_profile_id = null;

  public function __construct($enabled, $url, $security_profile_id) {
    $this->enabled = $enabled;
    $this->url = $url;
    $this->security_profile_id = $security_profile_id;
  }

  public function get_enabled() {
    return $this->enabled;
  }

  public function get_url() {
    return $this->url;
  }

  public function get_security_profile_id() {
    return $this->security_profile_id;
  }

}

?>