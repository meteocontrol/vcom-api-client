<?php

declare(strict_types=1);

namespace meteocontrol\client\vcomapi\endpoints\sub\systems\system\satellite;

use GuzzleHttp\RequestOptions;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\client\vcomapi\model\MeasurementValue;

class Irradiance extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '/irradiance';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @param MeasurementsCriteria $criteria
     * @return MeasurementValue[]
     */
    public function get(MeasurementsCriteria $criteria): array {
        $json = $this->api->get($this->getUri(), [RequestOptions::QUERY => $criteria->generateQueryString()]);
        return MeasurementValue::deserializeArray($this->jsonDecode($json, true)['data']);
    }
}
