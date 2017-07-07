<?php

namespace meteocontrol\client\vcomapi\tests\unit\systems;

use GuzzleHttp\Client;
use meteocontrol\client\vcomapi\ApiClient;
use meteocontrol\client\vcomapi\Config;
use meteocontrol\client\vcomapi\handlers\BasicAuthorizationHandler;
use meteocontrol\client\vcomapi\model\TechnicalData;

class TechnicalDataTest extends \PHPUnit_Framework_TestCase {

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

    public function testGetTechnicalData() {
        $json = file_get_contents(__DIR__ . '/responses/getTechnicalData.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/technical-data'))
            ->willReturn($json);

        /** @var TechnicalData $technicalData */
        $technicalData = $this->api->system('ABCDE')->technicalData()->get();

        $this->assertEquals(79.24, $technicalData->nominalPower);
        $this->assertEquals(549.11, $technicalData->siteArea);
        $this->assertEquals(1, count($technicalData->panels));
        $this->assertEquals('Suntech', $technicalData->panels[0]->vendor);
        $this->assertEquals('STP280-24/Vb', $technicalData->panels[0]->model);
        $this->assertEquals(283, $technicalData->panels[0]->count);
        $this->assertEquals(2, count($technicalData->inverters));
        $this->assertEquals('KACO new energy', $technicalData->inverters[0]->vendor);
        $this->assertEquals('Powador 8000xi', $technicalData->inverters[0]->model);
        $this->assertEquals(9, $technicalData->inverters[0]->count);
        $this->assertEquals('KACO new energy', $technicalData->inverters[1]->vendor);
        $this->assertEquals('Powador 3500xi', $technicalData->inverters[1]->model);
        $this->assertEquals(1, $technicalData->inverters[1]->count);
    }
}
