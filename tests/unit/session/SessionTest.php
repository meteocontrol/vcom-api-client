<?php

namespace meteocontrol\client\vcomapi\tests\unit\session;

use meteocontrol\client\vcomapi\tests\unit\TestCase;

class SessionTest extends TestCase {

    public function testGetSession() {
        $json = file_get_contents(__DIR__ . '/responses/GetSession.json');

        $this->api->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('session'))
            ->willReturn($json);

        $session = $this->api->session()->get();

        $this->assertEquals("123", $session->user->id);
        $this->assertEquals('Mr.', $session->user->title);
        $this->assertEquals('First Name', $session->user->firstName);
        $this->assertEquals('Last Name', $session->user->lastName);
        $this->assertEquals('user.name', $session->user->username);
        $this->assertEquals('fake@mail.intra', $session->user->email);
        $this->assertEquals('en', $session->user->language);
        $this->assertEquals('Company', $session->user->company);
        $this->assertEquals('0234567890', $session->user->fax);
        $this->assertEquals('0234567891', $session->user->telephone);
        $this->assertEquals('0987654321', $session->user->cellphone);
        $this->assertEquals('City', $session->user->address->city);
        $this->assertEquals('Country', $session->user->address->country);
        $this->assertEquals('12345', $session->user->address->postalCode);
        $this->assertEquals('Street 123', $session->user->address->street);
        $this->assertEquals('2F-1', $session->user->address->streetAddition);
        $this->assertEquals('Europe/Berlin', $session->user->timezone->name);
        $this->assertEquals('+02:00', $session->user->timezone->utcOffset);
        $this->assertEquals(true, $session->user->hasVcom);
    }
}
