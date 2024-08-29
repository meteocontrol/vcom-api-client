<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems\calculations;

use GuzzleHttp\RequestOptions;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\vcomapi\model\SimulationValue;

class Simulation extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '/simulation';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @param MeasurementsCriteria $criteria
     * @return SimulationValue[]
     */
    public function get(MeasurementsCriteria $criteria): array {
        $valueJson = $this->api->get($this->getUri(), [RequestOptions::QUERY => $criteria->generateQueryString()]);
        $decodedJson = json_decode($valueJson, true);
        return SimulationValue::deserializeArray($decodedJson['data']);
    }
}
