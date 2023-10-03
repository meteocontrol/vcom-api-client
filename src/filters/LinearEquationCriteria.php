<?php

namespace meteocontrol\client\vcomapi\filters;

use DateTime;

class LinearEquationCriteria extends YieldLossesCriteria {

    /**
     * @param DateTime $referenceFrom
     * @return LinearEquationCriteria
     */
    public function withDateReferenceFrom(DateTime $referenceFrom): self {
        $this->filters['referenceFrom'] = $referenceFrom->format(DATE_ATOM);
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateReferenceFrom(): DateTime {
        return DateTime::createFromFormat(DATE_ATOM, $this->filters['referenceFrom']);
    }

    /**
     * @param DateTime $referenceTo
     * @return LinearEquationCriteria
     */
    public function withDateReferenceTo(DateTime $referenceTo): self {
        $this->filters['referenceTo'] = $referenceTo->format(DATE_ATOM);
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateReferenceTo(): DateTime {
        return DateTime::createFromFormat(DATE_ATOM, $this->filters['referenceTo']);
    }
}
