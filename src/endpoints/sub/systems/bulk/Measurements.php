<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems\bulk;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\client\vcomapi\readers\MeasurementsBulkReader;

class Measurements extends SubEndpoint {
    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '/measurements';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return MeasurementsBulkReader
     */
    public function get(MeasurementsCriteria $criteria) {
        return new MeasurementsBulkReader(
            $this->api->run($this->getUri(), $criteria->generateQueryString()),
            $criteria
        );
    }
}
