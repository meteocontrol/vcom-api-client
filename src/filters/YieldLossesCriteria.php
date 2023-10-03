<?php

namespace meteocontrol\client\vcomapi\filters;

use DateTime;

class YieldLossesCriteria {

    /** @var string[] */
    protected $filters;

    /**
     * @param DateTime $from
     * @return YieldLossesCriteria
     */
    public function withDateFrom(DateTime $from): self {
        $this->filters['from'] = $from->format(DATE_ATOM);
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateFrom(): DateTime {
        return DateTime::createFromFormat(DATE_ATOM, $this->filters['from']);
    }

    /**
     * @param DateTime $to
     * @return YieldLossesCriteria
     */
    public function withDateTo(DateTime $to): self {
        $this->filters['to'] = $to->format(DATE_ATOM);
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateTo(): DateTime {
        return DateTime::createFromFormat(DATE_ATOM, $this->filters['to']);
    }

    /**
     * @return string
     */
    public function generateQueryString(): string {
        return http_build_query($this->filters);
    }
}
