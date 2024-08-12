<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;

class FlatRate extends SubEndpoint {

    public function __construct(EndpointInterface $parent) {
        $this->uri = '/flat-rate';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    public function gridOperator(): GridOperator {
        return new GridOperator($this);
    }

    public function energyTrader(): EnergyTrader {
        return new EnergyTrader($this);
    }

    public function manual(): ManualSetpoint {
        return new ManualSetpoint($this);
    }

    public function static(): StaticSetpoint {
        return new StaticSetpoint($this);
    }
}
