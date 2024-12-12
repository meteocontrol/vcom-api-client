<?php

declare(strict_types=1);

namespace meteocontrol\client\vcomapi\filters;

use DateTime;

class InverterPrCriteria extends MeasurementsCriteria {

    public function withDateFrom(DateTime $from): self {
        $this->filters['from'] = $from->format('Y-m-d');
        return $this;
    }

    public function withDateTo(DateTime $to): self {
        $this->filters['to'] = $to->format('Y-m-d');
        return $this;
    }
}
