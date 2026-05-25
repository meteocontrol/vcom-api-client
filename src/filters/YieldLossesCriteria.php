<?php

namespace meteocontrol\client\vcomapi\filters;

use DateTime;

class YieldLossesCriteria {

    /** @var string[] */
    protected $filters = [];

    public function withDateFrom(DateTime $from): self {
        $this->filters['from'] = $from->format(DATE_ATOM);
        return $this;
    }

    public function withDateTo(DateTime $to): self {
        $this->filters['to'] = $to->format(DATE_ATOM);
        return $this;
    }

    public function withResolution(int $resolution): self {
        $this->filters['resolution'] = $resolution;
        return $this;
    }

    public function generateQueryString(): string {
        return http_build_query($this->filters);
    }
}
