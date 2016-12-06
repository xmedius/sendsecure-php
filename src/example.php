<?php

  include 'sdk/sendsecure/client.php';


  // Get a token or use your token
  $token = Client::get_user_token('acme', 'device_id', 'systemtest');
  echo "\n-------------------\n";
  echo "\n" . $token . "\n";
  echo "\n-------------------\n";


  /*********************************************************************************************/
  //
  // Json Client Usage
  //
  /*********************************************************************************************/


  // Create a Json Client
  $json_client = new JsonClient($token, 'acme');
  echo "\n" . $json_client . "\n";
  echo "\n-------------------\n";

  // Get the Enterprise setting
  $json_enterprise_setting = $json_client->get_enterprise_settings();
  echo "\n" . $json_enterprise_setting . "\n";
  echo "\n-------------------\n";

  // Get the Security profiles
  $json_security_profiles= $json_client->get_security_profiles('test@test.com');
  echo "\n" . $json_security_profiles . "\n";
  echo "\n-------------------\n";

  // Create a Safebox
  $json_new_safebox = $json_client->new_safebox('test@test.com');
  echo "\n" . $json_new_safebox . "\n";
  echo "\n-------------------\n";

  // Upload a file
  $upload_file = $json_client->upload_file(json_decode($json_new_safebox)->upload_url,'/home/jlevesque/logo.png','image/png');
  echo "\n" . $upload_file . "\n";
  echo "\n-------------------\n";

  // Commit the safebox
  $payload = array('safebox' => array(
    'guid' => json_decode($json_new_safebox)->guid,
    'recipients' => array(array("email" => "test@test.com")),
    'subject' => "subject",
    'message' => "message",
    'security_profile_id' => 39,
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

  $commit_safebox = $json_client->commit_safebox($payload);
  echo "\n" . $commit_safebox . "\n";
  echo "\n-------------------\n";


  /*********************************************************************************************/
  //
  // Client Usage
  //
  /*********************************************************************************************/


  // Create a Client object
  $client = new Client($token, 'acme');
  var_dump($client);
  echo "\n-------------------\n";

  // Get the EnterpriseSettings object
  $enterprise_setting = $client->enterprise_settings();
  var_dump($enterprise_setting);
  echo "\n-------------------\n";

  // Get all the  SecurityProfile objects
  $security_profiles = $client->security_profiles('test@test.com');
  var_dump($security_profiles);
  echo "\n-------------------\n";

  // Get the default SecurityProfile object
  $security_profile = $client->default_security_profile('test@test.com');
  var_dump($security_profile);
  echo "\n-------------------\n";

  // Create a ContactMethod object
  $contact_method = new ContactMethod('514-514-5144');
  var_dump($contact_method);
  echo "\n-------------------\n";

  // Create a Recipient object
  $recipient = new Recipient('test@test.com', 'Test', 'Test', 'acme');
  $recipient->contact_methods = [$contact_method];
  var_dump($recipient);
  echo "\n-------------------\n";

  // Create an Attachment object
  $attachment = Attachment::from_file_path('/home/jlevesque/logo.png','image/png');
  var_dump($attachment);
  echo "\n-------------------\n";

  // Create a Safebox object
  $safebox = new Safebox('test@test.com');
  $safebox->subject = 'subject';
  $safebox->message = 'message';
  $safebox->recipients = [$recipient];
  $safebox->attachments = [$attachment];
  $safebox->security_profile = $security_profile;
  $safebox->notification_language = 'en';
  var_dump($safebox);
  echo "\n-------------------\n";

  // Initialize a Safebox object
  $client->initialize_safebox($safebox);
  var_dump($safebox);
  echo "\n-------------------\n";

  // Upload an attachment to a Safebox object
  $client->upload_attachment($safebox, $attachment);
  var_dump($safebox);
  echo "\n-------------------\n";

  // Send the Safebox object
  $safebox_response = $client->commit_safebox($safebox);
  var_dump($safebox_response);
  echo "\n-------------------\n";

?>





