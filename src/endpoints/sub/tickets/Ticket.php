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
                'includeInReports' => $ticket->includeInReports,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'description' => $ticket->description,
                'assignee' => $ticket->assignee
            ];
            if ($ticket->rectifiedAt || $ticket->rectifiedOn) {
                $fields['rectifiedAt'] = $ticket->rectifiedAt ?
                    $ticket->rectifiedAt->format(\DateTime::RFC3339) :
                    $ticket->rectifiedOn->format(\DateTime::RFC3339);
            }
        } else {
            if (isset($updateFilter['rectifiedOn'])) {
                $updateFilter['rectifiedAt'] = $updateFilter['rectifiedOn'];
                unset($updateFilter['rectifiedOn']);
            }
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
        $commentEndpoint = new Comment($commentIdEndpoint);
        return $commentEndpoint;
    }

    /**
     * @return Attachments
     */
    public function attachments() {
        return new Attachments($this);
    }

    /**
     * @param int $attachmentId
     * @return Attachment
     */
    public function attachment($attachmentId) {
        $attachments = new Attachments($this);
        $attachmentIdEndpoint = new AttachmentId($attachments, (string)$attachmentId);
        return new Attachment($attachmentIdEndpoint);
    }

    /**
     * @return Histories
     */
    public function histories() {
        return new Histories($this);
    }
}
