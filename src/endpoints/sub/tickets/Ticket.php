<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use GuzzleHttp\RequestOptions;
use InvalidArgumentException;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\TicketsCriteria;
use meteocontrol\client\vcomapi\model\Ticket as TicketModel;

class Ticket extends SubEndpoint {

    public function __construct(EndpointInterface $parent) {
        $this->uri = '';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    public function get(?TicketsCriteria $criteria = null): TicketModel {
        $options = [];
        if ($criteria) {
            $options = [RequestOptions::QUERY => $criteria->generateQueryString()];
        }
        $ticketJson = $this->api->get($this->getUri(), $options);
        return TicketModel::deserialize($this->jsonDecode($ticketJson, true)['data']);
    }

    /**
     * @param TicketModel $ticket
     * @param array | null $updateFilter Properties to update. Update all if nothing given.
     * @return void
     * @throws InvalidArgumentException
     */
    public function update(TicketModel $ticket, array $updateFilter = null): void {
        if (!$ticket || !$ticket->isValid()) {
            throw new InvalidArgumentException('Ticket is invalid!');
        }
        if (!$updateFilter) {
            $fields = [
                'designation' => $ticket->designation,
                'summary' => $ticket->summary,
                'includeInReports' => $ticket->includeInReports,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'description' => $ticket->description,
                'assignee' => $ticket->assignee,
            ];
            if ($ticket->rectifiedAt) {
                $fields['rectifiedAt'] = $ticket->rectifiedAt->format(DATE_ATOM);
            }
            if ($ticket->cause) {
                $fields['cause'] = $ticket->cause;
            }
            if (is_bool($ticket->fieldService)) {
                $fields['fieldService'] = $ticket->fieldService;
            }
        } else {
            $fields = $this->applyFilter($updateFilter, $ticket);
        }

        $this->api->patch($this->getUri(), [RequestOptions::JSON => $fields]);
    }

    public function delete(): void {
        $this->api->delete($this->getUri());
    }

    public function comments(): Comments {
        return new Comments($this);
    }

    public function comment(int $commentId): Comment {
        $comments = new Comments($this);
        $commentIdEndpoint = new CommentId($comments, $commentId);
        return new Comment($commentIdEndpoint);
    }

    public function outage(): Outage {
        return new Outage($this);
    }

    public function attachments(): Attachments {
        return new Attachments($this);
    }

    public function attachment(int $attachmentId): Attachment {
        $attachments = new Attachments($this);
        $attachmentIdEndpoint = new AttachmentId($attachments, (string)$attachmentId);
        return new Attachment($attachmentIdEndpoint);
    }

    public function histories(): Histories {
        return new Histories($this);
    }

    public function yieldLosses(): YieldLosses {
        return new YieldLosses($this);
    }
}
