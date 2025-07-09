<?php

namespace meteocontrol\client\vcomapi\filters;

use DateTime;

class SiteAccessCriteria {
    /** @var array */
    private $filters = [];

    /**
     * @param array | string $systemKey
     * @return SiteAccessCriteria
     */
    public function withSystemKey($systemKey): self {
        $this->filters['systemKey'] = is_array($systemKey) ? implode(',', $systemKey) : $systemKey;
        return $this;
    }

    /**
     * @param DateTime $checkIn
     * @return SiteAccessCriteria
     */
    public function withDateCheckIn(DateTime $checkIn): self {
        $this->filters['checkIn'] = $checkIn->format(DATE_ATOM);
        return $this;
    }

    /**
     * @param DateTime $checkOut
     * @return SiteAccessCriteria
     */
    public function withDateCheckOut(DateTime $checkOut) {
        $this->filters['checkOut'] = $checkOut->format(DATE_ATOM);
        return $this;
    }

    /**
     * @param array | string $status SiteAccess::STATUS_UNREGISTERED | SiteAccess::STATUS_REGISTERED |
     *                       SiteAccess::STATUS_EXCEEDED
     * @return SiteAccessCriteria
     */
    public function withStatus($status): self {
        $this->filters['status'] = is_array($status) ? implode(',', $status) : $status;
        return $this;
    }

    /**
     * @return string
     */
    public function generateQueryString(): string {
        return http_build_query($this->filters);
    }
}
