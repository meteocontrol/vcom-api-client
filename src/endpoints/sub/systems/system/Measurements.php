<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems\system;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\client\vcomapi\model\MeasurementValue;

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
     * @return MeasurementValue[]
     */
    public function get(MeasurementsCriteria $criteria) {
        $measurementsJson = $this->api->run($this->getUri(), $criteria->generateQueryString());
        $decodedJson = json_decode($measurementsJson, true);
        return MeasurementValue::deserializeArray($decodedJson['data']);
    }
}
