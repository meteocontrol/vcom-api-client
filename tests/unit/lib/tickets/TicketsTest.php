<?php

namespace meteocontrol\client\vcomapi\tests\unit\tickets;

use GuzzleHttp\Client;
use meteocontrol\client\vcomapi\ApiClient;
use meteocontrol\client\vcomapi\Config;
use meteocontrol\client\vcomapi\filters\TicketsCriteria;
use meteocontrol\client\vcomapi\model\Ticket;

class TicketsTest extends \PHPUnit_Framework_TestCase {

    /** @var \PHPUnit_Framework_MockObject_MockObject | ApiClient */
    private $api;

    public function setup() {
        $config = new Config();
        $client = new Client();
        $this->api = $this->getMockBuilder('\meteocontrol\client\vcomapi\ApiClient')
            ->setConstructorArgs([$config, $client])
            ->setMethods(['run'])
            ->getMock();
    }

    public function testGetTicketsWithFilter() {
        $json = file_get_contents(__DIR__ . '/responses/getTickets.json');
        $criteria = new TicketsCriteria();
        $criteria->withDateFrom(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-01-01T00:00:00+00:00'))
            ->withDateTo(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-03-01T01:00:00+00:00'))
            ->withLastChangeFrom(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-01-01T12:00:00+00:00'))
            ->withLastChangeTo(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-02-21T12:00:00+00:00'))
            ->withRectifiedOnFrom(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-01-01T14:00:00+00:00'))
            ->withRectifiedOnTo(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-02-20T14:00:00+00:00'))
            ->withStatus(Ticket::STATUS_OPEN)
            ->withPriority(Ticket::PRIORITY_NORMAL)
            ->withSeverity(Ticket::SEVERITY_CRITICAL)
            ->withSystemKey('ABC123');

        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('tickets'),
                'date%5Bfrom%5D=2016-01-01T00%3A00%3A00%2B00%3A00' .
                '&date%5Bto%5D=2016-03-01T01%3A00%3A00%2B00%3A00' .
                '&lastChange%5Bfrom%5D=2016-01-01T12%3A00%3A00%2B00%3A00' .
                '&lastChange%5Bto%5D=2016-02-21T12%3A00%3A00%2B00%3A00' .
                '&rectifiedOn%5Bfrom%5D=2016-01-01T14%3A00%3A00%2B00%3A00' .
                '&rectifiedOn%5Bto%5D=2016-02-20T14%3A00%3A00%2B00%3A00' .
                '&status=open' .
                '&priority=normal' .
                '&severity=critical' .
                '&systemKey=ABC123'
            )
            ->willReturn($json);

        /** @var \meteocontrol\client\vcomapi\model\Ticket[] $tickets */
        $tickets = $this->api->tickets()->find($criteria);

        $this->assertEquals(2, count($tickets));

        $this->assertEquals(123, $tickets[0]->id);
        $this->assertEquals('ABCDE', $tickets[0]->systemKey);
        $this->assertEquals('Ticket #123', $tickets[0]->designation);
        $this->assertEquals('This is a summary.', $tickets[0]->summary);
        $this->assertEquals('2016-01-01T12:00:00', $tickets[0]->date->format('Y-m-d\TH:i:s'));
        $this->assertEquals('2016-01-01T13:00:00', $tickets[0]->lastChange->format('Y-m-d\TH:i:s'));
        $this->assertEquals(null, $tickets[0]->assignee);
        $this->assertEquals(Ticket::STATUS_CLOSED, $tickets[0]->status);
        $this->assertEquals(Ticket::PRIORITY_NORMAL, $tickets[0]->priority);
        $this->assertEquals(Ticket::SEVERITY_NORMAL, $tickets[0]->severity);

        $this->assertEquals(456, $tickets[1]->id);
        $this->assertEquals('FGHIJ', $tickets[1]->systemKey);
        $this->assertEquals('Ticket #456', $tickets[1]->designation);
        $this->assertEquals('This is a summary.', $tickets[1]->summary);
        $this->assertEquals('2016-02-01T12:00:00', $tickets[1]->date->format('Y-m-d\TH:i:s'));
        $this->assertEquals('2016-02-02T13:00:00', $tickets[1]->lastChange->format('Y-m-d\TH:i:s'));
        $this->assertEquals(null, $tickets[1]->assignee);
        $this->assertEquals(Ticket::STATUS_INPROGRESS, $tickets[1]->status);
        $this->assertEquals(Ticket::PRIORITY_HIGH, $tickets[1]->priority);
        $this->assertEquals(null, $tickets[1]->severity);
    }

    public function testGetSingleTicket() {
        $json = file_get_contents(__DIR__ . '/responses/getTicket.json');

        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('tickets/123'))
            ->willReturn($json);

        /** @var \meteocontrol\client\vcomapi\model\Ticket $ticket */
        $ticket = $this->api->ticket(123)->get();

        $this->assertEquals(123, $ticket->id);
        $this->assertEquals('ABCDE', $ticket->systemKey);
        $this->assertEquals('Ticket #123', $ticket->designation);
        $this->assertEquals('This is a summary.', $ticket->summary);
        $this->assertEquals('2016-01-01T12:00:00', $ticket->date->format('Y-m-d\TH:i:s'));
        $this->assertEquals('2016-01-01T13:00:00', $ticket->lastChange->format('Y-m-d\TH:i:s'));
        $this->assertEquals('2016-01-01T14:00:00', $ticket->rectifiedOn->format('Y-m-d\TH:i:s'));
        $this->assertEquals(null, $ticket->assignee);
        $this->assertEquals(Ticket::STATUS_INPROGRESS, $ticket->status);
        $this->assertEquals(10, $ticket->causeId);
        $this->assertEquals(Ticket::PRIORITY_NORMAL, $ticket->priority);
        $this->assertEquals(Ticket::SEVERITY_NORMAL, $ticket->severity);
        $this->assertEquals('no', $ticket->includeInReports);
        $this->assertEquals(true, $ticket->fieldService);
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
                            'date' => '2016-01-01T00:00:00+00:00',
                            'includeInReports' => 'detail',
                            'status' => 'closed',
                            'priority' => 'urgent',
                            'description' => 'description'
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
                            'date' => '2016-01-01T00:00:00+00:00',
                            'includeInReports' => 'detail'
                        ]
                    ),
                    'PATCH'
                ]
            );
        $this->api->ticket($ticket->id)->update($ticket);
        $this->api->ticket($ticket->id)->update($ticket, ['designation', 'date', 'includeInReports']);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Ticket is invalid!
     */
    public function testUpdateTicketWithWrongFilter() {
        $ticket = $this->getTicket();
        unset($ticket->date);

        $this->api->expects($this->never())
            ->method('run');
        $this->api->ticket($ticket->id)->update($ticket, ['designation', 'date', 'reportType']);
    }

    public function testCreateTicket() {
        $ticket = new Ticket();
        $ticket->systemKey = 'ABCDE';
        $ticket->designation = 'designation';
        $ticket->date = \DateTime::createFromFormat(\DateTime::RFC3339, '2016-07-01T02:02:10+00:00');
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
                        'date' => '2016-07-01T02:02:10+00:00',
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

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Ticket is invalid!
     */
    public function testCreateTicketWithoutRequiredValue() {
        $ticket = new Ticket();
        $ticket->designation = 'designation';
        $ticket->date = \DateTime::createFromFormat(\DateTime::RFC3339, '2016-07-01T02:02:10+00:00');
        $ticket->status = Ticket::STATUS_OPEN;
        $ticket->priority = Ticket::PRIORITY_HIGH;

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
        $this->api->ticket(123)->delete();
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
        $ticket->date = \DateTime::createFromFormat(\DateTime::RFC3339, '2016-01-01T00:00:00+00:00');
        $ticket->includeInReports = Ticket::REPORT_TYPE_DETAIL;
        $ticket->status = Ticket::STATUS_CLOSED;
        $ticket->priority = Ticket::PRIORITY_URGENT;
        $ticket->description = 'description';
        return $ticket;
    }
}
