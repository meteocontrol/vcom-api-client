<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems\system\forecasts;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\ForecastCriteria;
use meteocontrol\client\vcomapi\readers\MeasurementsBulkReader;

class Forecast extends SubEndpoint {
    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '/forecast';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @param ForecastCriteria $forecastCriteria
     * @return MeasurementsBulkReader
     */
    public function get(ForecastCriteria $forecastCriteria): MeasurementsBulkReader {
        return new MeasurementsBulkReader(
            $this->api->run($this->getUri(), $forecastCriteria->generateQueryString()),
            $forecastCriteria
        );
    }
}
