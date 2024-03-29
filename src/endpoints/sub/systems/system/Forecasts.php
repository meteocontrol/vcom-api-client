<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems\system;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\endpoints\sub\systems\system\forecasts\Forecast;
use meteocontrol\client\vcomapi\endpoints\sub\systems\system\forecasts\ForecastsYield;

class Forecasts extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '/forecasts';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return ForecastsYield
     */
    public function forecastsYield(): ForecastsYield {
        return new ForecastsYield($this);
    }

    /**
     * @return Forecast
     */
    public function forecast(): Forecast {
        return new Forecast($this);
    }
}
