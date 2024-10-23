<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\model\PowerPlantController;

class PowerPlantControllers extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '/power-plant-controllers';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return PowerPlantController[]
     */
    public function get(): array {
        $json = $this->api->get($this->getUri());
        return PowerPlantController::deserializeArray($this->jsonDecode($json, true)['data']);
    }

    /**
     * @return Bulk
     */
    public function bulk() {
        return new Bulk($this);
    }
}
