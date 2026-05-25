<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems\system;

use GuzzleHttp\RequestOptions;
use InvalidArgumentException;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\client\vcomapi\model\MeasurementValue;
use meteocontrol\client\vcomapi\model\MeasurementValueWithInterval;

class Measurements extends SubEndpoint {

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
            return $this->deserializeIntervalData($measurementsJson);
        }
        return $this->deserializeData($measurementsJson);
    }

    private function deserializeIntervalData(string $measurementsJson): array {
        $data = $this->jsonDecode($measurementsJson, true)['data'];
        $deviceMeasurements = [];
        foreach ($data as $abbreviation => $value) {
            $deviceMeasurements[$abbreviation] = MeasurementValueWithInterval::deserializeArray($value);
        }
        return $deviceMeasurements;
    }

    private function deserializeData(string $measurementsJson): array {
        $data = $this->jsonDecode($measurementsJson, true)['data'];
        $deviceMeasurements = [];
        foreach ($data as $abbreviation => $value) {
            $deviceMeasurements[$abbreviation] = MeasurementValue::deserializeArray($value);
        }
        return $deviceMeasurements;
    }
}
