<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\cmms;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\model\CmmsSystem;

class Systems extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '/systems';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return CmmsSystem[]
     */
    public function get(): array {
        $systemJson = $this->api->get($this->getUri());
        $decodedJson = json_decode($systemJson, true);
        return CmmsSystem::deserializeArray($decodedJson['data']);
    }
}
