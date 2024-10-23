<?php

namespace meteocontrol\client\vcomapi\tests\unit\tickets;

use DateTime;
use GuzzleHttp\RequestOptions;
use InvalidArgumentException;
use meteocontrol\client\vcomapi\model\AttachmentFile;
use meteocontrol\client\vcomapi\tests\unit\TestCase;

class AttachmentsTest extends TestCase {

    public function testGetAttachments() {
        $json = file_get_contents($this->getExpectedAttachments());
        $this->api->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('tickets/123/attachments'))
            ->willReturn($json);
        $actual = $this->api->ticket('123')->attachments()->get();
        $this->assertCount(2, $actual);
        $this->assertEquals("1234", $actual[0]->attachmentId);
        $this->assertEquals("test.jpg", $actual[0]->filename);
        $this->assertEquals("5678", $actual[1]->attachmentId);
        $this->assertEquals("test2.jpg", $actual[1]->filename);
    }

    public function testGetAttachment() {
        $json = file_get_contents($this->getExpectedAttachment());
        $this->api->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('tickets/123/attachments/1234'))
            ->willReturn($json);
        $actual = $this->api->ticket('123')->attachment(1234)->get();
        $this->assertEquals(1234, $actual->attachmentId);
        $this->assertEquals("test.jpg", $actual->filename);
        $this->assertEquals($this->getEncodedTestAttachment(), $actual->content);
        $this->assertEquals(12345, $actual->creatorId);
        $this->assertEquals("test attachment", $actual->description);
        $this->assertEquals(new DateTime("2017-08-29T04:22:23+01:00"), $actual->createdAt);
        $this->assertEquals(["location" => ["lat" => 40, "lon" => 20]], $actual->metaData);
    }

    public function testCreateAttachment() {
        $json = file_get_contents($this->getExpectedResultOfPostAttachment());
        $this->api->expects($this->once())
            ->method('post')
            ->with(
                $this->identicalTo('tickets/123/attachments'),
                [RequestOptions::JSON => $this->getPostAttachmentRequestBody()],
            )->willReturn($json);
        $attachment = new AttachmentFile();
        $attachment->description = "test attachment";
        $attachment->filename = "test.jpg";
        $attachment->content = $this->getEncodedTestAttachment();
        $attachment->metaData = '{ "location": { "lat": 40, "lon": 20 } }';
        $actual = $this->api->ticket('123')->attachments()->create($attachment);
        $this->assertEquals("1234", $actual['attachmentId']);
        $this->assertEquals("test.jpg", $actual['filename']);
    }

    public function testCreateAttachmentButFilenameIsInvalid() {
        $this->api->expects($this->never())
            ->method('post');
        $attachment = new AttachmentFile();
        $attachment->content = $this->getEncodedTestAttachment();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid attachment - empty file name.');

        $this->api->ticket('123')->attachments()->create($attachment);
    }

    public function testCreateAttachmentButContentIsInvalid() {
        $this->api->expects($this->never())
            ->method('post');
        $attachment = new AttachmentFile();
        $attachment->filename  = "test.jpg";

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid attachment - empty file content.');

        $this->api->ticket('123')->attachments()->create($attachment);
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
    private function getEncodedTestAttachment() {
        return $this->encodeContent(file_get_contents(__DIR__ . "/responses/test.jpg"));
    }

    /**
     * @return string
     */
    private function getExpectedResultOfPostAttachment() {
        return __DIR__ . "/responses/postAttachmentResult.json";
    }

    /**
     * @return array
     */
    private function getPostAttachmentRequestBody() {
        return [
            "filename" => "test.jpg",
            "content" => $this->getEncodedTestAttachment(),
            "description" => "test attachment",
            "metaData" => '{ "location": { "lat": 40, "lon": 20 } }'
        ];
    }

    /**
     * @param string $content
     * @return string
     */
    private function encodeContent(string $content) {
        return 'data:' . "image/jpeg" . ';base64,' . base64_encode($content);
    }
}
