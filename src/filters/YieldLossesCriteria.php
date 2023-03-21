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
        $this->filters['from'] = $from->format(DateTime::RFC3339);
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateFrom(): DateTime {
        return DateTime::createFromFormat(DateTime::RFC3339, $this->filters['from']);
    }

    /**
     * @param DateTime $to
     * @return YieldLossesCriteria
     */
    public function withDateTo(DateTime $to): self {
        $this->filters['to'] = $to->format(DateTime::RFC3339);
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateTo(): DateTime {
        return DateTime::createFromFormat(DateTime::RFC3339, $this->filters['to']);
    }

    /**
     * @return string
     */
    public function generateQueryString(): string {
        return http_build_query($this->filters);
    }
}
