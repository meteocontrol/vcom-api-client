<?php

namespace meteocontrol\client\vcomapi\filters;

use DateTime;

class MeterReadingCriteria {

    public const READING_TYPE_AUTO = 'AUTO';
    public const READING_TYPE_MANUAL = 'MANUAL';
    public const READING_TYPE_ALL = 'ALL';

    /** @var array */
    private $filters = [];

    /**
     * @param DateTime $from
     * @return MeterReadingCriteria
     */
    public function withDateFrom(DateTime $from): self {
        $this->filters['from'] = $from->format(DATE_ATOM);
        return $this;
    }

    /**
     * @param DateTime $to
     * @return MeterReadingCriteria
     */
    public function withDateTo(DateTime $to) {
        $this->filters['to'] = $to->format(DATE_ATOM);
        return $this;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function withType(string $type): self {
        $this->filters['type'] = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function generateQueryString(): string {
        return http_build_query($this->filters);
    }
}
