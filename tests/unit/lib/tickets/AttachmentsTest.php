<?php

namespace meteocontrol\client\vcomapi\tests\unit\tickets;

use GuzzleHttp\Client;
use meteocontrol\client\vcomapi\ApiClient;
use meteocontrol\client\vcomapi\Config;
use meteocontrol\client\vcomapi\handlers\BasicAuthorizationHandler;
use meteocontrol\client\vcomapi\model\AttachmentFile;

class AttachmentsTest extends \PHPUnit_Framework_TestCase {

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

    public function testGetAttachments() {
        $json = file_get_contents($this->getExpectedAttachments());
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('tickets/123/attachments'))
            ->willReturn($json);
        $actual = $this->api->ticket(123)->attachments()->get();
        $this->assertCount(2, $actual);
        $this->assertEquals("1234", $actual[0]['attachmentId']);
        $this->assertEquals("test.jpg", $actual[0]['filename']);
        $this->assertEquals("5678", $actual[1]['attachmentId']);
        $this->assertEquals("test2.jpg", $actual[1]['filename']);
    }

    public function testGetAttachment() {
        $json = file_get_contents($this->getExpectedAttachment());
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('tickets/123/attachments/1234'))
            ->willReturn($json);
        $actual = $this->api->ticket(123)->attachment(1234)->get();
        $this->assertEquals(1234, $actual->getId());
        $this->assertEquals("test.jpg", $actual->getFilename());
        $this->assertEquals($this->getTestAttachment(), $actual->getContent());
    }

    public function testCreateAttachment() {
        $json = file_get_contents($this->getExpectedResultOfPostAttachment());
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('tickets/123/attachments'),
                null,
                $this->getPostAttachmentRequestBody(),
                'POST'
            )->willReturn($json);
        $attachment = new AttachmentFile("test.jpg", $this->getTestAttachment());
        $actual = $this->api->ticket(123)->attachments()->create($attachment);
        $this->assertEquals("1234", $actual['attachmentId']);
        $this->assertEquals("test.jpg", $actual['filename']);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid attachment - empty file name and/or content.
     */
    public function testCreateAttachmentButFilenameIsInvalid() {
        $this->api->expects($this->never())
            ->method('run');
        $attachment = new AttachmentFile(null, $this->getTestAttachment());
        $this->api->ticket(123)->attachments()->create($attachment);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid attachment - empty file name and/or content.
     */
    public function testCreateAttachmentButContentIsInvalid() {
        $this->api->expects($this->never())
            ->method('run');
        $attachment = new AttachmentFile("test.jpg", null);
        $this->api->ticket(123)->attachments()->create($attachment);
    }

    /**
     * @return string
     */
    private function getExpectedAttachments() {
        return __DIR__ . "/responses/getAttachments.json";
    }

    /**
     * @return string
     */
    private function getExpectedAttachment() {
        return __DIR__ . "/responses/getAttachment.json";
    }

    /**
     * @return string
     */
    private function getTestAttachment() {
        return file_get_contents(__DIR__ . "/responses/test.jpg");
    }

    /**
     * @return string
     */
    private function getExpectedResultOfPostAttachment() {
        return __DIR__ . "/responses/postAttachmentResult.json";
    }

    /**
     * @return string
     */
    private function getPostAttachmentRequestBody() {
        $data = [
            "filename" => "test.jpg",
            "content" => $this->encodeContent($this->getTestAttachment())
        ];
        return json_encode($data, 79);
    }

    /**
     * @param string $content
     * @return string
     */
    private function encodeContent($content) {
        return 'data:' . "image/jpeg" . ';base64,' . base64_encode($content);
    }
}
