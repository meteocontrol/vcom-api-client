<?php

namespace meteocontrol\client\vcomapi\tests\unit;

use meteocontrol\client\vcomapi\Config;
use meteocontrol\client\vcomapi\Factory;

class FactoryTest extends \PHPUnit_Framework_TestCase {

    /** @var Factory */
    private $factory;

    public function setup() {
        $this->factory = new Factory();
    }

    public function testIsInstantiable() {
        $this->assertInstanceOf('meteocontrol\client\vcomapi\Factory', $this->factory);
    }

    public function testGetApiClient() {
        $config = new Config(__DIR__ . '/_files/config.ini');
        $apiClient = $this->factory->getApiClient($config);
        $this->assertInstanceOf('meteocontrol\client\vcomapi\ApiClient', $apiClient);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetApiClientWithWrongConfig() {
        $config = new Config(__DIR__);
        $this->factory->getApiClient($config);
    }

    public function testGetHttpClient() {
        $config = new Config(__DIR__ . '/_files/config.ini');
        $client = $this->factory->getHttpClient($config);
        $this->assertInstanceOf('GuzzleHttp\Client', $client);
    }
}
