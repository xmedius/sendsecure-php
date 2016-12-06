
<?php

/*********************************************************************************************/
//
// ExtensionFilter object
//
/*********************************************************************************************/

class ExtensionFilter {

  public $mode = null;
  public $list = array();

  /**
    * @desc constructor
    * @param string $mode, mode
    *        string $list, array of extension
    * @return json, request json result
  */
  public function __construct($mode, $list) {
    $this->mode = $mode;
    $this->setting = $list;
  }

}

?>