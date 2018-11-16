<?php

include 'src/sdk/sendsecure/client.php';

$user_email = '';
$endpoint = '';
$username = '';
$password = '';
$enterprise = '';

//CALL TO GET USER TOKEN + USER ID
$token = \SendSecure\Client::get_user_token($enterprise, $username, $password, 'device_id', 'test', 'SendSecure PHP', $endpoint);
echo "\n-------------------\n";
echo "\n" . $token->{'token'} . "\n";
echo "\n" . $token->{'user_id'} . "\n";
echo "\n-------------------\n";

//CREATE A SENDSECURE CLIENT
$client = new \SendSecure\Client($token->{'token'} , $token->{'user_id'}, $enterprise, $endpoint);

//SUBMIT A SAFEBOX WITH 1 RECIPIENT and 1 ATTACHMENT
$safebox = new \SendSecure\Safebox($user_email);
$safebox->subject = 'Subject';
$safebox->message = 'Message';

$contact_method = new \SendSecure\ContactMethod('55555');
$guest_options = new \SendSecure\GuestOptions('Test');
$guest_options->add_contact_method($contact_method);
$participant = new \SendSecure\Participant('user@email.com', 'first_name', 'last_name', $guest_options);

$attachment = \SendSecure\Attachment::from_file_path('/path/to/file','application/pdf');
$safebox->participants = [$participant];
$safebox->attachments = [$attachment];

$client->submit_safebox($safebox);

var_dump($safebox);
echo "\n-------------------\n";

?>





