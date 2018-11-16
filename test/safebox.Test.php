<?php

use PHPUnit\Framework\TestCase;

class SafeboxTest extends TestCase {

  private $safebox;

  protected function setUp() {
    $json = '{ "guid": "b4d898ada15f42f293e31905c514607f",
              "user_id": 4,
              "enterprise_id": 4,
              "subject": "Donec rutrum congue leo eget malesuada. ",
              "notification_language": "de",
              "status": "in_progress",
              "security_profile_name": "All Contact Method Allowed!",
              "unread_count": 0,
              "double_encryption_status": "disabled",
              "audit_record_pdf": null,
              "secure_link": null,
              "secure_link_title": null,
              "email_notification_enabled": true,
              "created_at": "2017-05-24T14:45:35.062Z",
              "updated_at": "2017-05-24T14:45:35.589Z",
              "assigned_at": "2017-05-24T14:45:35.040Z",
              "latest_activity": "2017-05-24T14:45:35.544Z",
              "expiration": "2017-05-31T14:45:35.038Z",
              "closed_at": null,
              "content_deleted_at": null,
              "security_options": {
                "security_code_length": 4,
                "allowed_login_attempts": 3,
                "allow_remember_me": true,
                "allow_sms": true,
                "allow_voice": true,
                "allow_email": false,
                "reply_enabled": true,
                "group_replies": false,
                "code_time_limit": 5,
                "encrypt_message": true,
                "two_factor_required": true,
                "auto_extend_value": 3,
                "auto_extend_unit": "days",
                "retention_period_type": "do_not_discard",
                "retention_period_value": null,
                "retention_period_unit": "hours",
                "allow_manual_delete": true,
                "allow_manual_close": false
              },
              "participants": [{
                  "id": "7a3c51e00a004917a8f5db807180fcc5",
                  "first_name": "",
                  "last_name": "",
                  "email": "john.smith@example.com",
                  "type": "guest",
                  "role": "guest",
                  "guest_options": {
                    "company_name": "",
                    "locked": false,
                    "bounced_email": false,
                    "failed_login_attempts": 0,
                    "verified": false,
                    "contact_methods": [{
                        "id": 35016,
                        "destination": "+15145550000",
                        "destination_type": "cell_phone",
                        "verified": false,
                        "created_at": "2017-05-24T14:45:35.453Z",
                        "updated_at": "2017-05-24T14:45:35.453Z"
                      },
                      {
                        "id": 35017,
                        "destination": "+15145551111",
                        "destination_type": "office_phone",
                        "verified": false,
                        "created_at": "2017-05-24T14:45:35.537Z",
                        "updated_at": "2017-05-24T14:45:35.537Z"
                      }
                    ]
                  }
                },
                {
                  "id": 34208,
                  "first_name": "Jane",
                  "last_name": "Doe",
                  "email": "jane.doe@example.com",
                  "type": "user",
                  "role": "owner"
                }
              ],
              "messages": [{
                "note": "Lorem Ipsum...",
                "note_size": 148,
                "read": true,
                "author_id": "3",
                "author_type": "guest",
                "created_at": "2017-04-05T14:49:35.198Z",
                "documents": [{
                  "id": "5a3df276aaa24e43af5aca9b2204a535",
                  "name": "Axient-soapui-project.xml",
                  "sha": "724ae04430315c60ca17f4dbee775a37f5b18c91fde6eef24f77a605aee99c9c",
                  "size": 129961,
                  "url": "https://sendsecure.integration.xmedius.com/api/v2/safeboxes/b4d898ada15f42f293e31905c514607f/documents/5a3df276aaa24e43af5aca9b2204a535/url"
                }]
              }],
              "download_activity": {
                "guests": [{
                  "id": "42220c777c30486e80cd3bbfa7f8e82f",
                  "documents": [{
                    "id": "5a3df276aaa24e43af5aca9b2204a535",
                    "downloaded_bytes": 0,
                    "download_date": null
                  }]
                }],
                "owner": {
                  "id": 72,
                  "documents": []
                }
              },
              "event_history": [{
                "type": "safebox_created_owner",
                "date": "2017-03-30T18:09:05.966Z",
                "metadata": {
                  "emails": [
                    "john44@example.com"
                  ],
                  "attachment_count": 0
                },
                "message": "SafeBox créée par laurence4815@gmail.com avec 0 pièce(s) jointe(s) depuis 192.168.0.1 pour john44@example.com"
              }]
            }';
    $this->safebox = \SendSecure\Safebox::from_json(json_decode($json));
  }

  public function test_basic_attributes() {
    $this->assertEquals($this->safebox->guid, "b4d898ada15f42f293e31905c514607f");
    $this->assertEquals($this->safebox->get_user_id(), 4);
    $this->assertEquals($this->safebox->get_enterprise_id(), 4);
    $this->assertEquals($this->safebox->subject, "Donec rutrum congue leo eget malesuada. ");
    $this->assertEquals($this->safebox->notification_language, "de");
    $this->assertEquals($this->safebox->get_status(), "in_progress");
    $this->assertEquals($this->safebox->get_security_profile_name(), "All Contact Method Allowed!");
    $this->assertEquals($this->safebox->get_unread_count(), 0);
    $this->assertEquals($this->safebox->get_double_encryption_status(), "disabled");
    $this->assertNull($this->safebox->get_audit_record_pdf());
    $this->assertNull($this->safebox->get_secure_link());
    $this->assertNull($this->safebox->get_secure_link_title());
    $this->assertTrue($this->safebox->email_notification_enabled);
    $this->assertEquals($this->safebox->get_created_at(), "2017-05-24T14:45:35.062Z");
    $this->assertEquals($this->safebox->get_updated_at(), "2017-05-24T14:45:35.589Z");
    $this->assertEquals($this->safebox->get_assigned_at(), "2017-05-24T14:45:35.040Z");
    $this->assertEquals($this->safebox->get_latest_activity(), "2017-05-24T14:45:35.544Z");
    $this->assertEquals($this->safebox->expiration, "2017-05-31T14:45:35.038Z");
    $this->assertNull($this->safebox->get_closed_at());
    $this->assertNull($this->safebox->get_content_deleted_at());
  }

  public function test_security_options_attributes() {
    $security_options = $this->safebox->security_options;
    $this->assertEquals($security_options->get_security_code_length(), 4);
    $this->assertEquals($security_options->get_allowed_login_attempts(), 3);
    $this->assertTrue($security_options->get_allow_remember_me());
    $this->assertTrue($security_options->get_allow_sms());
    $this->assertTrue($security_options->get_allow_voice());
    $this->assertFalse($security_options->get_allow_email());
    $this->assertTrue($security_options->reply_enabled);
    $this->assertFalse($security_options->group_replies);
    $this->assertEquals($security_options->get_code_time_limit(), 5);
    $this->assertTrue($security_options->encrypt_message);
    $this->assertTrue($security_options->get_two_factor_required());
    $this->assertEquals($security_options->get_auto_extend_value(), 3);
    $this->assertEquals($security_options->get_auto_extend_unit(), "days");
    $this->assertEquals($security_options->retention_period_type, "do_not_discard");
    $this->assertNull($security_options->retention_period_value);
    $this->assertEquals($security_options->retention_period_unit, "hours");
    $this->assertTrue($security_options->get_allow_manual_delete());
    $this->assertFalse($security_options->get_allow_manual_close());
  }

  public function test_safebox_participants() {
    $this->assertCount(2, $this->safebox->participants);

    $participant = $this->safebox->participants[0];
    $this->assertInstanceOf(\SendSecure\Participant::class, $participant);
    $this->assertEquals($participant->get_id(), "7a3c51e00a004917a8f5db807180fcc5");
    $this->assertEmpty($participant->first_name);
    $this->assertEmpty($participant->last_name);
    $this->assertEquals($participant->email, "john.smith@example.com");
    $this->assertEquals($participant->get_type(), "guest");
    $this->assertEquals($participant->get_role(), "guest");

    $guest_options = $participant->guest_options;
    $this->assertInstanceOf(\SendSecure\GuestOptions::class, $guest_options);
    $this->assertEmpty($guest_options->company_name);
    $this->assertFalse($guest_options->locked);
    $this->assertFalse($guest_options->get_bounced_email());
    $this->assertEquals($guest_options->get_failed_login_attempts(), 0);
    $this->assertFalse($guest_options->get_verified());
    $this->assertCount(2, $guest_options->contact_methods);

    $contact_method = $guest_options->contact_methods[0];
    $this->assertInstanceOf(\SendSecure\ContactMethod::class, $contact_method);
    $this->assertEquals($contact_method->get_id(), 35016);
    $this->assertEquals($contact_method->destination, "+15145550000");
    $this->assertEquals($contact_method->destination_type, "cell_phone");
    $this->assertFalse($contact_method->get_verified());
    $this->assertEquals($contact_method->get_created_at(), "2017-05-24T14:45:35.453Z");
    $this->assertEquals($contact_method->get_updated_at(), "2017-05-24T14:45:35.453Z");

    $contact_method = $guest_options->contact_methods[1];
    $this->assertInstanceOf(\SendSecure\ContactMethod::class, $contact_method);
    $this->assertEquals($contact_method->get_id(), 35017);
    $this->assertEquals($contact_method->destination, "+15145551111");
    $this->assertEquals($contact_method->destination_type, "office_phone");
    $this->assertFalse($contact_method->get_verified());
    $this->assertEquals($contact_method->get_created_at(), "2017-05-24T14:45:35.537Z");
    $this->assertEquals($contact_method->get_updated_at(), "2017-05-24T14:45:35.537Z");

    $participant = $this->safebox->participants[1];
    $this->assertInstanceOf(\SendSecure\Participant::class, $participant);
    $this->assertEquals($participant->get_id(), 34208);
    $this->assertEquals($participant->first_name, "Jane");
    $this->assertEquals($participant->last_name, "Doe");
    $this->assertEquals($participant->email, "jane.doe@example.com");
    $this->assertEquals($participant->get_type(), "user");
    $this->assertEquals($participant->get_role(), "owner");
    $this->assertNull($participant->guest_options);
  }

  public function test_safebox_messages() {
    $this->assertCount(1, $this->safebox->get_messages());
    $message = $this->safebox->get_messages()[0];
    $this->assertInstanceOf(\SendSecure\Message::class, $message);
    $this->assertEquals($message->get_note(), "Lorem Ipsum...");
    $this->assertEquals($message->get_note_size(), 148);
    $this->assertTrue($message->get_read());
    $this->assertEquals($message->get_author_id(), 3);
    $this->assertEquals($message->get_author_type(), "guest");
    $this->assertEquals($message->get_created_at(), "2017-04-05T14:49:35.198Z");
    $this->assertCount(1, $message->get_documents());

    $document = $message->get_documents()[0];
    $this->assertInstanceOf(\SendSecure\MessageDocument::class, $document);
    $this->assertEquals($document->get_id(), "5a3df276aaa24e43af5aca9b2204a535");
    $this->assertEquals($document->get_name(), "Axient-soapui-project.xml");
    $this->assertEquals($document->get_sha(), "724ae04430315c60ca17f4dbee775a37f5b18c91fde6eef24f77a605aee99c9c");
    $this->assertEquals($document->get_size(), 129961);
    $this->assertEquals($document->get_url(), "https://sendsecure.integration.xmedius.com/api/v2/safeboxes/b4d898ada15f42f293e31905c514607f/documents/5a3df276aaa24e43af5aca9b2204a535/url");
  }

  public function test_safebox_download_activity() {
    $download_activity = $this->safebox->get_download_activity();

    $guests = $download_activity->get_guests();
    $this->assertCount(1, $guests);

    $guest = $guests[0];
    $this->assertInstanceOf(\SendSecure\DownloadActivityDetail::class, $guest);
    $this->assertEquals($guest->get_id(), "42220c777c30486e80cd3bbfa7f8e82f");

    $this->assertCount(1, $guest->get_documents());
    $document = $guest->get_documents()[0];
    $this->assertInstanceOf(\SendSecure\DownloadActivityDocument::class, $document);
    $this->assertEquals($document->get_id(), "5a3df276aaa24e43af5aca9b2204a535");
    $this->assertEquals($document->get_downloaded_bytes(), 0);
    $this->assertNull($document->get_download_date());

    $owner = $download_activity->get_owner();
    $this->assertInstanceOf(\SendSecure\DownloadActivityDetail::class, $owner);
    $this->assertEquals($owner->get_id(), 72);
    $this->assertEmpty($owner->get_documents());
  }

  public function test_safebox_event_history() {
    $event_history = $this->safebox->get_event_history();
    $this->assertCount(1, $event_history);

    $event_history = $this->safebox->get_event_history()[0];
    $this->assertInstanceOf(\SendSecure\EventHistory::class, $event_history);
    $this->assertEquals($event_history->get_type(), "safebox_created_owner");
    $this->assertEquals($event_history->get_date(), "2017-03-30T18:09:05.966Z");
    $this->assertEquals($event_history->get_message(), "SafeBox créée par laurence4815@gmail.com avec 0 pièce(s) jointe(s) depuis 192.168.0.1 pour john44@example.com");
    $this->assertCount(1, $event_history->get_metadata()->get_emails());
    $this->assertEquals($event_history->get_metadata()->get_emails(), ["john44@example.com"]);
    $this->assertEquals($event_history->get_metadata()->get_attachment_count(), 0);
  }

  public function test_safebox_to_json() {
    $expected_json = '{"safebox":{ "guid":"b4d898ada15f42f293e31905c514607f",' .
                          '"subject":"Donec rutrum congue leo eget malesuada. ",'.
                          '"notification_language":"de",'.
                          '"email_notification_enabled":true,'.
                          '"reply_enabled":true,'.
                          '"group_replies":false,'.
                          '"retention_period_type":"do_not_discard",'.
                          '"retention_period_unit":"hours",'.
                          '"encrypt_message":true,'.
                          '"recipients":['.
                            '{"email":"john.smith@example.com",'.
                            '"first_name":"",'.
                            '"last_name":"",'.
                            '"company_name":"",'.
                            '"locked":false,'.
                            '"contact_methods":['.
                              '{"destination_type":"cell_phone",'.
                              '"destination":"+15145550000"},'.
                              '{"destination_type":"office_phone",'.
                              '"destination":"+15145551111"}]},'.
                            '{"email":"jane.doe@example.com",'.
                            '"first_name":"Jane",'.
                            '"last_name":"Doe"}],'.
                          '"document_ids":[]}}';
    $this->assertJsonStringEqualsJsonString($this->safebox->to_json(), $expected_json);
  }

}

?>