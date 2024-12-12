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
     * @param DateTime $to
     * @return YieldLossesCriteria
     */
    public function withDateTo(DateTime $to): self {
        $this->filters['to'] = $to->format(DATE_ATOM);
        return $this;
    }

    /**
     * @return string
     */
    public function generateQueryString(): string {
        return http_build_query($this->filters);
    }
}
