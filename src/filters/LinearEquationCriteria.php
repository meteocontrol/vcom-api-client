<?php

namespace meteocontrol\client\vcomapi\filters;

use DateTime;

class LinearEquationCriteria extends YieldLossesCriteria {

    public function withDateReferenceFrom(DateTime $referenceFrom): self {
        $this->filters['referenceFrom'] = $referenceFrom->format(DATE_ATOM);
        return $this;
    }

    public function withDateReferenceTo(DateTime $referenceTo): self {
        $this->filters['referenceTo'] = $referenceTo->format(DATE_ATOM);
        return $this;
    }
}
