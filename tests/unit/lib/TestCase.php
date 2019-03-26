<?php

namespace meteocontrol\client\vcomapi\tests\unit;

use GuzzleHttp\Client;
use meteocontrol\client\vcomapi\ApiClient;
use meteocontrol\client\vcomapi\Config;
use meteocontrol\client\vcomapi\handlers\OAuthAuthorizationHandler;

class TestCase extends \PHPUnit_Framework_TestCase {

    /** @var \PHPUnit_Framework_MockObject_MockObject | ApiClient */
    protected $api;

    public function setup() {
        $this->api = $this->getMockedApiClient();
    }

    /**
     * @return ApiClient|\PHPUnit_Framework_MockObject_MockObject
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
