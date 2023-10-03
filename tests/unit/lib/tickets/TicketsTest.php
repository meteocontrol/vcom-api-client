<?php

namespace meteocontrol\client\vcomapi\tests\unit\tickets;

use DateTime;
use InvalidArgumentException;
use meteocontrol\client\vcomapi\filters\TicketsCriteria;
use meteocontrol\client\vcomapi\model\Ticket;
use meteocontrol\client\vcomapi\tests\unit\TestCase;

class TicketsTest extends TestCase {

    public function testGetTicketsWithFilter() {
        $json = file_get_contents(__DIR__ . '/responses/getTickets.json');
        $criteria = new TicketsCriteria();
        $criteria->withCreatedAtFrom(DateTime::createFromFormat(DATE_ATOM, '2016-01-01T00:00:00+00:00'))
            ->withCreatedAtTo(DateTime::createFromFormat(DATE_ATOM, '2016-03-01T01:00:00+00:00'))
            ->withLastChangedAtFrom(DateTime::createFromFormat(DATE_ATOM, '2016-01-01T12:00:00+00:00'))
            ->withLastChangedAtTo(DateTime::createFromFormat(DATE_ATOM, '2016-02-21T12:00:00+00:00'))
            ->withRectifiedAtFrom(DateTime::createFromFormat(DATE_ATOM, '2016-01-01T14:00:00+00:00'))
            ->withRectifiedAtTo(DateTime::createFromFormat(DATE_ATOM, '2016-02-20T14:00:00+00:00'));

        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('tickets'),
                $this->identicalToUrl(
                    'createdAt[from]=2016-01-01T00:00:00+00:00' .
                    '&createdAt[to]=2016-03-01T01:00:00+00:00' .
                    '&lastChangedAt[from]=2016-01-01T12:00:00+00:00' .
                    '&lastChangedAt[to]=2016-02-21T12:00:00+00:00' .
                    '&rectifiedAt[from]=2016-01-01T14:00:00+00:00' .
                    '&rectifiedAt[to]=2016-02-20T14:00:00+00:00'
                )
            )
            ->willReturn($json);

        /** @var \meteocontrol\client\vcomapi\model\TicketOverview[] $tickets */
        $tickets = $this->api->tickets()->find($criteria);

        $this->assertCount(2, $tickets);

        $this->assertEquals(123, $tickets[0]->id);
        $this->assertEquals('ABCDE', $tickets[0]->systemKey);
        $this->assertEquals('Ticket #123', $tickets[0]->designation);
        $this->assertEquals('This is a summary.', $tickets[0]->summary);
        $this->assertEquals('2016-01-01T12:00:00+02:00', $tickets[0]->createdAt->format(DATE_ATOM));
        $this->assertEquals('2016-01-01T13:00:00+02:00', $tickets[0]->lastChangedAt->format(DATE_ATOM));
        $this->assertEquals(Ticket::STATUS_CLOSED, $tickets[0]->status);
        $this->assertEquals(Ticket::PRIORITY_NORMAL, $tickets[0]->priority);
        $this->assertEquals(true, $tickets[0]->fieldService);
        $this->assertEquals(Ticket::SEVERITY_NORMAL, $tickets[0]->severity);

        $this->assertEquals(456, $tickets[1]->id);
        $this->assertEquals('FGHIJ', $tickets[1]->systemKey);
        $this->assertEquals('Ticket #456', $tickets[1]->designation);
        $this->assertEquals('This is a summary.', $tickets[1]->summary);
        $this->assertEquals('2016-02-01T12:00:00+04:00', $tickets[1]->createdAt->format(DATE_ATOM));
        $this->assertEquals('2016-02-02T13:00:00+04:00', $tickets[1]->lastChangedAt->format(DATE_ATOM));
        $this->assertEquals(Ticket::STATUS_INPROGRESS, $tickets[1]->status);
        $this->assertEquals(Ticket::PRIORITY_HIGH, $tickets[1]->priority);
        $this->assertEquals(false, $tickets[1]->fieldService);
        $this->assertEquals(null, $tickets[1]->severity);
    }

    public function testGetTicketsWithMultipleParametersInFilter() {
        $json = file_get_contents(__DIR__ . '/responses/getTickets2.json');
        $criteria = new TicketsCriteria();
        $criteria->withCreatedAtFrom(DateTime::createFromFormat(DATE_ATOM, '2016-01-01T00:00:00+00:00'))
            ->withCreatedAtTo(DateTime::createFromFormat(DATE_ATOM, '2016-03-01T01:00:00+00:00'))
            ->withLastChangedAtFrom(DateTime::createFromFormat(DATE_ATOM, '2016-01-01T12:00:00+00:00'))
            ->withLastChangedAtTo(DateTime::createFromFormat(DATE_ATOM, '2016-02-21T12:00:00+00:00'))
            ->withRectifiedAtFrom(DateTime::createFromFormat(DATE_ATOM, '2016-01-01T14:00:00+00:00'))
            ->withRectifiedAtTo(DateTime::createFromFormat(DATE_ATOM, '2016-02-20T14:00:00+00:00'))
            ->withStatus([Ticket::STATUS_CLOSED, Ticket::STATUS_INPROGRESS])
            ->withPriority([Ticket::PRIORITY_NORMAL, Ticket::PRIORITY_HIGH])
            ->withSeverity([Ticket::PRIORITY_NORMAL, Ticket::PRIORITY_HIGH])
            ->withSystemKey(['ABCDE', 'FGHIJ']);

        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('tickets'),
                $this->identicalToUrl(
                    'createdAt[from]=2016-01-01T00:00:00+00:00' .
                    '&createdAt[to]=2016-03-01T01:00:00+00:00' .
                    '&lastChangedAt[from]=2016-01-01T12:00:00+00:00' .
                    '&lastChangedAt[to]=2016-02-21T12:00:00+00:00' .
                    '&rectifiedAt[from]=2016-01-01T14:00:00+00:00' .
                    '&rectifiedAt[to]=2016-02-20T14:00:00+00:00' .
                    '&status=closed,inProgress&priority=normal,high' .
                    '&severity=normal,high&systemKey=ABCDE,FGHIJ'
                )
            )
            ->willReturn($json);

        /** @var \meteocontrol\client\vcomapi\model\Ticket[] $tickets */
        $tickets = $this->api->tickets()->find($criteria);

        $this->assertCount(2, $tickets);

        $this->assertEquals('123', $tickets[0]->id);
        $this->assertEquals('ABCDE', $tickets[0]->systemKey);
        $this->assertEquals('Ticket #123', $tickets[0]->designation);
        $this->assertEquals('This is a summary.', $tickets[0]->summary);
        $this->assertEquals('2016-01-01T12:00:00+02:00', $tickets[0]->createdAt->format(DATE_ATOM));
        $this->assertEquals('2016-01-01T13:00:00+02:00', $tickets[0]->lastChangedAt->format(DATE_ATOM));
        $this->assertEquals(Ticket::STATUS_CLOSED, $tickets[0]->status);
        $this->assertEquals(Ticket::PRIORITY_NORMAL, $tickets[0]->priority);
        $this->assertEquals(Ticket::SEVERITY_NORMAL, $tickets[0]->severity);

        $this->assertEquals('456', $tickets[1]->id);
        $this->assertEquals('FGHIJ', $tickets[1]->systemKey);
        $this->assertEquals('Ticket #456', $tickets[1]->designation);
        $this->assertEquals('This is a summary.', $tickets[1]->summary);
        $this->assertEquals('2016-02-01T12:00:00+04:00', $tickets[1]->createdAt->format(DATE_ATOM));
        $this->assertEquals('2016-02-02T13:00:00+04:00', $tickets[1]->lastChangedAt->format(DATE_ATOM));
        $this->assertEquals(Ticket::STATUS_INPROGRESS, $tickets[1]->status);
        $this->assertEquals(Ticket::PRIORITY_HIGH, $tickets[1]->priority);
        $this->assertEquals(Ticket::PRIORITY_HIGH, $tickets[1]->severity);
    }


    public function testGetSingleTicket() {
        $json = file_get_contents(__DIR__ . '/responses/getTicket.json');

        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('tickets/123'))
            ->willReturn($json);

        /** @var \meteocontrol\client\vcomapi\model\Ticket $ticket */
        $ticket = $this->api->ticket('123')->get();

        $this->assertEquals('123', $ticket->id);
        $this->assertEquals('ABCDE', $ticket->systemKey);
        $this->assertEquals('Ticket #123', $ticket->designation);
        $this->assertEquals('This is a summary.', $ticket->summary);
        $this->assertEquals('2016-01-01T12:00:00+02:00', $ticket->createdAt->format(DATE_ATOM));
        $this->assertEquals('2016-01-01T13:00:00+02:00', $ticket->lastChangedAt->format(DATE_ATOM));
        $this->assertEquals('2016-01-01T14:00:00+02:00', $ticket->rectifiedAt->format(DATE_ATOM));
        $this->assertEquals(null, $ticket->assignee);
        $this->assertEquals(Ticket::STATUS_INPROGRESS, $ticket->status);
        $this->assertEquals(10, $ticket->causeId);
        $this->assertEquals(Ticket::PRIORITY_NORMAL, $ticket->priority);
        $this->assertEquals(Ticket::SEVERITY_NORMAL, $ticket->severity);
        $this->assertEquals('no', $ticket->includeInReports);
        $this->assertEquals(true, $ticket->fieldService);
        $this->assertNull($ticket->outage);
    }

    public function testGetSingleTicketWithOutage() {
        $json = file_get_contents(__DIR__ . '/responses/getTicketWithOutage.json');

        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('tickets/123'))
            ->willReturn($json);

        /** @var \meteocontrol\client\vcomapi\model\Ticket $ticket */
        $ticket = $this->api->ticket('123')->get();

        $this->assertEquals('123', $ticket->id);
        $this->assertEquals('ABCDE', $ticket->systemKey);
        $this->assertEquals('Ticket #123', $ticket->designation);
        $this->assertEquals('This is a summary.', $ticket->summary);
        $this->assertEquals('2016-01-01T12:00:00+02:00', $ticket->createdAt->format(DATE_ATOM));
        $this->assertEquals('2016-01-01T13:00:00+02:00', $ticket->lastChangedAt->format(DATE_ATOM));
        $this->assertEquals('2016-01-01T14:00:00+02:00', $ticket->rectifiedAt->format(DATE_ATOM));
        $this->assertEquals(null, $ticket->assignee);
        $this->assertEquals(Ticket::STATUS_INPROGRESS, $ticket->status);
        $this->assertEquals(10, $ticket->causeId);
        $this->assertEquals(Ticket::PRIORITY_NORMAL, $ticket->priority);
        $this->assertEquals(Ticket::SEVERITY_NORMAL, $ticket->severity);
        $this->assertEquals('no', $ticket->includeInReports);
        $this->assertEquals(true, $ticket->fieldService);
        $this->assertEquals("2018-01-01T12:20:00+00:00", $ticket->outage->startedAt->format(DATE_ATOM));
        $this->assertEquals("2018-01-02T16:00:00+00:00", $ticket->outage->endedAt->format(DATE_ATOM));
        $this->assertEquals(5, $ticket->outage->affectedPower);
        $this->assertTrue($ticket->outage->shouldInfluenceAvailability);
        $this->assertTrue($ticket->outage->shouldInfluencePr);
    }

    public function testUpdateTicket() {
        $ticket = $this->getTicket();

        $this->api->expects($this->exactly(2))
            ->method('run')
            ->withConsecutive(
                [
                    $this->identicalTo('tickets/123'),
                    null,
                    json_encode(
                        [
                            'designation' => 'abc',
                            'summary' => 'summary',
                            'includeInReports' => 'detail',
                            'status' => 'closed',
                            'priority' => 'urgent',
                            'description' => 'description',
                            'assignee' => 9823,
                            'cause' => 'Unknown',
                        ]
                    ),
                    'PATCH'
                ],
                [
                    $this->identicalTo('tickets/123'),
                    null,
                    json_encode(
                        [
                            'designation' => 'abc',
                            'createdAt' => '2016-01-01T00:00:00+00:00',
                            'includeInReports' => 'detail',
                        ]
                    ),
                    'PATCH'
                ]
            );
        $this->api->ticket($ticket->id)->update($ticket);
        $this->api->ticket($ticket->id)->update($ticket, ['designation', 'createdAt', 'includeInReports']);
    }

    public function testUpdateTicketWithWrongFilter() {
        $ticket = $this->getTicket();
        unset($ticket->createdAt);

        $this->api->expects($this->never())->method('run');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Ticket is invalid!');

        $this->api->ticket($ticket->id)->update($ticket, ['designation', 'createdAt', 'reportType']);
    }

    public function testCreateTicket() {
        $ticket = new Ticket();
        $ticket->systemKey = 'ABCDE';
        $ticket->designation = 'designation';
        $ticket->createdAt = DateTime::createFromFormat(DATE_ATOM, '2016-07-01T02:02:10+00:00');
        $ticket->status = Ticket::STATUS_OPEN;
        $ticket->priority = Ticket::PRIORITY_HIGH;
        $ticket->includeInReports = Ticket::REPORT_TYPE_SUMMARY;

        $expectedResponse = file_get_contents(__DIR__ . '/responses/createTicket.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('tickets'),
                null,
                json_encode(
                    [
                        'systemKey' => 'ABCDE',
                        'designation' => 'designation',
                        'createdAt' => '2016-07-01T02:02:10+00:00',
                        'status' => 'open',
                        'priority' => 'high',
                        'includeInReports' => 'summary'
                    ]
                ),
                'POST'
            )
            ->willReturn(
                $expectedResponse
            );
        $ticketId = $this->api->tickets()->create($ticket);
        $this->assertEquals('123', $ticketId);
    }

    public function testCreateTicketWithoutRequiredValue() {
        $ticket = new Ticket();
        $ticket->designation = 'designation';
        $ticket->createdAt = DateTime::createFromFormat(DATE_ATOM, '2016-07-01T02:02:10+00:00');
        $ticket->status = Ticket::STATUS_OPEN;
        $ticket->priority = Ticket::PRIORITY_HIGH;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Ticket is invalid!');

        $this->api->tickets()->create($ticket);
    }

    public function testDeleteTicket() {
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('tickets/123'),
                null,
                null,
                'DELETE'
            );
        $this->api->ticket('123')->delete();
    }

    public function testGetTicketHistories() {
        $json = file_get_contents(__DIR__ . '/responses/getTicketHistories.json');

        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('tickets/123/histories'))
            ->willReturn($json);

        $histories = $this->api->ticket('123')->histories()->get();

        $this->assertCount(3, $histories);

        $history = $histories[0];
        $this->assertEquals(
            DateTime::createFromFormat(DATE_ATOM, '2017-08-31T03:42:03+02:00'),
            $history->createdAt
        );
        $this->assertSame('statusChanged', $history->action);
        $this->assertSame('userB', $history->personInCharge);
        $this->assertSame('open', $history->from);
        $this->assertSame('inProgress', $history->to);

        $history = $histories[1];
        $this->assertEquals(
            DateTime::createFromFormat(DATE_ATOM, '2017-08-31T04:18:51+02:00'),
            $history->createdAt
        );
        $this->assertSame('assigneeChanged', $history->action);
        $this->assertSame('userB', $history->personInCharge);
        $this->assertSame(null, $history->from);
        $this->assertSame('userA', $history->to);

        $history = $histories[2];
        $this->assertEquals(
            DateTime::createFromFormat(DATE_ATOM, '2017-08-31T04:19:41+02:00'),
            $history->createdAt
        );
        $this->assertSame('assigneeChanged', $history->action);
        $this->assertSame('userB', $history->personInCharge);
        $this->assertSame('userA', $history->from);
        $this->assertSame('userB', $history->to);
    }

    /**
     * @return Ticket
     */
    private function getTicket() {
        $ticket = new Ticket();
        $ticket->id = '123';
        $ticket->systemKey = 'ABCDE';
        $ticket->designation = 'abc';
        $ticket->summary = 'summary';
        $ticket->createdAt = DateTime::createFromFormat(DATE_ATOM, '2016-01-01T00:00:00+00:00');
        $ticket->includeInReports = Ticket::REPORT_TYPE_DETAIL;
        $ticket->status = Ticket::STATUS_CLOSED;
        $ticket->priority = Ticket::PRIORITY_URGENT;
        $ticket->description = 'description';
        $ticket->assignee = 9823;
        $ticket->cause = 'Unknown';
        return $ticket;
    }
}
