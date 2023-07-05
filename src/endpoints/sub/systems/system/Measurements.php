<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems\system;

use InvalidArgumentException;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\vcomapi\model\MeasurementValue;
use meteocontrol\vcomapi\model\MeasurementValueWithInterval;

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
     * @return MeasurementValue[]
     */
    public function get(MeasurementsCriteria $criteria): array {
        $measurementsJson = $this->api->run($this->getUri(), $criteria->generateQueryString());
        if ($criteria->getIntervalIncluded()) {
            if ($criteria->getResolution() !== null
                && $criteria->getResolution() !== MeasurementsCriteria::RESOLUTION_INTERVAL
            ) {
                throw new InvalidArgumentException('"includeInterval" is only accepted with interval resolution.');
            }
            return $this->deserializeIntervalData($measurementsJson);
        }
        return $this->deserializeData($measurementsJson);
    }

    /**
     * @param string $measurementsJson
     * @return array
     */
    private function deserializeIntervalData(string $measurementsJson): array {
        $data = $this->jsonDecode($measurementsJson, true)['data'];
        $deviceMeasurements = [];
        foreach ($data as $abbreviation => $value) {
            $deviceMeasurements[$abbreviation] = MeasurementValueWithInterval::deserializeArray($value);
        }
        return $deviceMeasurements;
    }

    /**
     * @param string $measurementsJson
     * @return array
     */
    private function deserializeData(string $measurementsJson): array {
        $data = $this->jsonDecode($measurementsJson, true)['data'];
        $deviceMeasurements = [];
        foreach ($data as $abbreviation => $value) {
            $deviceMeasurements[$abbreviation] = MeasurementValue::deserializeArray($value);
        }
        return $deviceMeasurements;
    }
}
