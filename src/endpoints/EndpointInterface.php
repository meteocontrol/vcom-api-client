<?php

namespace meteocontrol\client\vcomapi\endpoints;

use meteocontrol\client\vcomapi\ApiClient;

interface EndpointInterface {

    /**
     * @return string
     */
    public function getUri(): string;

    /**
     * @return ApiClient
     */
    public function getApiClient(): ApiClient;
}
