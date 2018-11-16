<?php namespace SendSecure;

/**
 * Class BaseHelper contains helpers common methods.
 */
class BaseHelper
{
    public function to_json()
    {
        $properties = new \stdClass;
        foreach ($this as $key => $value) {
            if (isset($value) && !in_array($key, $this->ignored_keys())) {
                $properties->$key = $value;
            }
        }
        return $properties;
    }
}
