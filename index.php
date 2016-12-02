
<?php

  include 'sendsecure.php';

  $token = Client::get_user_token('acme');
  echo "\n-------------------\n";
  echo "\n" . $token . "\n";
  echo "\n-------------------\n";

  $json_client = new JsonClient($token, 'acme');
  echo "\n" . $json_client . "\n";
  echo "\n-------------------\n";

  $json_new_safebox = $json_client->new_safebox('jalbert.levesque@xmedius.com');
  echo "\n" . $json_new_safebox . "\n";
  echo "\n-------------------\n";

  $json_enterprise_setting= $json_client->get_enterprise_settings();
  echo "\n" . $json_enterprise_setting . "\n";
  echo "\n-------------------\n";

  $json_security_profiles= $json_client->get_security_profiles('jalbert.levesque@xmedius.com');
  echo "\n" . $json_security_profiles . "\n";
  echo "\n-------------------\n";

  //$upload_file = $json_client->upload_file(json_decode($json_new_safebox)->upload_url,'/home/jlevesque/test.txt','text/plain');
  //echo "\n" . $upload_file . "\n";
  echo "\n-------------------\n";

  //$upload_file2 = $json_client->upload_file_stream(json_decode($json_new_safebox)->upload_url, 'test','text/plain', 'test.txt', 4);
  //echo "\n" . $upload_file2 . "\n";
  echo "\n-------------------\n";

  $payload = array('safebox' => array(
    'guid' => json_decode($json_new_safebox)->guid,
    'recipients' => array(array("email" => "test@test.com")),
    'subject' => "subject",
    'message' => "message",
    'security_profile_id' => 39,//json_decode($json_security_profiles)->default,
    'reply_enabled' => true,
    'group_replies' => true,
    'expiration_value' => 1,
    'expiration_unit' => 'months',
    'retention_period_type' => 'discard_at_expiration',
    'encrypt_message' => true,
    'double_encryption' => false,
    'public_encryption_key' => json_decode($json_new_safebox)->public_encryption_key,
    'notification_language' => 'en'));
  $payload = json_encode($payload);
  //$commit_safebox = $json_client->commit_safebox($payload);
  //echo "\n" . $commit_safebox . "\n";
  echo "\n-------------------\n";

  $json = json_decode('{"id":1,"default_security_profile_id":193,"created_at":"2015-12-21T20:52:17.240Z","updated_at":"2016-11-30T21:24:17.029Z","pdf_language":"en","use_pdfa_audit_records":false,"international_dialing_plan":"us","extension_filter":{"mode":"forbid","list":["cmd","exe","foo","patch","ppt"]},"include_users_in_autocomplete":true,"include_favorites_in_autocomplete":true}');
  $enterprise_setting = EnterpriseSettings::from_json($json);
  var_dump($enterprise_setting);
  echo "\n-------------------\n";

  $json = json_decode('{"id":39,"name":"All Contact Method Allowed!","description":"All Contact Method Allowed!","created_at":"2016-01-27T18:53:00.631Z","updated_at":"2016-09-14T18:41:23.043Z","allowed_login_attempts":{"value":10,"modifiable":false},"allow_remember_me":{"value":true,"modifiable":false},"allow_sms":{"value":true,"modifiable":false},"allow_voice":{"value":true,"modifiable":false},"allow_email":{"value":true,"modifiable":false},"code_time_limit":{"value":5,"modifiable":false},"code_length":{"value":6,"modifiable":false},"auto_extend_value":{"value":6,"modifiable":false},"auto_extend_unit":{"value":"hours","modifiable":false},"two_factor_required":{"value":true,"modifiable":false},"encrypt_attachments":{"value":true,"modifiable":false},"encrypt_message":{"value":true,"modifiable":false},"expiration_value":{"value":7,"modifiable":false},"expiration_unit":{"value":"days","modifiable":false},"reply_enabled":{"value":true,"modifiable":true},"group_replies":{"value":true,"modifiable":false},"double_encryption":{"value":false,"modifiable":false},"retention_period_value":{"value":null,"modifiable":false},"retention_period_unit":{"value":null,"modifiable":false}}');
  $security_profile = SecurityProfile::from_json($json);
  var_dump($security_profile);
  echo "\n-------------------\n";

  $contact_method = new ContactMethod('5145145144');
  var_dump($contact_method);
  echo "\n-------------------\n";

  $recipient = new Recipient('test@test.com', 'Jay', 'Lev', 'acme');
  $recipient->contact_methods = [$contact_method];
  var_dump($recipient);
  echo "\n-------------------\n";

  $attachment = Attachment::from_file_path('/home/jlevesque/test.txt', 'text/plain');
  var_dump($attachment);
  echo "\n-------------------\n";
  echo "\n-------------------\n";
  $safebox = new Safebox('jalbert.levesque@xmedius.com');
  $safebox->subject = 'subject';
  $safebox->message = 'message';
  $safebox->recipients = [$recipient];
  $safebox->attachments = [$attachment];
  $safebox->security_profile = $security_profile;
  $safebox->notification_language = 'en';
  var_dump($safebox);
  echo "\n-------------------\n";
  $client = new Client($token, 'acme');
  //var_dump($client);
  //var_dump($safebox);
  echo "\n-------------------\n";
  //var_dump($client->security_profiles('jalbert.levesque@xmedius.com'));
  var_dump($client->enterprise_settings());
  //var_dump($client->default_security_profile('jalbert.levesque@xmedius.com'));
  echo "\n-------------------\n";
  echo "\n--------SAFE-------\n";
  var_dump($client->submit_safebox($safebox));
?>





