<?php

declare(strict_types=1);

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use GuzzleHttp\RequestOptions;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\InverterPrCriteria;

class Pr extends SubEndpoint {

    public function __construct(EndpointInterface $parent) {
        $this->uri = '/pr';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    public function get(InverterPrCriteria $criteria): array {
        $prJson = $this->api->get($this->getUri(), [RequestOptions::QUERY => $criteria->generateQueryString()]);
        return $this->jsonDecode($prJson, true)['data'];
    }
}
