<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems\system;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\endpoints\sub\systems\system\forecasts\Forecast;
use meteocontrol\client\vcomapi\endpoints\sub\systems\system\forecasts\ForecastsAlternativeYield;
use meteocontrol\client\vcomapi\endpoints\sub\systems\system\forecasts\ForecastsYield;

class Forecasts extends SubEndpoint {

    public function __construct(EndpointInterface $parent) {
        $this->uri = '/forecasts';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    public function forecastsYield(): ForecastsYield {
        return new ForecastsYield($this);
    }

    public function forecastsAlternativeYield(): ForecastsAlternativeYield {
        return new ForecastsAlternativeYield($this);
    }

    public function forecast(): Forecast {
        return new Forecast($this);
    }
}
