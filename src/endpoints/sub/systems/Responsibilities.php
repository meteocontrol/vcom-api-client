<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\vcomapi\model\Responsibilities as ResponsibilitiesModel;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;

class Responsibilities extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '/responsibilities';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return ResponsibilitiesModel
     */
    public function get() {
        $responsibilitiesJson = $this->api->run($this->getUri());
        return ResponsibilitiesModel::deserialize($this->jsonDecode($responsibilitiesJson, true)['data']);
    }
}
