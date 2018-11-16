<?php namespace SendSecure;

include 'utils/Response.php';
include 'utils/Client.php';

/**
 * Class Request - HTTP requests using SendGrid PHP HTTP Client
 */

class Request
{

  /**
    * @desc HTTP GET Request with params
    * @param string $endpoint, the base url
    * @param string $path, query path with params
    * @param string $token, access token
    * @return json, json result
  */
    public static function get_http_request($endpoint, $path, $token)
    {
        $client = new \SendGrid\Client($endpoint, ['Authorization-Token: '.$token], null, [$path]);
        $response = $client->get();
        switch ($http_code = $response->statusCode()) {
      case $http_code < 400:
        break;
      default:
        throw new SendSecureException($http_code, $response->body());
    }
        return $response->body();
    }

    /**
      * @desc HTTP POST Request with params
      * @param string $endpoint, the base url
      * @param string $path, query path with params
      * @param json $body, request body
      * @param string $token, access token
      * @return json, json result
    */
    public static function post_http_request($endpoint, $path, $body, $token)
    {
        $client = new \SendGrid\Client($endpoint, ['Authorization-Token: '.$token], null, [$path]);
        $response = $client->post($body);
        switch ($http_code = $response->statusCode()) {
      case $http_code < 400:
        break;
      default:
        throw new SendSecureException($http_code, $response->body());
    }
        return $response->body();
    }

    /**
      * @desc HTTP PATCH Request with params
      * @param string $endpoint, the base url
      * @param string $path, query path with params
      * @param json $body, request body
      * @param string $token, access token
      * @return json, json result
    */
    public static function patch_http_request($endpoint, $path, $body, $token)
    {
        $client = new \SendGrid\Client($endpoint, ['Authorization-Token: '.$token], null, [$path]);
        $response = $client->patch($body);
        switch ($http_code = $response->statusCode()) {
      case $http_code < 400:
        break;
      default:
        throw new SendSecureException($http_code, $response->body());
    }
        return $response->body();
    }

    /**
      * @desc HTTP DELETE Request with params
      * @param string $endpoint, the base url
      * @param string $path, query path with params
      * @param json $body, request body
      * @param string $token, access token
      * @return json, json result
    */
    public static function delete_http_request($endpoint, $path, $token, $body = null)
    {
        $client = new \SendGrid\Client($endpoint, ['Authorization-Token: '.$token], null, [$path]);
        $response = $client->delete($body);
        switch ($http_code = $response->statusCode()) {
      case $http_code < 400:
        break;
      default:
        throw new SendSecureException($http_code, $response->body());
    }
        return $response->body();
    }

    /**
      * @desc POST HTTP Request for file
      * @param string $query_url, full url
      * @param string $file, file
      * @param string $content_type, file content_type
      * @return json, json result
    */
    public static function upload_file($query_url, $file, $content_type)
    {
        $ch = curl_init();

        if (function_exists('curl_file_create')) { // php 5.6+
            $c_file = curl_file_create($file);
        } else { //
            $c_file = '@' . realpath($file);
        }

        $post = array('type' => $content_type,'file_contents'=> $c_file);
        curl_setopt($ch, CURLOPT_URL, $query_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, '60');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $result = curl_exec($ch);

        if (!curl_errno($ch)) {
            switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
        case $http_code < 400:
          break;
        default:
          throw new UnexpectedServerResponseException($http_code);
      }
        }

        curl_close($ch);
        return $result;
    }

    /**
      * @desc POST HTTP Request for file stream
      * @param string $query_url, full url
      * @param string $stream, file stream
      * @param string $content_type, stream content_type
      * @param string $filename, file name
      * @param string $filesize, file size
      * @return json, json result
    */
    public static function upload_file_stream($query_url, $stream, $content_type, $filename, $filesize)
    {
        $ch = curl_init();

        global $streamcontent;
        $streamcontent = $stream;

        curl_setopt($ch, CURLOPT_URL, $query_url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, '60');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: '.$content_type.';', 'Content-Disposition: attachment; filename="'.$filename.'"', 'Content-Length: '. $filesize));
        curl_setopt($ch, CURLOPT_READFUNCTION, 'Api::read_stream');
        $result = curl_exec($ch);

        if (!curl_errno($ch)) {
            switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
        case $http_code < 400:
          break;
        default:
          throw new UnexpectedServerResponseException($http_code);
      }
        }

        curl_close($ch);
        return $result;
    }

    private static function read_stream($ch, $fp, $len)
    {
        static $pos=0; // keep track of position
        // set data
        $data = substr($GLOBALS['streamcontent'], $pos, $len);
        // increment $pos
        $pos += strlen($data);
        // return the data to send in the request
        return $data;
    }
}
