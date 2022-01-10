<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\vcomapi\model\Status;

class Statuses extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '/statuses';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return Status[]
     */
    public function get(): array {
        $json = $this->api->run($this->getUri());
        return Status::deserializeArray($this->jsonDecode($json, true)['data']);
    }

    /**
     * @return Bulk
     */
    public function bulk(): Bulk {
        return new Bulk($this);
    }
}
