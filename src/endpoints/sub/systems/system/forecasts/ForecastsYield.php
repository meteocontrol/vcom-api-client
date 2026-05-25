<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems\system\forecasts;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\endpoints\sub\systems\system\forecasts\SpecificEnergy\SpecificEnergy;

class ForecastsYield extends SubEndpoint {

    public function __construct(EndpointInterface $parent) {
        $this->uri = '/yield';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    public function specificEnergy(): SpecificEnergy {
        return new SpecificEnergy($this);
    }
}
