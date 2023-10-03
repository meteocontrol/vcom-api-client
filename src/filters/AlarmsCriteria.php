<?php

declare(strict_types=1);

namespace meteocontrol\client\vcomapi\filters;

use DateTime;

class AlarmsCriteria {

    /** @var array */
    private $filters;

    /**
     * @param string $systemKey
     * @return $this
     */
    public function withSystemKey(string $systemKey): self {
        $this->filters["systemKey"][] = $systemKey;
        return $this;
    }

    /**
     * @param int $ticketId
     * @return $this
     */
    public function withTicketId(int $ticketId): self {
        $this->filters["ticketId"][] = $ticketId;
        return $this;
    }

    /**
     * @param string $alarmType
     * @return $this
     */
    public function withAlarmType(string $alarmType): self {
        $this->filters["alarmType"][] = $alarmType;
        return $this;
    }

    /**
     * @param string $componentType
     * @return $this
     */
    public function withComponentType(string $componentType): self {
        $this->filters["componentType"][] = $componentType;
        return $this;
    }

    /**
     * @param string $severity
     * @return $this
     */
    public function withSeverity(string $severity): self {
        $this->filters["severity"][] = $severity;
        return $this;
    }

    /**
     * @param string $status Alarm::STATUS_OPEN | Alarm::STATUS_CLOSED
     * @return $this
     */
    public function withStatus(string $status): self {
        $this->filters["status"][] = $status;
        return $this;
    }

    /**
     * @param DateTime $from
     * @return $this
     */
    public function withLastChangedAtFrom(DateTime $from): self {
        $this->filters["lastChangedAt[from]"] = $from->format(DATE_ATOM);
        return $this;
    }

    /**
     * @param DateTime $to
     * @return $this
     */
    public function withLastChangedAtTo(DateTime $to): self {
        $this->filters["lastChangedAt[to]"] = $to->format(DATE_ATOM);
        return $this;
    }

    /**
     * @param DateTime $from
     * @return $this
     */
    public function withCreatedAtFrom(DateTime $from): self {
        $this->filters["createdAt[from]"] = $from->format(DATE_ATOM);
        return $this;
    }

    /**
     * @param DateTime $to
     * @return $this
     */
    public function withCreatedAtTo(DateTime $to): self {
        $this->filters["createdAt[to]"] = $to->format(DATE_ATOM);
        return $this;
    }

    /**
     * @return string
     */
    public function generateQueryString(): string {
        return http_build_query(
            array_map(
                function ($filterValues) {
                    return is_array($filterValues) ? implode(",", $filterValues) : $filterValues;
                },
                $this->filters
            )
        );
    }
}
