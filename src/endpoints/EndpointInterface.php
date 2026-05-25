<?php

namespace meteocontrol\client\vcomapi\endpoints;

use meteocontrol\client\vcomapi\ApiClient;

interface EndpointInterface {

    public function getUri(): string;

    public function getApiClient(): ApiClient;
}
