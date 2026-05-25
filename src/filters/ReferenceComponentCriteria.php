<?php

namespace meteocontrol\client\vcomapi\filters;

class ReferenceComponentCriteria extends YieldLossesCriteria {

    public function withAffectedInverterId(string $affectedInverterId): self {
        $this->filters['affectedInverterId'] = $affectedInverterId;
        return $this;
    }

    public function withReferenceInverterIds(string $referenceInverterIds): self {
        $this->filters['referenceInverterIds'] = $referenceInverterIds;
        return $this;
    }
}
