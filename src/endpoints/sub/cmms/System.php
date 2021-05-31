<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\cmms;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\vcomapi\model\CmmsSystem;

class System extends SubEndpoint {

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
    public function get() {
        $systemJson = $this->api->run($this->getUri());
        $decodedJson = json_decode($systemJson, true);
        return CmmsSystem::deserializeArray($decodedJson['data']);
    }
}
