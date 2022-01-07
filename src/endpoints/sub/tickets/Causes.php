<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;

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
    public function get(): array {
        $causesJson = $this->api->run($this->getUri());
        return $this->jsonDecode($causesJson, true)['data'];
    }
}
