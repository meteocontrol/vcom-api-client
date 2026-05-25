<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\model\Stringbox;

class Stringboxes extends SubEndpoint {

    public function __construct(EndpointInterface $parent) {
        $this->uri = '/stringboxes';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * Stringbox[]
     */
    public function get(): array {
        $json = $this->api->get($this->getUri());
        return Stringbox::deserializeArray($this->jsonDecode($json, true)['data']);
    }

    public function bulk(): Bulk {
        return new Bulk($this);
    }
}
