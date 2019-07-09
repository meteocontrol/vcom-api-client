<?php

namespace meteocontrol\client\vcomapi\tests\unit\handler;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use meteocontrol\client\vcomapi\Config;
use meteocontrol\client\vcomapi\handlers\OAuthAuthorizationHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class OAuthAuthorizationHandlerTest extends TestCase {

    /** @var Client|MockObject $mockedClient */
    private $mockedClient;

    /** @var Config */
    private $config;

    /** @var string */
    private $tokenAccessFile;

    public function setup() {
        $this->tokenAccessFile = __DIR__ . '/../../../../.tokenAccess';

        $this->config = new Config(__DIR__ . '/../_files/config.ini');

        $this->mockedClient = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['post'])
            ->getMock();
    }

    public function testGetAccessTokens() {
        if (file_exists($this->tokenAccessFile)) {
            unlink($this->tokenAccessFile);
        }

        $mockedJson = file_get_contents(__DIR__ . '/_files/expectedResponse.json');
        list($expectedAccessToken) = $this->parseResponse($mockedJson);

        $mockedResponse = $this->createMockedResponse($this->createMockedSteam($mockedJson));

        $this->mockedClient->expects($this->once())
            ->method('post')
            ->with(
                sprintf('%s/login', $this->config->getApiUrl()),
                [
                    'form_params' => [
                        'grant_type' => 'password',
                        'username' => $this->config->getApiUsername(),
                        'password' => $this->config->getApiPassword(),
                    ],
                ]
            )
            ->willReturn($mockedResponse);

        $handler = new OAuthAuthorizationHandler($this->config);
        $actualOptions = $handler->appendAuthorizationHeader($this->mockedClient, []);

        $this->assertEquals($expectedAccessToken, $actualOptions['headers']['Authorization']);
        $this->assertFileExists($this->tokenAccessFile);
    }

    /**
     * @depends testGetAccessTokens
     */
    public function testGetAccessWithCredentialsFile() {
        $mockedJson = file_get_contents(__DIR__ . '/_files/expectedResponse.json');
        list($expectedAccessToken) = $this->parseResponse($mockedJson);

        $this->mockedClient->expects($this->never())
            ->method('post');

        $handler = new OAuthAuthorizationHandler($this->config);
        $actualOptions = $handler->appendAuthorizationHeader($this->mockedClient, []);

        $this->assertEquals($expectedAccessToken, $actualOptions['headers']['Authorization']);
        $this->assertFileExists($this->tokenAccessFile);
    }

    public function testHandleUnauthorizedException() {
        unlink($this->tokenAccessFile);

        $mockedJson = file_get_contents(__DIR__ . '/_files/expectedResponse.json');
        $mockedJson2 = file_get_contents(__DIR__ . '/_files/expectedRefreshResponse.json');

        list(, $expectedRefreshToken) = $this->parseResponse($mockedJson);
        list($expectedAccessToken) = $this->parseResponse($mockedJson2);

        $mockedResponse = $this->createMockedResponse($this->createMockedSteam($mockedJson));
        $mockedResponse2 = $this->createMockedResponse($this->createMockedSteam($mockedJson2));

        $this->mockedClient->expects($this->exactly(2))
            ->method('post')
            ->withConsecutive(
                [
                    sprintf('%s/login', $this->config->getApiUrl()),
                    [
                        'form_params' => [
                            'grant_type' => 'password',
                            'username' => $this->config->getApiUsername(),
                            'password' => $this->config->getApiPassword(),
                        ],
                    ]
                ],
                [
                    sprintf('%s/login', $this->config->getApiUrl()),
                    [
                        'form_params' => [
                            'grant_type' => 'refresh_token',
                            'refresh_token' => $expectedRefreshToken,
                        ],
                    ]
                ]
            )
            ->willReturnOnConsecutiveCalls($mockedResponse, $mockedResponse2);

        $mockedException = $this->getMockBuilder(ClientException::class)->disableOriginalConstructor()->getMock();

        $handler = new OAuthAuthorizationHandler($this->config);

        $handler->appendAuthorizationHeader($this->mockedClient, []);
        $handler->handleUnauthorizedException($mockedException, $this->mockedClient);

        $actualOptions = $handler->appendAuthorizationHeader($this->mockedClient, []);
        $this->assertEquals($expectedAccessToken, $actualOptions['headers']['Authorization']);
        $this->assertFileExists($this->tokenAccessFile);
    }

    /**
     * @depends testHandleUnauthorizedException
     */
    public function testHandleUnauthorizedExceptionWithCredentialsFile() {
        $mockedJson = file_get_contents(__DIR__ . '/_files/expectedRefreshResponse.json');

        list($expectedAccessToken, $expectedRefreshToken) = $this->parseResponse($mockedJson);

        $mockedResponse = $this->createMockedResponse($this->createMockedSteam($mockedJson));

        $this->mockedClient->expects($this->once())
            ->method('post')
            ->with(
                sprintf('%s/login', $this->config->getApiUrl()),
                [
                    'form_params' => [
                        'grant_type' => 'refresh_token',
                        'refresh_token' => $expectedRefreshToken,
                    ],
                ]
            )
            ->willReturn($mockedResponse);

        $mockedException = $this->getMockBuilder(ClientException::class)->disableOriginalConstructor()->getMock();

        $handler = new OAuthAuthorizationHandler($this->config);

        $handler->appendAuthorizationHeader($this->mockedClient, []);
        $handler->handleUnauthorizedException($mockedException, $this->mockedClient);

        $actualOptions = $handler->appendAuthorizationHeader($this->mockedClient, []);
        $this->assertEquals($expectedAccessToken, $actualOptions['headers']['Authorization']);
        $this->assertFileExists($this->tokenAccessFile);
    }

    public function testDeleteCredentialsFileWhenException() {
        $mockedJson = file_get_contents(__DIR__ . '/_files/expectedRefreshResponse.json');

        list(, $expectedRefreshToken) = $this->parseResponse($mockedJson);

        $mockedException = $this->getMockBuilder(ClientException::class)->disableOriginalConstructor()->getMock();
        $mockedResponse = $this->getMockBuilder(Response::class)->disableOriginalConstructor()->getMock();

        $mockedException->expects($this->once())
            ->method('getResponse')
            ->willReturn($mockedResponse);

        $mockedResponse->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(221);

        $this->mockedClient->expects($this->once())
            ->method('post')
            ->with(
                sprintf('%s/login', $this->config->getApiUrl()),
                [
                    'form_params' => [
                        'grant_type' => 'refresh_token',
                        'refresh_token' => $expectedRefreshToken,
                    ],
                ]
            )
            ->willThrowException($mockedException);


        $handler = new OAuthAuthorizationHandler($this->config);

        $handler->appendAuthorizationHeader($this->mockedClient, []);
        try {
            $handler->handleUnauthorizedException($mockedException, $this->mockedClient);
        } catch (ClientException $ex) {
        }
        $this->assertFileNotExists($this->tokenAccessFile);
    }

    public function testWithTokenCallbackFunction() {
        if (file_exists($this->tokenAccessFile)) {
            unlink($this->tokenAccessFile);
        }

        $mockedJson = file_get_contents(__DIR__ . '/_files/expectedResponse.json');
        $mockedJson2 = file_get_contents(__DIR__ . '/_files/expectedRefreshResponse.json');

        list(, $expectedRefreshToken) = $this->parseResponse($mockedJson);
        list($expectedAccessToken) = $this->parseResponse($mockedJson2);

        $mockedResponse = $this->createMockedResponse($this->createMockedSteam($mockedJson2));

        $this->mockedClient->expects($this->once())
            ->method('post')
            ->with(
                sprintf('%s/login', $this->config->getApiUrl()),
                [
                    'form_params' => [
                        'grant_type' => 'refresh_token',
                        'refresh_token' => $expectedRefreshToken,
                    ],
                ]
            )
            ->willReturn($mockedResponse);

        $this->config->setTokenAccessCallable(function () use ($mockedJson) {
            return json_decode($mockedJson, true);
        });

        $this->config->setTokenRefreshCallable(function ($accessToken, $refreshToken) use ($mockedJson2) {
            $credentials = json_decode($mockedJson2, true);
            self::assertEquals($credentials['access_token'], $accessToken);
            self::assertEquals($credentials['refresh_token'], $refreshToken);
        });

        $mockedException = $this->getMockBuilder(ClientException::class)->disableOriginalConstructor()->getMock();

        $handler = new OAuthAuthorizationHandler($this->config);

        $handler->appendAuthorizationHeader($this->mockedClient, []);
        $handler->handleUnauthorizedException($mockedException, $this->mockedClient);

        $actualOptions = $handler->appendAuthorizationHeader($this->mockedClient, []);

        $this->assertEquals($expectedAccessToken, $actualOptions['headers']['Authorization']);
        $this->assertFileNotExists($this->tokenAccessFile);
    }

    private function createMockedSteam($mockedJson) {
        $mockedSteam = $this->getMockBuilder(Stream::class)->disableOriginalConstructor()->getMock();
        $mockedSteam->expects($this->once())->method('getContents')->willReturn($mockedJson);

        return $mockedSteam;
    }

    private function createMockedResponse($mockedSteam) {
        $mockedResponse = $this->getMockBuilder(Response::class)->disableOriginalConstructor()->getMock();
        $mockedResponse->expects($this->once())->method('getBody')->willReturn($mockedSteam);

        return $mockedResponse;
    }

    private function parseResponse(string $json) {
        $credentials = json_decode($json, true);
        return [
            sprintf('Bearer %s', $credentials['access_token']),
            $credentials['refresh_token'],
        ];
    }
}
