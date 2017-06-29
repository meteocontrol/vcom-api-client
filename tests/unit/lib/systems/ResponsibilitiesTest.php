<?php

namespace meteocontrol\client\vcomapi\tests\unit\systems;

use GuzzleHttp\Client;
use meteocontrol\client\vcomapi\ApiClient;
use meteocontrol\client\vcomapi\Config;
use meteocontrol\client\vcomapi\model\ExtendedAddress;
use meteocontrol\client\vcomapi\model\Responsibilities;
use meteocontrol\client\vcomapi\model\Timezone;
use meteocontrol\client\vcomapi\model\UserDetail;

class ResponsibilitiesTest extends \PHPUnit_Framework_TestCase {

    /** @var \PHPUnit_Framework_MockObject_MockObject | ApiClient */
    private $api;

    public function setup() {
        $config = new Config();
        $client = new Client();
        $this->api = $this->getMockBuilder('\meteocontrol\client\vcomapi\ApiClient')
            ->setConstructorArgs([$config, $client])
            ->setMethods(['run'])
            ->getMock();
    }

    public function testGetResponsibilities() {
        $json = file_get_contents(__DIR__ . '/responses/getResponsibilities.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/responsibilities'))
            ->willReturn($json);

        $responsibilities = $this->api->system('ABCDE')->responsibilities()->get();
        $expectedUser = $this->getExpectedUser();
        $this->assertInstanceOf(Responsibilities::class, $responsibilities);
        $this->assertEquals($expectedUser, $responsibilities->owner);
        $this->assertEquals($expectedUser, $responsibilities->operator);
        $this->assertEquals($expectedUser, $responsibilities->electrician);
        $this->assertEquals($expectedUser, $responsibilities->invoiceRecipient);
        $this->assertEquals($expectedUser, $responsibilities->alarmContact);
    }

    /**
     * @return UserDetail
     */
    private function getExpectedUser() {
        $address = new ExtendedAddress();
        $address->city = 'City';
        $address->country = 'Country';
        $address->postalCode = '12345';
        $address->street = 'Street 123';
        $address->streetAddition = '2F-1';

        $timezone = new Timezone();
        $timezone->name = 'Europe/Berlin';
        $timezone->utcOffset = '+02:00';

        $user = new UserDetail();
        $user->id = "123";
        $user->title = 'Mr.';
        $user->firstName = 'First Name';
        $user->lastName = 'Last Name';
        $user->username = 'user.name';
        $user->email = 'fake@mail.intra';
        $user->language = 'en';
        $user->company = 'Company';
        $user->fax = '0234567890';
        $user->telephone = '0234567891';
        $user->cellphone = '0987654321';
        $user->address = $address;
        $user->timezone = $timezone;
        return $user;
    }
}
