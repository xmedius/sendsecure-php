<?php namespace SendSecure;

/**
 * Class Value represent the value on a SecurityProfile
 */

class Value {

  public $value = null;
  public $modifiable = null; //boolean

  /**
    * @desc constructor
    * @param string $value, value
    * @param string $modifiable, boolean
  */
  public function __construct($value, $modifiable) {
    $this->value = $value;
    $this->modifiable = $modifiable;
  }

}

/**
 * Listed values
 */

//Time units
abstract class TimeUnit {
  const hours = "hours";
  const days = "days";
  const weeks = "weeks";
  const months = "months";
}

//Time units with year
abstract class LongTimeUnit {
  const hours = "hours";
  const days = "days";
  const weeks = "weeks";
  const months = "months";
  const years = "years"
;}

//Retention period type
abstract class RetentionPeriodType {
  const discard_at_expiration = "discard_at_expiration";
  const retain_at_expiration = "retain_at_expiration";
  const do_not_discard = "do_not_discard";
}

?>