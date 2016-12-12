**XMediusSENDSECURE (SendSecure)** is a collaborative file exchange platform that is both highly secure and simple to use.
It is expressly designed to allow for the secured exchange of sensitive documents via virtual SafeBoxes.

SendSecure comes with a **Web API**, which is **RESTful**, uses **HTTPs** and returns **JSON**.

Specific libraries have been published for various languages:
[C#](https://github.com/xmedius/sendsecure-csharp),
[Java](https://github.com/xmedius/sendsecure-java),
[JavaScript](https://github.com/xmedius/sendsecure-js),
**PHP**,
[Python](https://github.com/xmedius/sendsecure-python)
and
[Ruby](https://github.com/xmedius/sendsecure-ruby).

# sendsecure-php

**This library allows you to use the SendSecure Web API via PHP.**

With this library, you will be able to:
* Authenticate SendSecure users
* Create new SafeBoxes

# Table of Contents

* [Installation](#installation)
* [Quick Start](#quickstart)
* [Usage](#usage)
* [License](#license)
* [Credits](#credits)

<a name="installation"></a>
# Installation

## Prerequisites

- PHP version 5.6 or 7.0
- The SendSecure service, provided by [XMedius](https://www.xmedius.com/en/products?source=sendsecure-php) (demo accounts available on demand)

## Install Package

```php
TBD
```

<a name="quickstart"></a>
# Quick Start

## Authentication (Retrieving API Token)

Authentication is done using an API Token, which must be first obtained based on SendSecure enterprise account and user credentials.
Here is the minimum code to get such a user-based API Token.

```php
<?php

  include 'sdk/sendsecure/client.php';

  $token = Client::get_user_token('acme');
  echo "\n" . $token . "\n";

?>

```


## SafeBox Creation

Here is the minimum required code to create a SafeBox – with 1 recipient, a subject, a message and 1 attachment.
This example uses the user's *default* security profile (which requires to be set in the account).

### With SafeBox Helper Class

```php
<?php

  include 'sdk/sendsecure/client.php';

  $user_email = 'darthvader@empire.com';
  $token = 'USER|1d495165-4953-4457-8b5b-4fcf801e621a';
  $enterprise_account = 'deathstar';

  // Create a Client object
  $client = new Client($token, $enterprise_account);

  // Get the default SecurityProfile object
  $security_profile = $client->default_security_profile($user_email);

  // Create a ContactMethod object
  $contact_method = new ContactMethod('555-123-4567');

  // Create a Recipient object
  $recipient = new Recipient('lukeskywalker@rebels.com', 'Test', 'Test', 'acme');
  $recipient->contact_methods = [$contact_method];

  // Create an Attachment object
  $attachment = Attachment::from_file_path('/home/test/test.txt', 'text/plain');

  // Create a Safebox object
  $safebox = new Safebox($user_email);
  $safebox->subject = 'Family matters';
  $safebox->message = 'Son, you will find attached the evidence.';
  $safebox->recipients = [$recipient];
  $safebox->attachments = [$attachment];
  $safebox->security_profile = $security_profile;
  $safebox->notification_language = 'en';

  // Submit a Safebox object
  $safebox_response = $client->submit_safebox($safebox);
  var_dump($safebox_response);

?>

```

<!-- ### Without SafeBox Helper Class

```php
TBD
```
 -->
<a name="usage"></a>
# Usage

## Helper Methods

### Get User Token
```
get_user_token($enterprise_account, $username, $password, $device_id, $device_name, $application_type, $endpoint, $one_time_password)
```
Creates and returns an API Token for a specific user within a SendSecure enterprise account.
Calling this method again with the exact same params will always return the same Token.

Param               | Type   | Definition
--------------------|--------|-----------
$enterprise_account | String | The SendSecure enterprise account
$username           | String | The username of a SendSecure user of the current enterprise account
$password           | String | The password of this user
$device_id          | String | The unique ID of the device used to get the Token 
$device_name        | String | The name of the device used to get the Token
$application_type   | String | The type/name of the application used to get the Token ("SendSecure PHP" will be used by default if empty)
$endpoint           | String | The URL to the SendSecure service ("https://portal.xmedius.com" will be used by default if empty)
$one_time_password  | String | The one-time password of this user (if any)

### Client Object Constructor
```
__construct($api_token, $enterprise_account, $endpoint, $locale)
```

Param               | Type   | Definition
--------------------|--------|-----------
$api_token          | String | The API Token to be used for authentication with the SendSecure service
$enterprise_account | String | The SendSecure enterprise account
$endpoint           | String | The URL to the SendSecure service ("https://portal.xmedius.com" will be used by default if empty)
$locale             | String | The locale in which the server errors will be returned ("en" will be used by default if empty)

### Get Enterprise Settings
```
enterprise_settings()
```
Returns all values/properties of the enterprise account's settings specific to SendSecure.

### Get Default Security Profile
```
default_security_profile($user_email)
```
Returns the default security profile (if it has been set) for a specific user, with all its setting values/properties.

Param       | Type   | Definition
------------|--------|-----------
$user_email | String | The email address of a SendSecure user of the current enterprise account

### Get Security Profiles
```
security_profiles($user_email)
```
Returns the list of all security profiles available to a specific user, with all their setting values/properties.

Param       | Type   | Definition
------------|--------|-----------
$user_email | String | The email address of a SendSecure user of the current enterprise account

### Initialize SafeBox
```
initialize_safebox($safebox)
```
Pre-creates a SafeBox on the SendSecure system and returns the updated Safebox object with the necessary system parameters filled out (GUID, public encryption key, upload URL).

Param      | Type    | Definition
-----------|---------|-----------
$safebox   | Safebox | A Safebox object to be initialized by the SendSecure system

### Upload Attachment
```
upload_attachment($safebox, $attachment)
```
Uploads the specified file as an Attachment of the specified SafeBox and returns the updated Attachment object with the GUID parameter filled out.

Param       | Type       | Definition
------------|------------|-----------
$safebox    | Safebox    | An initialized Safebox object
$attachment | Attachment | An Attachment object - the file to upload to the SendSecure system

### Commit SafeBox
```
commit_safebox($safebox)
```
Finalizes the creation (commit) of the SafeBox on the SendSecure system.
This actually "Sends" the SafeBox with all content and contact info previously specified.

Param      | Type    | Definition
-----------|---------|-----------
$safebox   | Safebox | A Safebox object already initialized, with security profile, recipient(s), subject and message already defined, and attachments already uploaded. 

### Submit SafeBox
```
submit_safebox($safebox)
```
This method is a high-level combo that initializes the SafeBox, uploads all attachments and commits the SafeBox.

Param      | Type    | Definition
-----------|---------|-----------
$safebox   | Safebox | A non-initialized Safebox object with security profile, recipient(s), subject, message and attachments (not yet uploaded) already defined. 


## Helper Modules

### Safebox

### SafeboxResponse

### Attachment

### Recipient

### ContactMethod

### SecurityProfile

### EnterpriseSettings

### ExtensionFilter

<a name="license"></a>
# License

sendsecure-php is distributed under [MIT License](https://github.com/xmedius/sendsecure-php/blob/master/LICENSE).

<a name="credits"></a>
# Credits

sendsecure-php is developed, maintained and supported by [XMedius Solutions Inc.](https://www.xmedius.com?source=sendsecure-php)
The names and logos for sendsecure-php are trademarks of XMedius Solutions Inc.

![XMedius Logo](https://s3.amazonaws.com/xmc-public/images/xmedius-site-logo.png)
