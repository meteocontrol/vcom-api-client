<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\model\StringboxDetail;

class Stringbox extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return StringboxDetail
     */
    public function get() {
        $invertersJson = $this->api->run($this->getUri());
        $decodedJson = json_decode($invertersJson, true);
        return StringboxDetail::deserialize($decodedJson['data']);
    }
}
