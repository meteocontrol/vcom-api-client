<?php

namespace meteocontrol\client\vcomapi\endpoints\sub;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;

class AbbreviationId extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     * @param string $id
     */
    public function __construct(EndpointInterface $parent, string $id) {
        $this->uri = '/' . $id;
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }
}
