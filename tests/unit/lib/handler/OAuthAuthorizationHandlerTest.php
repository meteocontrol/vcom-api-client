<?php

namespace meteocontrol\client\vcomapi\tests\unit\handler;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use meteocontrol\client\vcomapi\Config;
use meteocontrol\client\vcomapi\handlers\OAuthAuthorizationHandler;
use meteocontrol\client\vcomapi\UnauthorizedException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

class OAuthAuthorizationHandlerTest extends TestCase {

    /** @var Client|MockObject $mockedClient */
    private $mockedClient;

    /** @var Config */
    private $config;

    /** @var string */
    private $tokenAccessFile;

    public function setup() {
        $this->config = new Config(__DIR__ . '/../_files/config.ini');
        $this->tokenAccessFile = __DIR__ . '/../../../../.tokenAccess/' . md5($this->config->getApiUsername());

        $this->mockedClient = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['post'])
            ->getMock();
    }

    public function testGetAccessTokens() {
        $this->config->deleteTokenAccessFile();

        [$mockedJson, $expectedAccessToken] = $this->parseResponse(__DIR__ . '/_files/expectedResponse.json');

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
        [, $expectedAccessToken] = $this->parseResponse(__DIR__ . '/_files/expectedResponse.json');

        $this->mockedClient->expects($this->never())
            ->method('post');

        $handler = new OAuthAuthorizationHandler($this->config);
        $actualOptions = $handler->appendAuthorizationHeader($this->mockedClient, []);

        $this->assertEquals($expectedAccessToken, $actualOptions['headers']['Authorization']);
        $this->assertFileExists($this->tokenAccessFile);
    }

    /**
     * @depends testGetAccessWithCredentialsFile
     */
    public function testGetAccessWithMultipleUserAndCredentialsFile() {
        $this->config->setApiUsername('guest-user');
        $this->tokenAccessFile = __DIR__ . '/../../../../.tokenAccess/' . md5($this->config->getApiUsername());

        [$mockedJson, $expectedAccessToken] = $this->parseResponse(__DIR__ . '/_files/expectedRefreshResponse.json');

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

        $this->config->deleteTokenAccessFile();

        $this->assertFileNotExists($this->tokenAccessFile);
    }

    public function testHandleUnauthorizedException() {
        $this->config->deleteTokenAccessFile();

        [$mockedJson, , $expectedRefreshToken] = $this->parseResponse(__DIR__ . '/_files/expectedResponse.json');
        [$mockedJson2, $expectedAccessToken] = $this->parseResponse(__DIR__ . '/_files/expectedRefreshResponse.json');

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
        [$mockedJson, $expectedAccessToken, $expectedRefreshToken] =
            $this->parseResponse(__DIR__ . '/_files/expectedRefreshResponse.json');

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
        $mockedException = $this->getMockBuilder(ClientException::class)->disableOriginalConstructor()->getMock();
        $mockedResponse = $this->getMockBuilder(Response::class)->disableOriginalConstructor()->getMock();
        $mockedBody = $this->getMockBuilder(StreamInterface::class)->disableOriginalConstructor()->getMock();

        $mockedBody->expects($this->once())
            ->method('getContents')
            ->willReturn('Unauthorized');

        $mockedException->expects($this->exactly(3))
            ->method('getResponse')
            ->willReturn($mockedResponse);

        $mockedResponse->expects($this->exactly(2))
            ->method('getStatusCode')
            ->willReturn(401);

        $mockedResponse->expects($this->once())
            ->method('getBody')
            ->willReturn($mockedBody);

        $this->mockedClient->expects($this->once())
            ->method('post')
            ->with($this->isType('string'))
            ->willThrowException($mockedException);


        $handler = new OAuthAuthorizationHandler($this->config);

        $handler->appendAuthorizationHeader($this->mockedClient, []);
        try {
            $handler->handleUnauthorizedException($mockedException, $this->mockedClient);
        } catch (UnauthorizedException $ex) {
            $this->assertEquals('Unauthorized', $ex->getMessage());
            $this->assertEquals(401, $ex->getCode());
        }
        $this->assertFileNotExists($this->tokenAccessFile);
    }

    public function testWithTokenCallbackFunction() {
        $this->config->deleteTokenAccessFile();

        [$mockedJson, , $expectedRefreshToken] = $this->parseResponse(__DIR__ . '/_files/expectedResponse.json');
        [$mockedJson2, $expectedAccessToken] = $this->parseResponse(__DIR__ . '/_files/expectedRefreshResponse.json');

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

    private function parseResponse(string $filename) {
        $json = file_get_contents($filename);
        $credentials = json_decode($json, true);
        return [
            $json,
            sprintf('Bearer %s', $credentials['access_token']),
            $credentials['refresh_token'],
        ];
    }
}
