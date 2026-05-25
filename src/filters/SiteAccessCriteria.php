<?php

namespace meteocontrol\client\vcomapi\filters;

use DateTime;

class SiteAccessCriteria {

    /** @var array */
    private $filters = [];

    /**
     * @param array | string $systemKey
     */
    public function withSystemKey($systemKey): self {
        $this->filters['systemKey'] = is_array($systemKey) ? implode(',', $systemKey) : $systemKey;
        return $this;
    }

    public function withDateCheckIn(DateTime $checkIn): self {
        $this->filters['checkIn'] = $checkIn->format(DATE_ATOM);
        return $this;
    }

    public function withDateCheckOut(DateTime $checkOut) {
        $this->filters['checkOut'] = $checkOut->format(DATE_ATOM);
        return $this;
    }

    /**
     * @param array | string $status SiteAccess::STATUS_UNREGISTERED | SiteAccess::STATUS_REGISTERED |
     *                       SiteAccess::STATUS_EXCEEDED
     */
    public function withStatus($status): self {
        $this->filters['status'] = is_array($status) ? implode(',', $status) : $status;
        return $this;
    }

    public function generateQueryString(): string {
        return http_build_query($this->filters);
    }
}
