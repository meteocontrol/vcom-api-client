<?php

namespace meteocontrol\client\vcomapi\filters;

use DateTime;
use meteocontrol\client\vcomapi\model\Ticket;

class TicketsCriteria {

    /** @var string[] */
    private $filters;

    /**
     * @param DateTime $from
     * @return TicketsCriteria
     */
    public function withLastChangedAtFrom(DateTime $from): self {
        $this->filters['lastChangedAt[from]'] = $from->format(DATE_ATOM);
        return $this;
    }
    /**
     * @param DateTime $to
     * @return TicketsCriteria
     */
    public function withLastChangedAtTo(DateTime $to): self {
        $this->filters['lastChangedAt[to]'] = $to->format(DATE_ATOM);
        return $this;
    }

    /**
     * @param DateTime $from
     * @return TicketsCriteria
     */
    public function withCreatedAtFrom(DateTime $from): self {
        $this->filters['createdAt[from]'] = $from->format(DATE_ATOM);
        return $this;
    }

    /**
     * @param DateTime $to
     * @return TicketsCriteria
     */
    public function withCreatedAtTo(DateTime $to): self {
        $this->filters['createdAt[to]'] = $to->format(DATE_ATOM);
        return $this;
    }

    /**
     * @param DateTime $from
     * @return TicketsCriteria
     */
    public function withRectifiedAtFrom(DateTime $from): self {
        $this->filters['rectifiedAt[from]'] = $from->format(DATE_ATOM);
        return $this;
    }

    /**
     * @param DateTime $to
     * @return TicketsCriteria
     */
    public function withRectifiedAtTo(DateTime $to): self {
        $this->filters['rectifiedAt[to]'] = $to->format(DATE_ATOM);
        return $this;
    }

    /**
     * @param array | Ticket::REPORT_TYPE_NO | Ticket::REPORT_TYPE_DETAIL | Ticket::REPORT_TYPE_SUMMARY $type
     * @return TicketsCriteria
     */
    public function withIncludeInReports($type): self {
        $this->filters['includeInReports'] = is_array($type) ? implode(',', $type) : $type;
        return $this;
    }

    /**
     * @param array | string $status Ticket::STATUS_OPEN | Ticket::STATUS_CLOSED | Ticket::STATUS_DELETED |
     *                       Ticket::STATUS_ASSIGNED | Ticket::STAUTS_INPROGRESS $status
     * @return TicketsCriteria
     */
    public function withStatus($status): self {
        $this->filters['status'] = is_array($status) ? implode(',', $status) : $status;
        return $this;
    }

    /**
     * @param array | string $severity Ticket::SEVERITY_NORMAL | Ticket::SEVERITY_HIGH |
     *                       Ticket::SEVERITY_CRITICAL $severity
     * @return TicketsCriteria
     */
    public function withSeverity($severity): self {
        $this->filters['severity'] = is_array($severity) ? implode(',', $severity) : $severity;
        return $this;
    }

    /**
     * @param array | string $priority Ticket::PRIORITY_LOW | Ticket::PRIORITY_NORMAL | Ticket::PRIORITY_HIGH |
     *                         Ticket::PRIORITY_URGENT $priority
     * @return TicketsCriteria
     */
    public function withPriority($priority): self {
        $this->filters['priority'] = is_array($priority) ? implode(',', $priority) : $priority;
        return $this;
    }

    /**
     * @param array | string $assignee
     * @return TicketsCriteria
     */
    public function withAssignee($assignee): self {
        $this->filters['assignee'] = is_array($assignee) ? implode(',', $assignee) : $assignee;
        return $this;
    }

    /**
     * @param array | string $systemKey
     * @return TicketsCriteria
     */
    public function withSystemKey($systemKey): self {
        $this->filters['systemKey'] = is_array($systemKey) ? implode(',', $systemKey) : $systemKey;
        return $this;
    }

    /**
     * @return string
     */
    public function generateQueryString(): string {
        return http_build_query($this->filters);
    }
}
