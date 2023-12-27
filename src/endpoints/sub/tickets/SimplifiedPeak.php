<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;

class SimplifiedPeak extends SubEndpoint {

    public function __construct(EndpointInterface $parent) {
        $this->uri = '/simplified-peak';
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
