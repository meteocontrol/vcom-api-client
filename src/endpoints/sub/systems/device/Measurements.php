<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems\device;

use meteocontrol\vcomapi\model\DevicesMeasurement;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;

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
     * @return DevicesMeasurement
     */
    public function get(MeasurementsCriteria $criteria) {
        $measurementsJson = $this->api->run($this->getUri(), $criteria->generateQueryString());
        $decodedJson = json_decode($measurementsJson, true);
        return DevicesMeasurement::deserialize($decodedJson['data']);
    }
}
