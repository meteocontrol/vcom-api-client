<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems\system;

use meteocontrol\vcomapi\model\MeasurementValue;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
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
    public function get(MeasurementsCriteria $criteria) {
        $measurementsJson = $this->api->run($this->getUri(), $criteria->generateQueryString());
        if ($criteria->getIntervalIncluded()) {
            if ($criteria->getResolution() !== null
                && $criteria->getResolution() !== MeasurementsCriteria::RESOLUTION_INTERVAL
            ) {
                trigger_error('"includeInterval" is only accepted with interval resolution.');
            }
            return $this->deserializeIntervalData($measurementsJson);
        }
        return $this->deserializeData($measurementsJson);
    }

    /**
     * @param string $measurementsJson
     * @return array
     */
    private function deserializeIntervalData($measurementsJson): array {
        return $this->deserializeWithIntervalVersion2Data(
            $this->jsonDecode($measurementsJson, true)['data']
        );
    }

    /**
     * @param string $measurementsJson
     * @return array
     */
    private function deserializeData($measurementsJson): array {
        return $this->deserializeVersion2Data(
            $this->jsonDecode($measurementsJson, true)['data']
        );
    }

    /**
     * @param array $data
     * @return array
     */
    private function deserializeVersion2Data(array $data) {
        $deviceMeasurements = [];
        foreach ($data as $abbreviation => $value) {
            $deviceMeasurements[$abbreviation] = MeasurementValue::deserializeArray($value);
        }
        return $deviceMeasurements;
    }

    /**
     * @param array $data
     * @return array
     */
    private function deserializeWithIntervalVersion2Data(array $data) {
        $deviceMeasurements = [];
        foreach ($data as $abbreviation => $value) {
            $deviceMeasurements[$abbreviation] = MeasurementValueWithInterval::deserializeArray($value);
        }
        return $deviceMeasurements;
    }
}
