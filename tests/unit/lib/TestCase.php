<?php

namespace meteocontrol\client\vcomapi\tests\unit;

use GuzzleHttp\Client;
use meteocontrol\client\vcomapi\ApiClient;
use meteocontrol\client\vcomapi\Config;
use meteocontrol\client\vcomapi\handlers\OAuthAuthorizationHandler;
use PHPUnit\Framework\MockObject\MockObject;

class TestCase extends \PHPUnit\Framework\TestCase {

    /** @var MockObject | ApiClient */
    protected $api;

    public function setup(): void {
        $this->api = $this->getMockedApiClient();
    }

    /**
     * @return ApiClient|MockObject
     */
    private function getMockedApiClient(): ApiClient {
        $config = new Config();
        $client = new Client();
        $authHandler = new OAuthAuthorizationHandler($config);
        return $this->getMockBuilder('\meteocontrol\client\vcomapi\ApiClient')
            ->setConstructorArgs([$client, $authHandler])
            ->setMethods(['run'])
            ->getMock();
    }
}
