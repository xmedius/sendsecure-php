<?php namespace SendSecure;

/**
 * Class Attachment builds an object to be uploaded to the server. Can be created either with a File or InputStream
*/

class Attachment {

  public $guid = null;
  public $filename = null;
  public $content_type = null;
  public $size = null;
  public $file_path = null;
  public $stream = null;

  public function __construct() {

  }

  /**
    * @desc build attachment from file
    * @param string $file_path, file path
    * @param string $content_type, content type
    * @return Attachment
  */
  public static function from_file_path($file_path, $content_type) {
    $attachment = new Attachment();
    $attachment->file_path = $file_path;
    $attachment->content_type = $content_type;
    return $attachment;
  }

  /**
    * @desc build attachment from stream
    * @param string $stream, stream
    * @param string $filename, filename
    * @param string $content_type, content type
    * @param string $size, file size
    * @return Attachment
  */
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