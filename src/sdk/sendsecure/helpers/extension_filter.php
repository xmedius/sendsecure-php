<?php namespace SendSecure;

/**
 * Class ExtensionFilter represent the list of allow/forbid extension for an attachment
*/

class ExtensionFilter {

  public $mode = null;
  public $list = array();

  /**
    * @desc constructor
    * @param string $mode, mode
    * @param string $list, array of extension
    * @return json, request json result
  */
  public function __construct($mode, $list) {
    $this->mode = $mode;
    $this->setting = $list;
  }

}

?>