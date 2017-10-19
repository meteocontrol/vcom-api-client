<?php

namespace meteocontrol\client\vcomapi\filters;

use meteocontrol\client\vcomapi\model\Ticket;

class TicketsCriteria {

    /** @var string[] */
    private $filters;

    /**
     * @deprecated
     * @return \DateTime
     */
    public function getLastChangeFrom() {
        return \DateTime::createFromFormat(\DateTime::RFC3339, $this->filters['lastChangedAt[from]']);
    }

    /**
     * @return \DateTime
     */
    public function getLastChangedAtFrom() {
        return \DateTime::createFromFormat(\DateTime::RFC3339, $this->filters['lastChangedAt[from]']);
    }

    /**
     * @deprecated
     * @param \DateTime $from
     * @return TicketsCriteria
     */
    public function withLastChangeFrom(\DateTime $from) {
        $this->filters['lastChangedAt[from]'] = $from->format(\DateTime::RFC3339);
        return $this;
    }

    /**
     * @param \DateTime $from
     * @return TicketsCriteria
     */
    public function withLastChangedAtFrom(\DateTime $from) {
        $this->filters['lastChangedAt[from]'] = $from->format(\DateTime::RFC3339);
        return $this;
    }

    /**
     * @deprecated
     * @return \DateTime
     */
    public function getLastChangeTo() {
        return \DateTime::createFromFormat(\DateTime::RFC3339, $this->filters['lastChangedAt[to]']);
    }

    /**
     * @return \DateTime
     */
    public function getLastChangedAtTo() {
        return \DateTime::createFromFormat(\DateTime::RFC3339, $this->filters['lastChangedAt[to]']);
    }

    /**
     * @deprecated
     * @param \DateTime $to
     * @return TicketsCriteria
     */
    public function withLastChangeTo(\DateTime $to) {
        $this->filters['lastChangedAt[to]'] = $to->format(\DateTime::RFC3339);
        return $this;
    }

    /**
     * @param \DateTime $to
     * @return TicketsCriteria
     */
    public function withLastChangedAtTo(\DateTime $to) {
        $this->filters['lastChangedAt[to]'] = $to->format(\DateTime::RFC3339);
        return $this;
    }

    /**
     * @deprecated
     * @return \DateTime
     */
    public function getDateFrom() {
        return \DateTime::createFromFormat(\DateTime::RFC3339, $this->filters['createdAt[from]']);
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAtFrom() {
        return \DateTime::createFromFormat(\DateTime::RFC3339, $this->filters['createdAt[from]']);
    }

    /**
     * @deprecated
     * @param \DateTime $from
     * @return TicketsCriteria
     */
    public function withDateFrom(\DateTime $from) {
        $this->filters['createdAt[from]'] = $from->format(\DateTime::RFC3339);
        return $this;
    }

    /**
     * @param \DateTime $from
     * @return TicketsCriteria
     */
    public function withCreatedAtFrom(\DateTime $from) {
        $this->filters['createdAt[from]'] = $from->format(\DateTime::RFC3339);
        return $this;
    }

    /**
     * @deprecated
     * @return \DateTime
     */
    public function getDateTo() {
        return \DateTime::createFromFormat(\DateTime::RFC3339, $this->filters['createdAt[to]']);
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAtTo() {
        return \DateTime::createFromFormat(\DateTime::RFC3339, $this->filters['createdAt[to]']);
    }

    /**
     * @deprecated
     * @param \DateTime $to
     * @return TicketsCriteria
     */
    public function withDateTo(\DateTime $to) {
        $this->filters['createdAt[to]'] = $to->format(\DateTime::RFC3339);
        return $this;
    }

    /**
     * @param \DateTime $to
     * @return TicketsCriteria
     */
    public function withCreatedAtTo(\DateTime $to) {
        $this->filters['createdAt[to]'] = $to->format(\DateTime::RFC3339);
        return $this;
    }

    /**
     * @deprecated
     * @return \DateTime
     */
    public function getRectifiedOnFrom() {
        return \DateTime::createFromFormat(\DateTime::RFC3339, $this->filters['rectifiedAt[from]']);
    }

    /**
     * @return \DateTime
     */
    public function getRectifiedAtFrom() {
        return \DateTime::createFromFormat(\DateTime::RFC3339, $this->filters['rectifiedAt[from]']);
    }

    /**
     * @deprecated
     * @param \DateTime $from
     * @return TicketsCriteria
     */
    public function withRectifiedOnFrom(\DateTime $from) {
        $this->filters['rectifiedAt[from]'] = $from->format(\DateTime::RFC3339);
        return $this;
    }

    /**
     * @param \DateTime $from
     * @return TicketsCriteria
     */
    public function withRectifiedAtFrom(\DateTime $from) {
        $this->filters['rectifiedAt[from]'] = $from->format(\DateTime::RFC3339);
        return $this;
    }

    /**
     * @deprecated
     * @return \DateTime
     */
    public function getRectifiedOnTo() {
        return \DateTime::createFromFormat(\DateTime::RFC3339, $this->filters['rectifiedAt[to]']);
    }

    /**
     * @return \DateTime
     */
    public function getRectifiedAtTo() {
        return \DateTime::createFromFormat(\DateTime::RFC3339, $this->filters['rectifiedAt[to]']);
    }

    /**
     * @deprecated
     * @param \DateTime $to
     * @return TicketsCriteria
     */
    public function withRectifiedOnTo(\DateTime $to) {
        $this->filters['rectifiedAt[to]'] = $to->format(\DateTime::RFC3339);
        return $this;
    }

    /**
     * @param \DateTime $to
     * @return TicketsCriteria
     */
    public function withRectifiedAtTo(\DateTime $to) {
        $this->filters['rectifiedAt[to]'] = $to->format(\DateTime::RFC3339);
        return $this;
    }

    /**
     * @return Ticket::REPORT_TYPE_NO | Ticket::REPORT_TYPE_DETAIL | Ticket::REPORT_TYPE_SUMMARY
     */
    public function getIncludeInReports() {
        return $this->filters['includeInReports'];
    }

    /**
     * @param array | Ticket::REPORT_TYPE_NO | Ticket::REPORT_TYPE_DETAIL | Ticket::REPORT_TYPE_SUMMARY $type
     * @return TicketsCriteria
     */
    public function withIncludeInReports($type) {
        $this->filters['includeInReports'] = is_array($type) ? implode(",", $type) : $type;
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
     * @param array | string $status Ticket::STATUS_OPEN | Ticket::STATUS_CLOSED | Ticket::STATUS_DELETED |
     *                       Ticket::STATUS_ASSIGNED | Ticket::STAUTS_INPROGRESS $status
     * @return TicketsCriteria
     */
    public function withStatus($status) {
        $this->filters['status'] = is_array($status) ? implode(",", $status) : $status;
        return $this;
    }

    /**
     * @return Ticket::SEVERITY_NORMAL | Ticket::SEVERITY_HIGH | Ticket::SEVERITY_CRITICAL
     */
    public function getSeverity() {
        return $this->filters['severity'];
    }

    /**
     * @param array | string $severity Ticket::SEVERITY_NORMAL | Ticket::SEVERITY_HIGH |
     *                       Ticket::SEVERITY_CRITICAL $severity
     * @return TicketsCriteria
     */
    public function withSeverity($severity) {
        $this->filters['severity'] = is_array($severity) ? implode(",", $severity) : $severity;
        return $this;
    }

    /**
     * @return Ticket::PRIORITY_LOW | Ticket::PRIORITY_NORMAL | Ticket::PRIORITY_HIGH | Ticket::PRIORITY_URGENT
     */
    public function getPriority() {
        return $this->filters['priority'];
    }

    /**
     * @param array | string $priority Ticket::PRIORITY_LOW | Ticket::PRIORITY_NORMAL | Ticket::PRIORITY_HIGH |
     *                         Ticket::PRIORITY_URGENT $priority
     * @return TicketsCriteria
     */
    public function withPriority($priority) {
        $this->filters['priority'] = is_array($priority) ? implode(",", $priority) : $priority;
        return $this;
    }

    /**
     * @return string
     */
    public function getAssignee() {
        return $this->filters['assignee'];
    }

    /**
     * @param array | string $assignee
     * @return TicketsCriteria
     */
    public function withAssignee($assignee) {
        $this->filters['assignee'] = is_array($assignee) ? implode(",", $assignee) : $assignee;
        return $this;
    }

    /**
     * @return string
     */
    public function getSystemKey() {
        return $this->filters['systemKey'];
    }

    /**
     * @param array | string $systemKey
     * @return TicketsCriteria
     */
    public function withSystemKey($systemKey) {
        $this->filters['systemKey'] = is_array($systemKey) ? implode(",", $systemKey) : $systemKey;
        return $this;
    }

    /**
     * @return string
     */
    public function generateQueryString() {
        return http_build_query($this->filters);
    }
}
