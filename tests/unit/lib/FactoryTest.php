<?php

namespace meteocontrol\client\vcomapi\tests\unit;

use InvalidArgumentException;
use meteocontrol\client\vcomapi\Config;
use meteocontrol\client\vcomapi\Factory;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase {

    /** @var Factory */
    private $factory;

    public function setup(): void {
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

    public function testGetApiClientWithWrongConfig() {
        $config = new Config(__DIR__);

        $this->expectException(InvalidArgumentException::class);

        $this->factory->getApiClient($config);
    }

    public function testGetHttpClient() {
        $config = new Config(__DIR__ . '/_files/config.ini');
        $client = $this->factory->getHttpClient($config);
        $this->assertInstanceOf('GuzzleHttp\Client', $client);
    }
}
