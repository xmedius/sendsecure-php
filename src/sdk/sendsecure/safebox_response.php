<?php

/*********************************************************************************************/
//
// SafeboxResponse
//
/*********************************************************************************************/

class SafeboxResponse {

  public $guid = null;
  public $preview_url = null;
  public $encryption_key = null;

  public function __construct($guid, $preview_url, $encryption_key) {
    $this->guid = $guid;
    $this->preview_url = $preview_url;
    $this->encryption_key = $encryption_key;
  }

}

?>