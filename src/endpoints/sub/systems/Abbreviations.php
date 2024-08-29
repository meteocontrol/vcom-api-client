<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;

class Abbreviations extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '/abbreviations';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return string[]
     */
    public function get(): array {
        $abbreviationJson = $this->api->get($this->getUri());
        return $this->jsonDecode($abbreviationJson, true)['data'];
    }
}
