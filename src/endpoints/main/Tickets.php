<?php

namespace meteocontrol\client\vcomapi\endpoints\main;

use DateTime;
use InvalidArgumentException;
use meteocontrol\client\vcomapi\ApiClient;
use meteocontrol\client\vcomapi\endpoints\sub\tickets\Causes;
use meteocontrol\client\vcomapi\filters\TicketsCriteria;
use meteocontrol\vcomapi\model\Ticket;
use meteocontrol\vcomapi\model\TicketOverview;

class Tickets extends MainEndpoint {

    /**
     * @param ApiClient $apiClient
     */
    public function __construct(ApiClient $apiClient) {
        $this->uri = 'tickets';
        $this->api = $apiClient;
    }

    /**
     * @param TicketsCriteria $criteria
     * @return TicketOverview[]
     */
    public function find(TicketsCriteria $criteria): array {
        $ticketsJson = $this->api->run($this->uri, $criteria->generateQueryString());
        return TicketOverview::deserializeArray($this->jsonDecode($ticketsJson, true)['data']);
    }

    /**
     * @param Ticket $ticket
     * @return int
     * @throws InvalidArgumentException
     */
    public function create(Ticket $ticket): int {
        if (!$ticket || !$ticket->isValid()) {
            throw new InvalidArgumentException('Ticket is invalid!');
        }

        $fields = [
            'systemKey' => $ticket->systemKey,
            'designation' => $ticket->designation,
            'createdAt' => $ticket->createdAt->format(DateTime::RFC3339)
        ];
        empty($ticket->summary) ?: $fields['summary'] = $ticket->summary;
        empty($ticket->description) ?: $fields['description'] = $ticket->description;
        empty($ticket->status) ?: $fields['status'] = $ticket->status;
        empty($ticket->priority) ?: $fields['priority'] = $ticket->priority;
        empty($ticket->includeInReports) ?: $fields['includeInReports'] = $ticket->includeInReports;
        empty($ticket->assignee) ?: $fields['assignee'] = $ticket->assignee;
        empty($ticket->cause) ?: $fields['cause'] = $ticket->cause;

        $responseBody = $this->api->run(
            $this->uri,
            null,
            json_encode($fields),
            'POST'
        );
        return $this->jsonDecode($responseBody)->data->ticketId;
    }

    /**
     * @return Causes
     */
    public function causes(): Causes {
        return new Causes($this);
    }
}
