<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;

/**
 * @deprecated It is scheduled to be removed on 2024-06-30.
 */
class FlatRateBefore2021 extends SubEndpoint {

    public function __construct(EndpointInterface $parent) {
        $this->uri = '/flat-rate-before-2021';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    public function gridOperator(): GridOperator {
        return new GridOperator($this);
    }

    public function energyTrader(): EnergyTrader {
        return new EnergyTrader($this);
    }
}
