<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\endpoints\sub\systems\bulk\Measurements as BulkMeasurements;

class Bulk extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '/bulk';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return BulkMeasurements
     */
    public function measurements() {
        return new BulkMeasurements($this);
    }
}
