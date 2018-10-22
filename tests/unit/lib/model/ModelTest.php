<?php

namespace meteocontrol\client\vcomapi;

use meteocontrol\vcomapi\model\Outage;
use meteocontrol\vcomapi\model\AttachmentFile;
use meteocontrol\vcomapi\model\Comment;
use meteocontrol\vcomapi\model\CommentDetail;
use meteocontrol\vcomapi\model\Coordinates;
use meteocontrol\vcomapi\model\MeasurementValue;
use meteocontrol\vcomapi\model\MeasurementValueWithInterval;
use meteocontrol\vcomapi\model\SystemDetail;
use meteocontrol\vcomapi\model\Ticket;
use meteocontrol\vcomapi\model\TicketHistory;

class ModelTest extends \PHPUnit_Framework_TestCase {

    public function testModelEncodeToJson() {
        $dateTime = new \DateTime('2018-01-02T03:04:01+00:00');
        $intValue = 1;
        $floatValue = 0.1;
        $stringValue = 'string';
        $booleanValue = true;
        $intervalValue = 300;
        $expectedData = json_decode(file_get_contents(__DIR__ . '/_files/models.json'), true);

        $attachmentFile = new AttachmentFile();
        $attachmentFile->attachmentId = $attachmentFile->creatorId = $intValue;
        $attachmentFile->filename = $attachmentFile->content = $attachmentFile->description = $stringValue;
        $attachmentFile->created = $attachmentFile->createdAt = $dateTime;

        $comment = new Comment();
        $comment->commentId = $intValue;
        $comment->comment = $comment->username = $comment->firstName =
        $comment->lastName = $stringValue;
        $comment->date = $comment->createdAt = $dateTime;

        $commentDetail = new CommentDetail();
        $commentDetail->commentId = $intValue;
        $commentDetail->comment = $commentDetail->username = $commentDetail->firstName =
        $commentDetail->lastName = $stringValue;
        $commentDetail->date = $commentDetail->createdAt = $dateTime;

        $measurementValue = new MeasurementValue();
        $measurementValue->value = $stringValue;
        $measurementValue->timestamp = $dateTime;

        $measurementValueWithInterval = new MeasurementValueWithInterval();
        $measurementValueWithInterval->value = $stringValue;
        $measurementValueWithInterval->timestamp = $dateTime;
        $measurementValueWithInterval->interval = $intervalValue;

        $ticket = new Ticket();
        $ticket->id = $ticket->causeId = $intValue;
        $ticket->systemKey = $ticket->designation = $ticket->summary = $ticket->assignee =
        $ticket->status = $ticket->priority = $ticket->includeInReports = $ticket->severity =
        $ticket->description = $stringValue;
        $ticket->fieldService = $booleanValue;
        $ticket->date = $ticket->lastChange = $ticket->rectifiedOn = $ticket->createdAt =
        $ticket->lastChangedAt = $ticket->rectifiedAt = $dateTime;
        $ticket->outage = new Outage();
        $ticket->outage->startedAt = $ticket->outage->endedAt = $dateTime;
        $ticket->outage->shouldInfluenceAvailability = $ticket->outage->shouldInfluencePr = $booleanValue;
        $ticket->outage->affectedPower = $floatValue;

        $ticketHistory = new TicketHistory();
        $ticketHistory->action = $ticketHistory->personInCharge = $ticketHistory->from =
        $ticketHistory->to = $stringValue;
        $ticketHistory->timestamp = $ticketHistory->createdAt = $dateTime;

        $systemDetail = new SystemDetail();
        $systemDetail->elevation = $intValue;
        $systemDetail->name = $systemDetail->currency = $stringValue;
        $systemDetail->commissionDate = $dateTime;

        $coordinates = new Coordinates();
        $coordinates->latitude = $coordinates->longitude = $floatValue;

        $this->assertEquals($expectedData['attachmentFile'], json_decode(json_encode($attachmentFile), true));
        $this->assertEquals($expectedData['comment'], json_decode(json_encode($comment), true));
        $this->assertEquals($expectedData['commentDetail'], json_decode(json_encode($commentDetail), true));
        $this->assertEquals($expectedData['measurementValue'], json_decode(json_encode($measurementValue), true));
        $this->assertEquals(
            $expectedData['measurementValueWithInterval'],
            json_decode(json_encode($measurementValueWithInterval), true)
        );
        $this->assertEquals($expectedData['ticket'], json_decode(json_encode($ticket), true));
        $this->assertEquals($expectedData['ticketHistory'], json_decode(json_encode($ticketHistory), true));
        $this->assertEquals($expectedData['systemDetail'], json_decode(json_encode($systemDetail), true));
        $this->assertEquals($expectedData['coordinates'], json_decode(json_encode($coordinates), true));
    }
}
