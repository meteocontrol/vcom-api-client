<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems\device;

use GuzzleHttp\RequestOptions;
use InvalidArgumentException;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\vcomapi\model\DevicesMeasurement;
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
        $measurementsJson = $this->api->get(
            $this->getUri(),
            [RequestOptions::QUERY => $criteria->generateQueryString()],
        );
        if ($criteria->getIntervalIncluded()) {
            if ($criteria->getResolution() !== null
                && $criteria->getResolution() !== MeasurementsCriteria::RESOLUTION_INTERVAL
            ) {
                throw new InvalidArgumentException('"includeInterval" is only accepted with interval resolution.');
            }
            return DevicesMeasurementWithInterval::deserialize($this->jsonDecode($measurementsJson, true)['data']);
        }
        return DevicesMeasurement::deserialize($this->jsonDecode($measurementsJson, true)['data']);
    }
}
