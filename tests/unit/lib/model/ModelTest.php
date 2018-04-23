<?php

namespace meteocontrol\client\vcomapi;

use meteocontrol\vcomapi\model\AttachmentFile;
use meteocontrol\vcomapi\model\Comment;
use meteocontrol\vcomapi\model\CommentDetail;
use meteocontrol\vcomapi\model\MeasurementValue;
use meteocontrol\vcomapi\model\SystemDetail;
use meteocontrol\vcomapi\model\Ticket;
use meteocontrol\vcomapi\model\TicketHistory;

class ModelTest extends \PHPUnit_Framework_TestCase {

    public function testModelEncodeToJson() {
        $dateTime = new \DateTime('2018-01-02T03:04:01+00:00');
        $expectedData = json_decode(file_get_contents(__DIR__ . '/_files/models.json'), true);

        $attachmentFile = new AttachmentFile();
        $attachmentFile->created =
        $attachmentFile->createdAt = $dateTime;

        $comment = new Comment();
        $comment->date =
        $comment->createdAt = $dateTime;

        $commentDetail = new CommentDetail();
        $commentDetail->date =
        $commentDetail->createdAt = $dateTime;

        $measurementValue = new MeasurementValue();
        $measurementValue->timestamp = $dateTime;

        $ticket = new Ticket();
        $ticket->date =
        $ticket->lastChange =
        $ticket->rectifiedOn =
        $ticket->createdAt =
        $ticket->lastChangedAt =
        $ticket->rectifiedAt = $dateTime;

        $ticketHistory = new TicketHistory();
        $ticketHistory->timestamp =
        $ticketHistory->createdAt = $dateTime;

        $systemDetail = new SystemDetail();
        $systemDetail->commissionDate = $dateTime;

        $this->assertEquals($expectedData['attachmentFile'], json_decode(json_encode($attachmentFile), true));
        $this->assertEquals($expectedData['comment'], json_decode(json_encode($comment), true));
        $this->assertEquals($expectedData['commentDetail'], json_decode(json_encode($commentDetail), true));
        $this->assertEquals($expectedData['measurementValue'], json_decode(json_encode($measurementValue), true));
        $this->assertEquals($expectedData['ticket'], json_decode(json_encode($ticket), true));
        $this->assertEquals($expectedData['ticketHistory'], json_decode(json_encode($ticketHistory), true));
        $this->assertEquals($expectedData['systemDetail'], json_decode(json_encode($systemDetail), true));
    }
}
