<?php

namespace meteocontrol\client\vcomapi\tests\unit;

use meteocontrol\client\vcomapi\Config;

class ConfigTest extends \PHPUnit_Framework_TestCase {

    /** @var Config */
    private $config;

    public function setup() {
        $this->config = new Config(__DIR__ . '/_files/config.ini');
        $this->config->validate();
    }

    public function testIsInstantiable() {
        $this->assertInstanceOf('meteocontrol\client\vcomapi\Config', $this->config);
    }

    public function testGetProperties() {
        $this->assertEquals(
            'https://test.meteocontrol.api',
            $this->config->getApiUrl()
        );

        $this->assertEquals(
            'test-api-key',
            $this->config->getApiKey()
        );

        $this->assertEquals(
            'test-api-username',
            $this->config->getApiUsername()
        );

        $this->assertEquals(
            'test-api-password',
            $this->config->getApiPassword()
        );

        $this->assertEquals(
            'basic',
            $this->config->getApiAuthorizationMode()
        );
    }

    public function testCreateConfigManually() {
        $this->config = new Config();

        $this->config->setApiKey('test-api-key');
        $this->config->setApiUrl('https://test.meteocontrol.api');
        $this->config->setApiUsername('test-api-username');
        $this->config->setApiPassword('test-api-password');
        $this->config->setApiAuthorizationMode('basic');
        $this->config->validate();

        $this->assertEquals('test-api-key', $this->config->getApiKey());
        $this->assertEquals('https://test.meteocontrol.api', $this->config->getApiUrl());
        $this->assertEquals('test-api-username', $this->config->getApiUsername());
        $this->assertEquals('test-api-password', $this->config->getApiPassword());
        $this->assertEquals('basic', $this->config->getApiAuthorizationMode());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateWrongConfig() {
        $this->config = new Config(__DIR__);
        $this->config->validate();
    }
}
