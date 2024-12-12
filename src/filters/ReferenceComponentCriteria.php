<?php

namespace meteocontrol\client\vcomapi\filters;

class ReferenceComponentCriteria extends YieldLossesCriteria {

    /**
     * @param string $affectedInverterId
     * @return ReferenceComponentCriteria
     */
    public function withAffectedInverterId(string $affectedInverterId): self {
        $this->filters['affectedInverterId'] = $affectedInverterId;
        return $this;
    }

    /**
     * @param string $referenceInverterIds
     * @return ReferenceComponentCriteria
     */
    public function withReferenceInverterIds(string $referenceInverterIds): self {
        $this->filters['referenceInverterIds'] = $referenceInverterIds;
        return $this;
    }
}
