<?php

/*********************************************************************************************/
//
// Curl methods
//
/*********************************************************************************************/

#API
class Api {

  public static function get_http_request($query_url, $token) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization-Token: '.$token]);
    curl_setopt($ch, CURLOPT_URL, $query_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, '60');
    $result = trim(curl_exec($ch));

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

  public static function post_http_request($query_url, $payload, $token) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization-Token: '.$token, 'Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_URL, $query_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, '60');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    $result = trim(curl_exec($ch));

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

  public static function upload_file($query_url, $file, $content_type) {
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

  public static function upload_file_stream($query_url, $stream, $content_type, $filename, $filesize) {
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

  private static function read_stream($ch, $fp, $len) {
    static $pos=0; // keep track of position
    // set data
    $data = substr($GLOBALS['streamcontent'], $pos, $len);
    // increment $pos
    $pos += strlen($data);
    // return the data to send in the request
    return $data;
  }

}

?>