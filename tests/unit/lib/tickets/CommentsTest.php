<?php

namespace meteocontrol\client\vcomapi\tests\unit\tickets;

use GuzzleHttp\Client;
use meteocontrol\client\vcomapi\ApiClient;
use meteocontrol\client\vcomapi\Config;
use meteocontrol\client\vcomapi\handlers\BasicAuthorizationHandler;
use meteocontrol\client\vcomapi\model\Comment;
use meteocontrol\client\vcomapi\model\CommentDetail;

class CommentsTest extends \PHPUnit_Framework_TestCase {

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

    public function testGetComments() {
        $json = file_get_contents(__DIR__ . '/responses/getComments.json');

        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('tickets/123/comments'))
            ->willReturn($json);

        /** @var Comment[] */
        $comments = $this->api->ticket('123')->comments()->get();

        $this->assertEquals(2, count($comments));
        $this->assertEquals(661288, $comments[0]->commentId);
        $this->assertEquals('2016-02-19T12:49:20+01:00', $comments[0]->date->format(\DateTime::RFC3339));
        $this->assertEquals('2016-02-19T16:49:20+05:00', $comments[0]->createdAt->format(\DateTime::RFC3339));
        $this->assertEquals('Comment text', $comments[0]->comment);
        $this->assertEquals('Username', $comments[0]->username);
        $this->assertEquals('First', $comments[0]->firstName);
        $this->assertEquals('Last', $comments[0]->lastName);
        $this->assertEquals(661286, $comments[1]->commentId);
        $this->assertEquals('2016-02-19T12:49:07+01:00', $comments[1]->date->format(\DateTime::RFC3339));
        $this->assertEquals('2016-02-19T16:49:07+05:00', $comments[1]->createdAt->format(\DateTime::RFC3339));
        $this->assertEquals('Comment text', $comments[1]->comment);
        $this->assertEquals('Username', $comments[1]->username);
        $this->assertEquals('First', $comments[1]->firstName);
        $this->assertEquals('Last', $comments[1]->lastName);
    }

    public function testGetSingleComment() {
        $json = file_get_contents(__DIR__ . '/responses/getComment.json');

        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('tickets/123/comments/661288'))
            ->willReturn($json);

        /** @var \meteocontrol\client\vcomapi\model\CommentDetail $commentDetail */
        $commentDetail = $this->api->ticket('123')->comment(661288)->get();

        $this->assertEquals(661288, $commentDetail->commentId);
        $this->assertEquals('2016-02-19T12:49:20+01:00', $commentDetail->date->format(\DateTime::RFC3339));
        $this->assertEquals('2016-02-19T16:49:20+05:00', $commentDetail->createdAt->format(\DateTime::RFC3339));
        $this->assertEquals('Comment text', $commentDetail->comment);
        $this->assertEquals('Username', $commentDetail->username);
        $this->assertEquals('First', $commentDetail->firstName);
        $this->assertEquals('Last', $commentDetail->lastName);
    }

    public function testUpdateComment() {
        $commentDetail = $this->getCommentDetail();

        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('tickets/123/comments/661288'),
                null,
                json_encode(['comment' => 'New Comment']),
                'PATCH'
            );
        $this->api->ticket('123')->comment(661288)->update($commentDetail);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Comment is invalid!
     */
    public function testUpdateCommentWithEmptyText() {
        $commentDetail = $this->getCommentDetail();
        $commentDetail->comment = '';
        $this->api->expects($this->never())
            ->method('run');
        $this->api->ticket('123')->comment(661288)->update($commentDetail);
    }

    public function testCreateComment() {
        $commentDetail = $this->getCommentDetail();

        $expectedResponse = file_get_contents(__DIR__ . '/responses/createComment.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('tickets/123/comments'),
                null,
                json_encode(['comment' => 'New Comment']),
                'POST'
            )
            ->willReturn(
                $expectedResponse
            );
        $commentId = $this->api->ticket('123')->comments()->create($commentDetail);
        $this->assertEquals('454548', $commentId);
    }


    public function testCreateCommentWithDatetime() {
        $commentDetail = $this->getCommentDetail2();

        $expectedResponse = file_get_contents(__DIR__ . '/responses/createComment.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('tickets/123/comments'),
                null,
                json_encode([
                    'comment' => 'New Comment 2',
                    'createdAt' => '2017-10-01T00:00:00+03:00',
                ]),
                'POST'
            )
            ->willReturn(
                $expectedResponse
            );
        $commentId = $this->api->ticket('123')->comments()->create($commentDetail);
        $this->assertEquals('454548', $commentId);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Comment is invalid!
     */
    public function testCreateTicketWithoutRequiredValue() {
        $comment = new CommentDetail();
        $this->api->expects($this->never())
            ->method('run');
        $this->api->ticket('123')->comments()->create($comment);
    }

    public function testDeleteComment() {
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('tickets/123/comments/454548'),
                null,
                null,
                'DELETE'
            );
        $this->api->ticket('123')->comment(454548)->delete();
    }

    /**
     * @return CommentDetail
     */
    private function getCommentDetail() {
        $commentDetail = new CommentDetail();
        $commentDetail->date = \DateTime::createFromFormat(\DateTime::RFC3339, '2016-01-01T00:00:00+00:00');
        $commentDetail->comment = 'New Comment';
        $commentDetail->username = 'test.username';
        return $commentDetail;
    }

    /**
     * @return CommentDetail
     */
    private function getCommentDetail2() {
        $commentDetail = new CommentDetail();
        $commentDetail->createdAt = \DateTime::createFromFormat(\DateTime::RFC3339, '2017-10-01T00:00:00+03:00');
        $commentDetail->comment = 'New Comment 2';
        $commentDetail->username = 'test.username';
        return $commentDetail;
    }
}
