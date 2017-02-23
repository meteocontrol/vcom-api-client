<?php

namespace meteocontrol\client\vcomapi\filters;

use meteocontrol\client\vcomapi\model\Ticket;

class TicketsCriteria {

    /** @var string[] */
    private $filters;

    /**
     * @return \DateTime
     */
    public function getLastChangeFrom() {
        return \DateTime::createFromFormat(\DateTime::RFC3339, $this->filters['lastChange[from]']);
    }

    /**
     * @param \DateTime $from
     * @return TicketsCriteria
     */
    public function withLastChangeFrom(\DateTime $from) {
        $this->filters['lastChange[from]'] = $from->format(\DateTime::RFC3339);
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastChangeTo() {
        return \DateTime::createFromFormat(\DateTime::RFC3339, $this->filters['lastChange[to]']);
    }

    /**
     * @param \DateTime $to
     * @return TicketsCriteria
     */
    public function withLastChangeTo(\DateTime $to) {
        $this->filters['lastChange[to]'] = $to->format(\DateTime::RFC3339);
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateFrom() {
        return \DateTime::createFromFormat(\DateTime::RFC3339, $this->filters['date[from]']);
    }

    /**
     * @param \DateTime $from
     * @return TicketsCriteria
     */
    public function withDateFrom(\DateTime $from) {
        $this->filters['date[from]'] = $from->format(\DateTime::RFC3339);
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateTo() {
        return \DateTime::createFromFormat(\DateTime::RFC3339, $this->filters['date[to]']);
    }

    /**
     * @param \DateTime $to
     * @return TicketsCriteria
     */
    public function withDateTo(\DateTime $to) {
        $this->filters['date[to]'] = $to->format(\DateTime::RFC3339);
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRectifiedOnFrom() {
        return \DateTime::createFromFormat(\DateTime::RFC3339, $this->filters['rectifiedOn[from]']);
    }

    /**
     * @param \DateTime $from
     * @return TicketsCriteria
     */
    public function withRectifiedOnFrom(\DateTime $from) {
        $this->filters['rectifiedOn[from]'] = $from->format(\DateTime::RFC3339);
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getRectifiedOnTo() {
        return \DateTime::createFromFormat(\DateTime::RFC3339, $this->filters['rectifiedOn[to]']);
    }

    /**
     * @param \DateTime $to
     * @return TicketsCriteria
     */
    public function withRectifiedOnTo(\DateTime $to) {
        $this->filters['rectifiedOn[to]'] = $to->format(\DateTime::RFC3339);
        return $this;
    }

    /**
     * @return Ticket::REPORT_TYPE_NO | Ticket::REPORT_TYPE_DETAIL | Ticket::REPORT_TYPE_SUMMARY
     */
    public function getIncludeInReports() {
        return $this->filters['includeInReports'];
    }

    /**
     * @param Ticket::REPORT_TYPE_NO | Ticket::REPORT_TYPE_DETAIL | Ticket::REPORT_TYPE_SUMMARY $type
     * @return TicketsCriteria
     */
    public function withIncludeInReports($type) {
        $this->filters['includeInReports'] = $type;
        return $this;
    }

    /**
     * @return string Ticket::STATUS_OPEN | Ticket::STATUS_CLOSED | Ticket::STATUS_DELETED | Ticket::STATUS_ASSIGNED |
     *                Ticket::STATUS_INPROGRESS
     */
    public function getStatus() {
        return $this->filters['status'];
    }

    /**
     * @param string $status Ticket::STATUS_OPEN | Ticket::STATUS_CLOSED | Ticket::STATUS_DELETED |
     *                       Ticket::STATUS_ASSIGNED | Ticket::STAUTS_INPROGRESS $status
     * @return TicketsCriteria
     */
    public function withStatus($status) {
        $this->filters['status'] = $status;
        return $this;
    }

    /**
     * @return Ticket::SEVERITY_NORMAL | Ticket::SEVERITY_HIGH | Ticket::SEVERITY_CRITICAL
     */
    public function getSeverity() {
        return $this->filters['severity'];
    }

    /**
     * @param string $severity Ticket::SEVERITY_NORMAL | Ticket::SEVERITY_HIGH | Ticket::SEVERITY_CRITICAL $severity
     * @return TicketsCriteria
     */
    public function withSeverity($severity) {
        $this->filters['severity'] = $severity;
        return $this;
    }

    /**
     * @return Ticket::PRIORITY_LOW | Ticket::PRIORITY_NORMAL | Ticket::PRIORITY_HIGH | Ticket::PRIORITY_URGENT
     */
    public function getPriority() {
        return $this->filters['priority'];
    }

    /**
     * @param string $priority Ticket::PRIORITY_LOW | Ticket::PRIORITY_NORMAL | Ticket::PRIORITY_HIGH |
     *                         Ticket::PRIORITY_URGENT $priority
     * @return TicketsCriteria
     */
    public function withPriority($priority) {
        $this->filters['priority'] = $priority;
        return $this;
    }

    /**
     * @return string
     */
    public function getAssignee() {
        return $this->filters['assignee'];
    }

    /**
     * @param string $assignee
     * @return TicketsCriteria
     */
    public function withAssignee($assignee) {
        $this->filters['assignee'] = $assignee;
        return $this;
    }

    /**
     * @return string
     */
    public function getSystemKey() {
        return $this->filters['systemKey'];
    }

    /**
     * @param string $systemKey
     * @return TicketsCriteria
     */
    public function withSystemKey($systemKey) {
        $this->filters['systemKey'] = $systemKey;
        return $this;
    }

    /**
     * @return string
     */
    public function generateQueryString() {
        return http_build_query($this->filters);
    }
}
