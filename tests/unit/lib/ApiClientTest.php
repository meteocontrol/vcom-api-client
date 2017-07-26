<?php

namespace meteocontrol\client\vcomapi;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use meteocontrol\client\vcomapi\handlers\BasicAuthorizationHandler;
use meteocontrol\client\vcomapi\handlers\OAuthAuthorizationHandler;

function usleep($us) {
    ApiClientTest::$us = $us;
}

class ApiClientTest extends \PHPUnit_Framework_TestCase {

    /** @var float */
    public static $us;

    public function testIsInstantiable() {
        $factory = new Factory();
        $config = new Config(__DIR__ . '/_files/config.ini');
        $apiClient = $factory->getApiClient($config);
        $this->assertInstanceOf('meteocontrol\client\vcomapi\ApiClient', $apiClient);
    }

    public function testIsInstantiableUsingStaticMethodToInstantiate() {
        $apiClient = ApiClient::get("username", "clientName", "Key");
        $this->assertInstanceOf('meteocontrol\client\vcomapi\ApiClient', $apiClient);
    }

    public function testRunGet() {
        $responseMock = $this->getResponseMock();

        /** @var Client|\PHPUnit_Framework_MockObject_MockObject $client */
        $client = $this->getMockBuilder('\GuzzleHttp\Client')
            ->setMethods(['get'])
            ->getMock();
        $client->expects($this->once())
            ->method('get')
            ->with('url')
            ->willReturn($responseMock);

        $config = new Config(__DIR__ . '/_files/config.ini');
        $authHandler = new BasicAuthorizationHandler($config);
        $apiClient = new ApiClient($client, $authHandler);
        $apiClient->run('url');
    }

    public function testRunGetWithParameters() {
        $responseMock = $this->getResponseMock();

        /** @var Client|\PHPUnit_Framework_MockObject_MockObject $client */
        $client = $this->getMockBuilder('\GuzzleHttp\Client')
            ->setMethods(['get'])
            ->getMock();
        $client->expects($this->once())
            ->method('get')
            ->with(
                'url',
                [
                    'query' => ['name' => 'aa', 'value' => 'bb'],
                    'body' => null,
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept-Encoding' => 'gzip, deflate',
                        'Authorization' => 'Basic dGVzdC1hcGktdXNlcm5hbWU6dGVzdC1hcGktcGFzc3dvcmQ='
                    ]
                ]
            )
            ->willReturn($responseMock);

        $config = new Config(__DIR__ . '/_files/config.ini');
        $authHandler = new BasicAuthorizationHandler($config);
        $apiClient = new ApiClient($client, $authHandler);
        $apiClient->run('url', ['name' => 'aa', 'value' => 'bb']);
    }

    public function testRunDeleteWithParameters() {
        $responseMock = $this->getResponseMock();

        /** @var Client|\PHPUnit_Framework_MockObject_MockObject $client */
        $client = $this->getMockBuilder('\GuzzleHttp\Client')
            ->setMethods(['delete'])
            ->getMock();
        $client->expects($this->once())
            ->method('delete')
            ->with(
                'url',
                [
                    'query' => ['name' => 'aa', 'value' => 'bb'],
                    'body' => null,
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept-Encoding' => 'gzip, deflate',
                        'Authorization' => 'Basic dGVzdC1hcGktdXNlcm5hbWU6dGVzdC1hcGktcGFzc3dvcmQ='
                    ]
                ]
            )
            ->willReturn($responseMock);

        $config = new Config(__DIR__ . '/_files/config.ini');
        $authHandler = new BasicAuthorizationHandler($config);
        $apiClient = new ApiClient($client, $authHandler);
        $apiClient->run('url', ['name' => 'aa', 'value' => 'bb'], null, 'DELETE');
    }

    public function testRunPostWithParameters() {
        $responseMock = $this->getResponseMock();

        /** @var Client|\PHPUnit_Framework_MockObject_MockObject $client */
        $client = $this->getMockBuilder('\GuzzleHttp\Client')
            ->setMethods(['post'])
            ->getMock();
        $client->expects($this->once())
            ->method('post')
            ->with(
                'url',
                [
                    'query' => ['name' => 'aa', 'value' => 'bb'],
                    'body' => 'post body',
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept-Encoding' => 'gzip, deflate',
                        'Authorization' => 'Basic dGVzdC1hcGktdXNlcm5hbWU6dGVzdC1hcGktcGFzc3dvcmQ='
                    ]
                ]
            )
            ->willReturn($responseMock);

        $config = new Config(__DIR__ . '/_files/config.ini');
        $authHandler = new BasicAuthorizationHandler($config);
        $apiClient = new ApiClient($client, $authHandler);
        $apiClient->run('url', ['name' => 'aa', 'value' => 'bb'], 'post body', 'POST');
    }

    public function testRunPatchWithParameters() {
        $responseMock = $this->getResponseMock();

        /** @var Client|\PHPUnit_Framework_MockObject_MockObject $client */
        $client = $this->getMockBuilder('\GuzzleHttp\Client')
            ->setMethods(['patch'])
            ->getMock();
        $client->expects($this->once())
            ->method('patch')
            ->with(
                'url',
                [
                    'query' => ['name' => 'aa', 'value' => 'bb'],
                    'body' => 'patch body',
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept-Encoding' => 'gzip, deflate',
                        'Authorization' => 'Basic dGVzdC1hcGktdXNlcm5hbWU6dGVzdC1hcGktcGFzc3dvcmQ='
                    ]
                ]
            )
            ->willReturn($responseMock);

        $config = new Config(__DIR__ . '/_files/config.ini');
        $authHandler = new BasicAuthorizationHandler($config);
        $apiClient = new ApiClient($client, $authHandler);
        $apiClient->run('url', ['name' => 'aa', 'value' => 'bb'], 'patch body', 'PATCH');
    }

    /**
     * @expectedException \meteocontrol\client\vcomapi\ApiClientException
     * @expectedExceptionMessage Unacceptable HTTP method UNKNOWN
     */
    public function testRunUnknownMethod() {
        $factory = new Factory();
        $config = new Config(__DIR__ . '/_files/config.ini');
        $apiClient = $factory->getApiClient($config);
        $apiClient->run('url', ['name' => 'aa', 'value' => 'bb'], 'patch body', 'UNKNOWN');
    }

    /**
     * @expectedException \meteocontrol\client\vcomapi\UnauthorizedException
     * @expectedExceptionMessage 123
     */
    public function testRunWithBasicAuthenticationUnauthorized() {
        $config = new Config();

        $streamMock = $this->getMockBuilder('GuzzleHttp\Psr7\BufferStream')
            ->disableOriginalConstructor()
            ->setMethods(['getContents'])
            ->getMock();
        $streamMock->expects($this->once())
            ->method('getContents')
            ->willReturn('123');

        $request = new Request('GET', 'url');
        $responseMock = $this->getMockBuilder('GuzzleHttp\Psr7\Response')
            ->disableOriginalConstructor()
            ->getMock();
        $responseMock->expects($this->exactly(3))
            ->method('getStatusCode')
            ->willReturn(401);
        $responseMock->expects($this->once())
            ->method('getBody')
            ->willReturn($streamMock);
        $clientException = new ClientException('', $request, $responseMock);

        /** @var Client|\PHPUnit_Framework_MockObject_MockObject $client */
        $client = $this->getMockBuilder('\GuzzleHttp\Client')
            ->setMethods(['get'])
            ->getMock();
        $client->expects($this->once())
            ->method('get')
            ->with('url')
            ->willThrowException($clientException);

        $authHandler = new BasicAuthorizationHandler($config);
        $apiClient = new ApiClient($client, $authHandler);
        $apiClient->run('url');
    }

    public function testRunWithOAuthUnauthorizedAndRefreshTokenIsValid() {
        $config = new Config(__DIR__ . '/_files/config.ini');

        $responseMockPasswordGrant = $this->getResponseMockForOAuthPasswordGrant();

        $responseMockRefreshGrant = $this->getResponseMockForOAuthRefreshGrant();

        $streamMock = $this->getMockBuilder('GuzzleHttp\Psr7\BufferStream')
            ->disableOriginalConstructor()
            ->setMethods(['getContents'])
            ->getMock();
        $streamMock->expects($this->once())
            ->method('getContents')
            ->willReturn('123');
        $responseMock = $this->getMockBuilder('Guzzle\Http\Message\Response')
            ->disableOriginalConstructor()
            ->setMethods(['getHeaderLine', 'getBody'])
            ->getMock();
        $responseMock->expects($this->once())
            ->method('getBody')
            ->willReturn($streamMock);
        $responseMock->expects($this->exactly(1))
            ->method('getHeaderLine')
            ->withConsecutive(['X-RateLimit-Remaining-Minute'])
            ->willReturn('10');

        /** @var Client|\PHPUnit_Framework_MockObject_MockObject $client */
        $client = $this->getMockBuilder('\GuzzleHttp\Client')
            ->setMethods(['get', 'post'])
            ->getMock();
        $client->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(['url'], ['url'])
            ->willReturnOnConsecutiveCalls(
                $this->returnCallback(function () {
                    $request = new Request('GET', 'url');
                    $responseMock = $this->getMockBuilder('GuzzleHttp\Psr7\Response')
                        ->disableOriginalConstructor()
                        ->getMock();
                    $responseMock->expects($this->exactly(2))
                        ->method('getStatusCode')
                        ->willReturn(401);
                    throw new ClientException('', $request, $responseMock);
                }),
                $responseMock
            );
        $client->expects($this->exactly(2))
            ->method('post')
            ->withConsecutive([
                'https://test.meteocontrol.api/login',
                [
                    'form_params' => [
                        'grant_type' => 'password',
                        'username' => 'test-api-username',
                        'password' => 'test-api-password'
                    ]
                ]
            ], [
                'https://test.meteocontrol.api/login',
                [
                    'form_params' => [
                        'grant_type' => 'refresh_token',
                        'refresh_token' => 'refreshToken'
                    ]
                ]
            ])
            ->willReturnOnConsecutiveCalls(
                $responseMockPasswordGrant,
                $responseMockRefreshGrant
            );

        $authHandler = new OAuthAuthorizationHandler($config);
        $apiClient = new ApiClient($client, $authHandler);
        $this->assertEquals('123', $apiClient->run('url'));
    }

    /**
     * @expectedException \meteocontrol\client\vcomapi\UnauthorizedException
     * @expectedExceptionMessage {"hint":"refresh token is revoked"}
     */
    public function testRunWithOAuthUnauthorizedAndRefreshTokenIsInvalid() {
        $config = new Config(__DIR__ . '/_files/config.ini');

        $responseMockPasswordGrant = $this->getResponseMockForOAuthPasswordGrant();

        $request = new Request('GET', 'url');
        $responseMock = $this->getMockBuilder('GuzzleHttp\Psr7\Response')
            ->disableOriginalConstructor()
            ->getMock();
        $responseMock->expects($this->exactly(2))
            ->method('getStatusCode')
            ->willReturn(401);
        $clientException = new ClientException('', $request, $responseMock);

        /** @var Client|\PHPUnit_Framework_MockObject_MockObject $client */
        $client = $this->getMockBuilder('\GuzzleHttp\Client')
            ->setMethods(['get', 'post'])
            ->getMock();
        $client->expects($this->once())
            ->method('get')
            ->with('url')
            ->willThrowException($clientException);
        $client->expects($this->exactly(2))
            ->method('post')
            ->withConsecutive([
                'https://test.meteocontrol.api/login',
                [
                    'form_params' => [
                        'grant_type' => 'password',
                        'username' => 'test-api-username',
                        'password' => 'test-api-password'
                    ]
                ]
            ], [
                'https://test.meteocontrol.api/login',
                [
                    'form_params' => [
                        'grant_type' => 'refresh_token',
                        'refresh_token' => 'refreshToken'
                    ]
                ]
            ])
            ->willReturnOnConsecutiveCalls(
                $responseMockPasswordGrant,
                $this->returnCallback(function () {
                    $streamMock = $this->getMockBuilder('GuzzleHttp\Psr7\BufferStream')
                        ->disableOriginalConstructor()
                        ->setMethods(['getContents'])
                        ->getMock();
                    $streamMock->expects($this->once())
                        ->method('getContents')
                        ->willReturn(json_encode(['hint' => 'refresh token is revoked']));
                    $responseMock = $this->getMockBuilder('GuzzleHttp\Psr7\Response')
                        ->disableOriginalConstructor()
                        ->getMock();
                    $responseMock->expects($this->exactly(3))
                        ->method('getStatusCode')
                        ->willReturn(401);
                    $responseMock->expects($this->once())
                        ->method('getBody')
                        ->willReturn($streamMock);
                    $request = new Request('POST', '/login');
                    throw new ClientException('refresh token is revoked', $request, $responseMock);
                })
            );

        $authHandler = new OAuthAuthorizationHandler($config);
        $apiClient = new ApiClient($client, $authHandler);
        $apiClient->run('url');
    }

    public function testRateLimitHandling() {
        $streamMock = $this->getMockBuilder('GuzzleHttp\Psr7\BufferStream')
            ->disableOriginalConstructor()
            ->setMethods(['getContents'])
            ->getMock();
        $streamMock->expects($this->once())
            ->method('getContents')
            ->willReturn('json string response');
        $responseMock = $this->getMockBuilder('Guzzle\Http\Message\Response')
            ->disableOriginalConstructor()
            ->setMethods(['getHeaderLine', 'getBody'])
            ->getMock();
        $responseMock->expects($this->once())
            ->method('getBody')
            ->willReturn($streamMock);
        $responseMock->expects($this->exactly(3))
            ->method('getHeaderLine')
            ->withConsecutive(['X-RateLimit-Remaining-Minute'], ['Date'], ['X-RateLimit-Reset-Minute'])
            ->willReturnOnConsecutiveCalls('1', 'Thu, 25 Feb 2016 10:32:57 GMT', 'Thu, 25 Feb 2016 10:32:59 GMT');

        /** @var Client|\PHPUnit_Framework_MockObject_MockObject $client */
        $client = $this->getMockBuilder('\GuzzleHttp\Client')
            ->setMethods(['get'])
            ->getMock();
        $client->expects($this->once())
            ->method('get')
            ->with('url')
            ->willReturn($responseMock);

        $config = new Config(__DIR__ . '/_files/config.ini');
        $authHandler = new BasicAuthorizationHandler($config);
        $apiClient = new ApiClient($client, $authHandler);

        self::$us = 0;
        $apiClient->run('url');
        $this->assertEquals(2000000, self::$us);
    }

    private function getResponseMock() {
        $streamMock = $this->getMockBuilder('GuzzleHttp\Psr7\BufferStream')
            ->disableOriginalConstructor()
            ->setMethods(['getContents'])
            ->getMock();
        $streamMock->expects($this->once())
            ->method('getContents')
            ->willReturn('json string response');
        $responseMock = $this->getMockBuilder('Guzzle\Http\Message\Response')
            ->disableOriginalConstructor()
            ->setMethods(['getHeaderLine', 'getBody'])
            ->getMock();
        $responseMock->expects($this->once())
            ->method('getBody')
            ->willReturn($streamMock);
        $responseMock->expects($this->once())
            ->method('getHeaderLine')
            ->with('X-RateLimit-Remaining-Minute')
            ->willReturn('100');
        return $responseMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getResponseMockForOAuthPasswordGrant() {
        $streamMockPasswordGrant = $this->getMockBuilder('GuzzleHttp\Psr7\BufferStream')
            ->disableOriginalConstructor()
            ->setMethods(['getContents'])
            ->getMock();
        $streamMockPasswordGrant->expects($this->once())
            ->method('getContents')
            ->willReturn(json_encode(['access_token' => 'accessToken', 'refresh_token' => 'refreshToken']));
        $responseMockPasswordGrant = $this->getMockBuilder('Guzzle\Http\Message\Response')
            ->disableOriginalConstructor()
            ->setMethods(['getHeaderLine', 'getBody'])
            ->getMock();
        $responseMockPasswordGrant->expects($this->once())
            ->method('getBody')
            ->willReturn($streamMockPasswordGrant);
        return $responseMockPasswordGrant;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getResponseMockForOAuthRefreshGrant() {
        $streamMockRefreshGrant = $this->getMockBuilder('GuzzleHttp\Psr7\BufferStream')
            ->disableOriginalConstructor()
            ->setMethods(['getContents'])
            ->getMock();
        $streamMockRefreshGrant->expects($this->once())
            ->method('getContents')
            ->willReturn(json_encode(['access_token' => 'accessToken1', 'refresh_token' => 'refreshToken1']));
        $responseMockRefreshGrant = $this->getMockBuilder('Guzzle\Http\Message\Response')
            ->disableOriginalConstructor()
            ->setMethods(['getHeaderLine', 'getBody'])
            ->getMock();
        $responseMockRefreshGrant->expects($this->once())
            ->method('getBody')
            ->willReturn($streamMockRefreshGrant);
        return $responseMockRefreshGrant;
    }
}
