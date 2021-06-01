<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems\device;

use meteocontrol\vcomapi\model\DevicesMeasurement;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\vcomapi\model\DevicesMeasurementWithInterval;

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
     * @return DevicesMeasurement|DevicesMeasurementWithInterval
     */
    public function get(MeasurementsCriteria $criteria) {
        $measurementsJson = $this->api->run($this->getUri(), $criteria->generateQueryString());
        if ($criteria->getIntervalIncluded()) {
            if ($criteria->getResolution() !== null
                && $criteria->getResolution() !== MeasurementsCriteria::RESOLUTION_INTERVAL
            ) {
                trigger_error('"includeInterval" is only accepted with interval resolution.');
            }
            return DevicesMeasurementWithInterval::deserialize($this->jsonDecode($measurementsJson, true)['data']);
        }
        return DevicesMeasurement::deserialize($this->jsonDecode($measurementsJson, true)['data']);
    }
}
