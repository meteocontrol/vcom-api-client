<?php

namespace meteocontrol\client\vcomapi\filters;

use DateTime;

class LinearEquationCriteria extends YieldLossesCriteria {

    /**
     * @param DateTime $referenceFrom
     * @return LinearEquationCriteria
     */
    public function withDateReferenceFrom(DateTime $referenceFrom): self {
        $this->filters['referenceFrom'] = $referenceFrom->format(DateTime::RFC3339);
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateReferenceFrom(): DateTime {
        return DateTime::createFromFormat(DateTime::RFC3339, $this->filters['referenceFrom']);
    }

    /**
     * @param DateTime $referenceTo
     * @return LinearEquationCriteria
     */
    public function withDateReferenceTo(DateTime $referenceTo): self {
        $this->filters['referenceTo'] = $referenceTo->format(DateTime::RFC3339);
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateReferenceTo(): DateTime {
        return DateTime::createFromFormat(DateTime::RFC3339, $this->filters['referenceTo']);
    }
}
