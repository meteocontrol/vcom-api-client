<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems\calculations;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\vcomapi\model\ToleranceValue;

class ProductionTolerance extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '/production-tolerance';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @param MeasurementsCriteria $criteria
     * @return ToleranceValue[]
     */
    public function get(MeasurementsCriteria $criteria) {
        $valueJson = $this->api->run($this->getUri(), $criteria->generateQueryString());
        $decodedJson = json_decode($valueJson, true);
        return ToleranceValue::deserializeArray($decodedJson['data']);
    }
}
