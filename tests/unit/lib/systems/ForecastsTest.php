<?php

namespace meteocontrol\client\vcomapi\tests\unit\systems;

use GuzzleHttp\Client;
use meteocontrol\client\vcomapi\ApiClient;
use meteocontrol\client\vcomapi\Config;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\client\vcomapi\handlers\OAuthAuthorizationHandler;

class ForecastsTest extends \PHPUnit_Framework_TestCase {

    /** @var \PHPUnit_Framework_MockObject_MockObject | ApiClient */
    private $api;

    public function setup() {
        $config = new Config();
        $client = new Client();
        $authHandler = new OAuthAuthorizationHandler($config);
        $this->api = $this->getMockBuilder('\meteocontrol\client\vcomapi\ApiClient')
            ->setConstructorArgs([$client, $authHandler])
            ->setMethods(['run'])
            ->getMock();
    }

    public function testGetForecastsYield() {
        $json = file_get_contents(__DIR__ . '/responses/testGetForecastsYield.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/forecasts/yield/specific-energy'
                ),
                $this->identicalTo(
                    'from=2016-10-01T00%3A00%3A00%2B02%3A00&to=2016-12-31T23%3A59%3A59%2B01%3A00'
                )
            )
            ->willReturn($json);

        $measurementsCriteria = (new MeasurementsCriteria())
            ->withDateFrom(\DateTime::createFromFormat(\DateTime::ATOM, '2016-10-01T00:00:00+02:00'))
            ->withDateTo(\DateTime::createFromFormat(\DateTime::ATOM, '2016-12-31T23:59:59+01:00'));

        $yields = $this->api->system('ABCDE')
            ->forecasts()->forecastsYield()->specificEnergy()->get($measurementsCriteria);

        $this->assertEquals('2016-10-01T00:00:00+02:00', $yields[0]->timestamp->format(\DateTime::RFC3339));
        $this->assertEquals(59.759999999999998, $yields[0]->value);
        $this->assertEquals('2016-11-01T00:00:00+01:00', $yields[1]->timestamp->format(\DateTime::RFC3339));
        $this->assertEquals(33.709620000000001, $yields[1]->value);
        $this->assertEquals('2016-12-01T00:00:00+01:00', $yields[2]->timestamp->format(\DateTime::RFC3339));
        $this->assertEquals(24.437856, $yields[2]->value);
    }
}
