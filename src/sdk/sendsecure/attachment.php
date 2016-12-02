<?php

/*********************************************************************************************/
//
// Attachment
//
/*********************************************************************************************/

class Attachment {

  public $guid = null;
  public $filename = null;
  public $content_type = null;
  public $size = null;
  public $file_path = null;
  public $stream = null;

  public function __construct() {

  }

  public static function from_file_path($file_path, $content_type) {
    $attachment = new Attachment();
    $attachment->file_path = $file_path;
    $attachment->content_type = $content_type;
    return $attachment;
  }

  public static function from_file_stream($stream, $filename, $content_type, $size) {
    $attachment = new Attachment();
    $attachment->stream = $stream;
    $attachment->filename = $filename;
    $attachment->content_type = $content_type;
    $attachment->size = $size;
    return $attachment;
  }

}

?>