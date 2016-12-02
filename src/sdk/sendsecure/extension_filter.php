
<?php

/*********************************************************************************************/
//
// ExtensionFilter
//
/*********************************************************************************************/

class ExtensionFilter {

  public $mode = null;
  public $list = array();

  public function __construct($mode, $list) {
    $this->mode = $mode;
    $this->setting = $list;
  }

}

?>