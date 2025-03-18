<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\model\YieldLossCalculation;

class YieldLosses extends SubEndpoint {

    public function __construct(EndpointInterface $parent) {
        $this->uri = '/yield-losses';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    public function get(): YieldLossCalculation {
        $json = $this->api->get($this->getUri());
        return YieldLossCalculation::deserialize($this->jsonDecode($json, true)['data']);
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

    public function peak(): Peak {
        return new Peak($this);
    }

    public function simplifiedPeak(): SimplifiedPeak {
        return new SimplifiedPeak($this);
    }

    public function simulation(): Simulation {
        return new Simulation($this);
    }
}
