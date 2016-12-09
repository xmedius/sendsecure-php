<?php namespace SendSecure;

/**
 * Class SendSecureException
 */

class SendSecureException extends \Exception
{

    #
    # code message
    # 0XX generic error code, when the server does not behave as expected
    #   1 unexpected server response format
    #
    # 1XX user token error code
    # 100 unexpected user token error
    # 101 invalid permalink
    # 102 invalid credentials
    # 103 missing application_type parameter
    # 104 missing device_id parameter
    # 105 missing device_name parameter
    # 106 otp needed
    #
    # 404 not found
    # 500 unexpected error
    #

    public $response_content;

    /**
      * @desc constructor
      * @param string $code, error code
      * @param string $message, error message
      * @param string $previous, error previous
      * @return json, request json result
    */
    public function __construct($code, $message = 'unexpected server response format', $response_content = '', Exception $previous = null) {

        switch ($code) {
          case 100:
            $message = 'unexpected error';
            break;
          case 101:
            $message = 'invalid permalink';
            break;
          case 101:
            $message = 'invalid credentials';
            break;
          case 101:
            $message = 'missing application_type parameter';
            break;
          case 101:
            $message = 'missing device_id parameter';
            break;
          case 101:
            $message = 'missing device_name parameter';
            break;
          case 101:
            $message = 'otp needed';
            break;
          case 404:
            $message = 'not found';
            break;
          case 500:
            $message = 'unexpected error';
            break;
          default:
            $message = 'unexpected exception';
            break;
        }
        $this->response_content = $response_content;
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

}

class UnexpectedServerResponseException extends SendSecureException {}

?>