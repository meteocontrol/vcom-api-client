<?php

namespace meteocontrol\client\vcomapi\filters;

class ReferenceSystemCriteria extends YieldLossesCriteria {

    public function withReferenceSystemKey(string $referenceSystemKey): self {
        $this->filters['referenceSystemKey'] = $referenceSystemKey;
        return $this;
    }
}
