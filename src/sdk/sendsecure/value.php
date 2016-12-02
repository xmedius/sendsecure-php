<?php

/*********************************************************************************************/
//
// Value
//
/*********************************************************************************************/

class Value {

  public $value = null;
  public $modifiable = null;

  public function __construct($value, $modifiable) {
    $this->value = $value;
    $this->modifiable = $modifiable;
  }

}

abstract class TimeUnit {
  const hours = "hours";
  const days = "days";
  const weeks = "weeks";
  const months = "months";
}

abstract class LongTimeUnit {
  const hours = "hours";
  const days = "days";
  const weeks = "weeks";
  const months = "months";
  const years = "years";
}


abstract class RetentionPeriodType {
  const discard_at_expiration = "discard_at_expiration";
  const retain_at_expiration = "retain_at_expiration";
  const do_not_discard = "do_not_discard";
}

?>