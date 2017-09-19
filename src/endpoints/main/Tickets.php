<?php

namespace meteocontrol\client\vcomapi\endpoints\main;

use meteocontrol\client\vcomapi\ApiClient;
use meteocontrol\client\vcomapi\filters\TicketsCriteria;
use meteocontrol\client\vcomapi\model\Ticket;

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
     * @return Ticket[]
     */
    public function find(TicketsCriteria $criteria) {
        $ticketsJson = $this->api->run($this->uri, $criteria->generateQueryString());
        $decodedData = json_decode($ticketsJson, true);
        return Ticket::deserializeArray($decodedData['data']);
    }

    /**
     * @param Ticket $ticket
     * @return int ticketId
     */
    public function create(Ticket $ticket) {
        if (!$ticket || !$ticket->isValid()) {
            throw new \InvalidArgumentException('Ticket is invalid!');
        }

        $fields = [
            'systemKey' => $ticket->systemKey,
            'designation' => $ticket->designation,
            'createdAt' => $ticket->createdAt ?
                $ticket->createdAt->format(\DateTime::RFC3339) :
                $ticket->date->format(\DateTime::RFC3339)
        ];
        empty($ticket->summary) ?: $fields['summary'] = $ticket->summary;
        empty($ticket->description) ?: $fields['description'] = $ticket->description;
        empty($ticket->status) ?: $fields['status'] = $ticket->status;
        empty($ticket->priority) ?: $fields['priority'] = $ticket->priority;
        empty($ticket->includeInReports) ?: $fields['includeInReports'] = $ticket->includeInReports;
        empty($ticket->assignee) ?: $fields['assignee'] = $ticket->assignee;

        $responseBody = $this->api->run(
            $this->uri,
            null,
            json_encode($fields),
            'POST'
        );
        return json_decode($responseBody)->data->ticketId;
    }
}
