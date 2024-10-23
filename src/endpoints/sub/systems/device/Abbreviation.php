<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems\device;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\model\Abbreviation as AbbreviationModel;

class Abbreviation extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return AbbreviationModel
     */
    public function get(): AbbreviationModel {
        $abbreviationJson = $this->api->get($this->getUri());
        return AbbreviationModel::deserialize($this->jsonDecode($abbreviationJson, true)['data']);
    }

    /**
     * @return Measurements
     */
    public function measurements(): Measurements {
        return new Measurements($this);
    }
}
