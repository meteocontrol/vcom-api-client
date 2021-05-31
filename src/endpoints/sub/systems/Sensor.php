<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\vcomapi\model\SensorDetail;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\AbbreviationId;
use meteocontrol\client\vcomapi\endpoints\sub\systems\device\Abbreviation as DeviceAbbreviation;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;

class Sensor extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return SensorDetail
     */
    public function get(): SensorDetail {
        $json = $this->api->run($this->getUri());
        return SensorDetail::deserialize($this->jsonDecode($json, true)['data']);
    }

    /**
     * @return Abbreviations
     */
    public function abbreviations(): Abbreviations {
        return new Abbreviations($this);
    }

    /**
     * @param string|array $abbreviationId
     * @return DeviceAbbreviation
     */
    public function abbreviation($abbreviationId): DeviceAbbreviation {
        $abbreviationId = is_array($abbreviationId) ? implode(',', $abbreviationId) : $abbreviationId;
        $abbreviations = new Abbreviations($this);
        $abbreviationIdEndpoint = new AbbreviationId($abbreviations, $abbreviationId);
        return new DeviceAbbreviation($abbreviationIdEndpoint);
    }
}
