<?php

declare(strict_types=1);

namespace meteocontrol\client\vcomapi\filters;

use DateTime;

class InverterPrCriteria extends MeasurementsCriteria {

    public function getDateFrom(): DateTime {
        return DateTime::createFromFormat('Y-m-d', $this->filters['from']);
    }

    public function withDateFrom(DateTime $from): self {
        $this->filters['from'] = $from->format('Y-m-d');
        return $this;
    }

    public function getDateTo(): DateTime {
        return DateTime::createFromFormat('Y-m-d', $this->filters['to']);
    }

    public function withDateTo(DateTime $to): self {
        $this->filters['to'] = $to->format('Y-m-d');
        return $this;
    }
}
