<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\model\Responsibilities as ResponsibilitiesModel;

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
        $decodedJson = json_decode($responsibilitiesJson, true);
        return ResponsibilitiesModel::deserialize($decodedJson['data']);
    }
}
