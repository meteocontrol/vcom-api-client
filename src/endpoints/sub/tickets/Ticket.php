<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;

class Ticket extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return \meteocontrol\client\vcomapi\model\Ticket
     */
    public function get() {
        $ticketJson = $this->api->run($this->getUri());
        $decodedData = json_decode($ticketJson, true);
        return \meteocontrol\client\vcomapi\model\Ticket::deserialize($decodedData['data']);
    }

    /**
     * @param \meteocontrol\client\vcomapi\model\Ticket $ticket
     * @param array | null $updateFilter Properties to update. Update all if nothing given.
     */
    public function update(\meteocontrol\client\vcomapi\model\Ticket $ticket, array $updateFilter = null) {
        if (!$ticket || !$ticket->isValid()) {
            throw new \InvalidArgumentException('Ticket is invalid!');
        }
        if (!$updateFilter) {
            $fields = [
                'designation' => $ticket->designation,
                'summary' => $ticket->summary,
                'date' => $ticket->date->format(\DateTime::RFC3339),
                'includeInReports' => $ticket->includeInReports,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'description' => $ticket->description
            ];
        } else {
            $fields = $this->applyFilter($updateFilter, $ticket);
        }

        $this->api->run(
            $this->getUri(),
            null,
            json_encode($fields),
            'PATCH'
        );
    }

    /**
     *
     */
    public function delete() {
        $this->api->run(
            $this->getUri(),
            null,
            null,
            'DELETE'
        );
    }

    /**
     * @return Comments
     */
    public function comments() {
        return new Comments($this);
    }

    /**
     * @param int $commentId
     * @return Comment
     */
    public function comment($commentId) {
        $comments = new Comments($this);
        $commentIdEndpoint = new CommentId($comments, $commentId);
        $commentEndpoint = new \meteocontrol\client\vcomapi\endpoints\sub\tickets\Comment($commentIdEndpoint);
        return $commentEndpoint;
    }
}
