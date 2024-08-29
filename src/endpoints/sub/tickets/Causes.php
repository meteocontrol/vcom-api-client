<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use GuzzleHttp\RequestOptions;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\CausesCriteria;

class Causes extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '/causes';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return string[]
     */
    public function get(CausesCriteria $causesCriteria): array {
        $causesJson = $this->api->get($this->getUri(), [RequestOptions::HEADERS => $causesCriteria->getHeaders()]);
        return $this->jsonDecode($causesJson, true)['data'];
    }
}
