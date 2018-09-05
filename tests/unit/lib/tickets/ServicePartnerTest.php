<?php

namespace meteocontrol\client\vcomapi\tests\unit\tickets;

use GuzzleHttp\Client;
use meteocontrol\client\vcomapi\ApiClient;
use meteocontrol\client\vcomapi\Config;
use meteocontrol\client\vcomapi\handlers\BasicAuthorizationHandler;

class ServicePartnerTest extends \PHPUnit_Framework_TestCase {

    /** @var \PHPUnit_Framework_MockObject_MockObject | ApiClient */
    private $api;

    public function setup() {
        $config = new Config();
        $client = new Client();
        $authHandler = new BasicAuthorizationHandler($config);
        $this->api = $this->getMockBuilder('\meteocontrol\client\vcomapi\ApiClient')
            ->setConstructorArgs([$client, $authHandler])
            ->setMethods(['run'])
            ->getMock();
    }

    public function testGetServicePartner() {
        $json = file_get_contents(__DIR__ . '/responses/getServicePartner.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('tickets/123/service-partner'))
            ->willReturn($json);

        $actual = $this->api->ticket(123)->servicePartner()->get();
        $this->assertEquals(123456, $actual->id);
    }
}
