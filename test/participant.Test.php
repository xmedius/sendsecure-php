<?php

use PHPUnit\Framework\TestCase;

class ParticipantTest extends TestCase {

  private $participant;

  protected function setUp() {
    $json = '{"id": "7a3c51e00a004917a8f5db807180fcc5",
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
            }';
    $this->participant = \SendSecure\Participant::from_json(json_decode($json));
  }

  public function test_participant_from_json() {
    $this->assertEquals($this->participant->get_id(), "7a3c51e00a004917a8f5db807180fcc5");
    $this->assertEmpty($this->participant->first_name);
    $this->assertEmpty($this->participant->last_name);
    $this->assertEquals($this->participant->email, "john.smith@example.com");
    $this->assertEquals($this->participant->get_type(), "guest");
    $this->assertEquals($this->participant->get_role(), "guest");

    $guest_options = $this->participant->guest_options;
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
  }

  public function test_participant_to_json() {
    $expected_json = '{"participant":{ "email":"john.smith@example.com",'.
                                      '"first_name":"",'.
                                      '"last_name":"",'.
                                      '"company_name":"",'.
                                      '"locked":false,'.
                                      '"contact_methods":['.
                                        '{"destination_type":"cell_phone",'.
                                         '"destination":"+15145550000"},'.
                                        '{"destination_type":"office_phone",'.
                                         '"destination":"+15145551111"}]}}';
    $this->assertJsonStringEqualsJsonString($this->participant->to_json(), $expected_json);
  }

}

?>