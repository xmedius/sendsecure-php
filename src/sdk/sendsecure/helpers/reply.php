<?php namespace SendSecure;

class Reply extends BaseHelper
{
    public $message = null;
    public $consent = null;
    public $attachments = array(); //Array of Attachment objects.
    public $document_ids = array();

    public function __construct($message, $consent, $attachments = [])
    {
        $this->message = $message;
        $this->consent = $consent;
        $this->attachments = $attachments;
    }

    public function ignored_keys()
    {
        return ["attachments"];
    }

    public function to_json()
    {
        $reply = (object) array("safebox" => parent::to_json());
        return $reply;
    }
}
