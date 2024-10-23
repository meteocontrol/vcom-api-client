<?php

namespace meteocontrol\client\vcomapi\tests\unit\systems;

use GuzzleHttp\RequestOptions;
use meteocontrol\client\vcomapi\filters\UserCriteria;
use meteocontrol\client\vcomapi\model\User;
use meteocontrol\client\vcomapi\model\UserDetail;
use meteocontrol\client\vcomapi\tests\unit\TestCase;

class UsersTest extends TestCase {

    public function testGetUsers() {
        $json = file_get_contents(__DIR__ . '/responses/getUsers.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('systems/ABCDE/users'))
            ->willReturn($json);
        /** @var User[] $users */
        $users = $this->api->system('ABCDE')->users()->get();
        $this->assertCount(2, $users);
        $this->assertEquals("123", $users[0]->id);
        $this->assertEquals('test', $users[0]->username);
        $this->assertEquals('vcom-api', $users[0]->firstName);
        $this->assertEquals('e2e test user', $users[0]->lastName);

        $this->assertEquals("1234", $users[1]->id);
        $this->assertEquals('test2', $users[1]->username);
        $this->assertEquals('First Name', $users[1]->firstName);
        $this->assertEquals('Last Name', $users[1]->lastName);
    }

    public function testGetSingleUserById() {
        $json = file_get_contents(__DIR__ . '/responses/getSingleUser.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('systems/ABCDE/users/91366'))
            ->willReturn($json);
        /** @var UserDetail $userDetail */
        $userDetail = $this->api->system('ABCDE')->user("91366")->get();

        $this->assertEquals("123", $userDetail->id);
        $this->assertEquals("Mr.", $userDetail->title);
        $this->assertEquals('vcom-api', $userDetail->firstName);
        $this->assertEquals('e2e test user', $userDetail->lastName);
        $this->assertEquals('vcom-api-e2e-test-user', $userDetail->username);
        $this->assertEquals('invalid@intranet.ba', $userDetail->email);
        $this->assertEquals('de', $userDetail->language);
        $this->assertEquals('meteocontrol', $userDetail->company);
        $this->assertEquals('123456', $userDetail->fax);
        $this->assertEquals('123456', $userDetail->telephone);
        $this->assertEquals('654321', $userDetail->cellphone);
        $this->assertEquals('Berlin', $userDetail->address->city);
        $this->assertEquals('Germany', $userDetail->address->country);
        $this->assertEquals('AAA', $userDetail->address->street);
        $this->assertEquals('BBB', $userDetail->address->streetAddition);
        $this->assertEquals('123', $userDetail->address->postalCode);
        $this->assertEquals('Europe/Berlin', $userDetail->timezone->name);
        $this->assertEquals('+02:00', $userDetail->timezone->utcOffset);
    }

    public function testGetSingleUserByName() {
        $json = file_get_contents(__DIR__ . '/responses/getSingleUser.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo('systems/ABCDE/users'),
                $this->identicalToUrl([
                    RequestOptions::QUERY => 'username=vcom-api-e2e-test-user'
                ])
            )
            ->willReturn($json);
        $userCriteria = new UserCriteria();
        $userCriteria->withUsername("vcom-api-e2e-test-user");
        /** @var UserDetail $userDetail */
        $userDetail = $this->api->system('ABCDE')->users()->get($userCriteria);
        $this->assertEquals("123", $userDetail->id);
        $this->assertEquals("Mr.", $userDetail->title);
        $this->assertEquals('vcom-api', $userDetail->firstName);
        $this->assertEquals('e2e test user', $userDetail->lastName);
        $this->assertEquals('vcom-api-e2e-test-user', $userDetail->username);
        $this->assertEquals('invalid@intranet.ba', $userDetail->email);
        $this->assertEquals('de', $userDetail->language);
        $this->assertEquals('meteocontrol', $userDetail->company);
        $this->assertEquals('123456', $userDetail->fax);
        $this->assertEquals('123456', $userDetail->telephone);
        $this->assertEquals('654321', $userDetail->cellphone);
        $this->assertEquals('Berlin', $userDetail->address->city);
        $this->assertEquals('Germany', $userDetail->address->country);
        $this->assertEquals('AAA', $userDetail->address->street);
        $this->assertEquals('BBB', $userDetail->address->streetAddition);
        $this->assertEquals('123', $userDetail->address->postalCode);
        $this->assertEquals('Europe/Berlin', $userDetail->timezone->name);
        $this->assertEquals('+02:00', $userDetail->timezone->utcOffset);
    }
}
