<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems\system;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\endpoints\sub\systems\system\environmentalSavings\CO2;
use meteocontrol\client\vcomapi\endpoints\sub\systems\system\environmentalSavings\TreeEquivalents;

class EnvironmentalSavings extends SubEndpoint {

    public function __construct(EndpointInterface $parent) {
        $this->uri = '/environmental-savings';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    public function co2(): CO2 {
        return new CO2($this);
    }

    public function treeEquivalents(): TreeEquivalents {
        return new TreeEquivalents($this);
    }
}
