<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;

class YieldLosses extends SubEndpoint {

    public function __construct(EndpointInterface $parent) {
        $this->uri = '/yield-losses';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    public function referenceSystem(): ReferenceSystem {
        return new ReferenceSystem($this);
    }

    public function referenceComponent(): ReferenceComponent {
        return new ReferenceComponent($this);
    }

    public function linearEquation(): LinearEquation {
        return new LinearEquation($this);
    }

    public function flatRate(): FlatRate {
        return new FlatRate($this);
    }

    /**
     * @deprecated It is scheduled to be removed on 2024-06-30.
     */
    public function flatRateBefore2021(): FlatRateBefore2021 {
        return new FlatRateBefore2021($this);
    }

    public function peak(): Peak {
        return new Peak($this);
    }

    /**
     * @deprecated It is scheduled to be removed on 2024-06-30.
     */
    public function peakBefore2021(): PeakBefore2021 {
        return new PeakBefore2021($this);
    }

    public function simplifiedPeak(): SimplifiedPeak {
        return new SimplifiedPeak($this);
    }
}
