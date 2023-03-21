<?php

namespace meteocontrol\client\vcomapi\filters;

class ReferenceSystemCriteria extends YieldLossesCriteria {

    /**
     * @param string $referenceSystemKey
     * @return ReferenceSystemCriteria
     */
    public function withReferenceSystemKey(string $referenceSystemKey): self {
        $this->filters['referenceSystemKey'] = $referenceSystemKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getReferenceSystemKey(): string {
        return $this->filters['referenceSystemKey'];
    }
}
