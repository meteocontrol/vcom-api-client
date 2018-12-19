<?php

namespace meteocontrol\client\vcomapi\tests\unit\handler;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use meteocontrol\client\vcomapi\Config;
use meteocontrol\client\vcomapi\handlers\OAuthAuthorizationHandler;
use PHPUnit_Framework_MockObject_MockObject;

class OAuthAuthorizationHandlerTest extends \PHPUnit_Framework_TestCase {

    public function testGetAccessTokens() {
        $config = new Config(__DIR__ . '/../_files/config.ini');
        $mockedJson = file_get_contents(__DIR__ . '/_files/expectedResponse.json');
        $expectedAccessToken = sprintf('Bearer %s', json_decode($mockedJson, true)['access_token']);

        $mockedSteam = $this->getMockBuilder(Stream::class)->disableOriginalConstructor()->getMock();
        $mockedSteam->expects($this->once())->method('getContents')->willReturn($mockedJson);

        $mockedResponse = $this->getMockBuilder(Response::class)->disableOriginalConstructor()->getMock();
        $mockedResponse->expects($this->once())->method('getBody')->willReturn($mockedSteam);

        /** @var Client|PHPUnit_Framework_MockObject_MockObject $mockedClient */
        $mockedClient = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['post'])
            ->getMock();
        $mockedClient->expects($this->once())
            ->method('post')
            ->with(
                sprintf('%s/login', $config->getApiUrl()),
                [
                    'form_params' => [
                        'grant_type' => 'password',
                        'username' => $config->getApiUsername(),
                        'password' => $config->getApiPassword()
                    ]
                ]
            )
            ->willReturn($mockedResponse);

        $handler = new OAuthAuthorizationHandler($config);
        $actualOptions = $handler->appendAuthorizationHeader($mockedClient, []);

        $this->assertEquals($actualOptions['headers']['Authorization'], $expectedAccessToken);
    }

    public function testHandleUnauthorizedException() {
        $config = new Config(__DIR__ . '/../_files/config.ini');
        $mockedJson = file_get_contents(__DIR__ . '/_files/expectedResponse.json');
        $expectedRefreshToken = json_decode($mockedJson, true)['refresh_token'];

        $mockedSteam = $this->getMockBuilder(Stream::class)->disableOriginalConstructor()->getMock();
        $mockedSteam->expects($this->exactly(2))->method('getContents')->willReturn($mockedJson);

        $mockedResponse = $this->getMockBuilder(Response::class)->disableOriginalConstructor()->getMock();
        $mockedResponse->expects($this->exactly(2))->method('getBody')->willReturn($mockedSteam);

        /** @var Client|PHPUnit_Framework_MockObject_MockObject $mockedClient */
        $mockedClient = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['post'])
            ->getMock();
        $mockedClient->expects($this->exactly(2))
            ->method('post')
            ->withConsecutive(
                [
                    sprintf('%s/login', $config->getApiUrl()),
                    [
                        'form_params' => [
                            'grant_type' => 'password',
                            'username' => $config->getApiUsername(),
                            'password' => $config->getApiPassword()
                        ]
                    ]
                ],
                [
                    sprintf('%s/login', $config->getApiUrl()),
                    [
                        'form_params' => [
                            'grant_type' => 'refresh_token',
                            'refresh_token' => $expectedRefreshToken
                        ]
                    ]
                ]
            )
            ->willReturnOnConsecutiveCalls($mockedResponse, $mockedResponse);

        $mockedException = $this->getMockBuilder(ClientException::class)->disableOriginalConstructor()->getMock();

        $handler = new OAuthAuthorizationHandler($config);

        $handler->appendAuthorizationHeader($mockedClient, []);
        $handler->handleUnauthorizedException($mockedException, $mockedClient);
    }
}
