<?php

namespace meteocontrol\client\vcomapi\tests\unit;

use GuzzleHttp\Client;
use meteocontrol\client\vcomapi\ApiClient;
use meteocontrol\client\vcomapi\Config;
use meteocontrol\client\vcomapi\handlers\OAuthAuthorizationHandler;
use PHPUnit\Framework\Constraint\IsIdentical;
use PHPUnit\Framework\MockObject\MockObject;

class TestCase extends \PHPUnit\Framework\TestCase {

    /** @var MockObject | ApiClient */
    protected $api;

    public function setup(): void {
        $this->api = $this->getMockedApiClient();
    }

    protected function identicalToUrl($url): IsIdentical {
        $decode = ["+", ",", "/", ":", "[", "]"];
        $encode = ["%2B", "%2C", "%2F", "%3A", "%5B", "%5D"];

        return $this->identicalTo(str_replace($decode, $encode, $url));
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
            ->onlyMethods(['get', 'put', 'post', 'patch', 'delete'])
            ->getMock();
    }
}
