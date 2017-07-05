<?php

namespace meteocontrol\client\vcomapi;

use GuzzleHttp\Client;
use meteocontrol\client\vcomapi\handlers\BasicAuthorizationHandler;

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
}
