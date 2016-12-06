<?php

/*********************************************************************************************/
//
// Safebox object
//
/*********************************************************************************************/

class Safebox {

  public $guid = null;
  public $upload_url = null;
  public $public_encryption_key = null;

  public $user_email = null;
  public $subject = null;
  public $message = null;

  public $recipients = array(); //Recipient objects
  public $attachments = array(); //Attchment objects

  public $security_profile = null; //Security profile object
  public $notification_language = null;

  /**
    * @desc constructor
    * @param string $user_email, user email
    * @return
  */
  public function __construct($user_email) {
    $this->user_email = $user_email;
  }

  /**
    * @desc build SecurityProfile object from Json
    * @param
    * @return Json, json structure for JsonClient
  */
  public function as_json_for_client() {

    $all_recipients = array();
    foreach ($this->recipients as $recipient) {
      array_push($all_recipients, $recipient->to_json());
    }

    $all_documents = array();
    foreach ($this->attachments as $attachment) {
      array_push($all_documents, $attachment->guid);
    }

    $safebox = array(
      'safebox' => array(
      'guid' => $this->guid,
      'recipients' => $all_recipients,
      'subject' => $this->subject,
      'message' => $this->message,
      'security_profile_id' => $this->security_profile->id,
      'document_ids' => $all_documents,
      'reply_enabled' => $this->security_profile->reply_enabled->value,
      'group_replies' => $this->security_profile->group_replies->value,
      'expiration_value' => $this->security_profile->expiration_value->value,
      'expiration_unit' => $this->security_profile->expiration_unit->value,
      'retention_period_type' => $this->security_profile->retention_period_type->value,
      'retention_period_value' => $this->security_profile->retention_period_value->value,
      'retention_period_unit' => $this->security_profile->retention_period_unit->value,
      'encrypt_message' => $this->security_profile->encrypt_message->value,
      'double_encryption' => $this->security_profile->double_encryption->value,
      'public_encryption_key' => $this->public_encryption_key,
      'notification_language' => $this->notification_language));

    var_dump(json_encode($safebox));
    return json_encode($safebox);

  }

}

?>