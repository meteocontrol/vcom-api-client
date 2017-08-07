<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\model\Stringbox;

class Stringboxes extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '/stringboxes';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * Stringbox[]
     */
    public function get() {
        $invertersJson = $this->api->run($this->getUri());
        $decodedJson = json_decode($invertersJson, true);
        return Stringbox::deserializeArray($decodedJson['data']);
    }

    /**
     * @return Bulk
     */
    public function bulk() {
        return new Bulk($this);
    }
}
