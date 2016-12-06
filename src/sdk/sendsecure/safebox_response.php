<?php

/*********************************************************************************************/
//
// SafeboxResponse object
//
/*********************************************************************************************/

class SafeboxResponse {

  public $guid = null;
  public $preview_url = null;
  public $encryption_key = null;

  /**
    * @desc constructor
    * @param string $guid, global id
    *        string $preview_url, safebox url
    *        string $encryption_key, safebox encryption key
    * @return
  */
  public function __construct($guid, $preview_url, $encryption_key) {
    $this->guid = $guid;
    $this->preview_url = $preview_url;
    $this->encryption_key = $encryption_key;
  }

}

?>