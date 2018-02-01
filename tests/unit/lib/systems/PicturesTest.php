<?php

namespace meteocontrol\client\vcomapi\tests\unit\systems;

use GuzzleHttp\Client;
use meteocontrol\client\vcomapi\ApiClient;
use meteocontrol\client\vcomapi\Config;
use meteocontrol\client\vcomapi\handlers\BasicAuthorizationHandler;
use meteocontrol\client\vcomapi\model\PictureFile;

class PicturesTest extends \PHPUnit_Framework_TestCase {

    /** @var \PHPUnit_Framework_MockObject_MockObject | ApiClient */
    private $api;

    public function setup() {
        $config = new Config();
        $client = new Client();
        $authHandler = new BasicAuthorizationHandler($config);
        $this->api = $this->getMockBuilder('\meteocontrol\client\vcomapi\ApiClient')
            ->setConstructorArgs([$client, $authHandler])
            ->setMethods(['run'])
            ->getMock();
    }

    public function testGetPicture() {
        $json = file_get_contents(__DIR__ . '/responses/getSinglePicture.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/picture'))
            ->willReturn($json);
        /** @var PictureFile $picture */
        $picture = $this->api->system('ABCDE')->picture()->get();

        $this->assertEquals(12345, $picture->id);
        $this->assertEquals('mcLogo.png', $picture->filename);
        $this->assertEquals('image/png', $picture->type);
        $this->assertEquals($this->getEncodedTestPicture(), $picture->content);
    }

    /**
     * @return string
     */
    private function getEncodedTestPicture() {
        return 'data:image/png;base64,' . base64_encode(file_get_contents(__DIR__ . '/responses/mcLogo.png'));
    }
}
