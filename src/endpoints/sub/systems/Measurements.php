<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\client\vcomapi\model\Measurement;

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
     * @param MeasurementsCriteria $criteria
     * @return Measurement[]
     */
    public function get(MeasurementsCriteria $criteria) {
        $measurementsJson = $this->api->run($this->getUri(), $criteria->generateQueryString());
        $decodedJson = json_decode($measurementsJson, true);
        return Measurement::deserializeArray($decodedJson['data']);
    }
}
