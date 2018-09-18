<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\vcomapi\model\BatteryDetail;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\AbbreviationId;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\endpoints\sub\systems\device\Abbreviation as DeviceAbbreviation;

class Battery extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return BatteryDetail
     */
    public function get() {
        $batteryJson = $this->api->run($this->getUri());
        return BatteryDetail::deserialize($this->jsonDecode($batteryJson, true)['data']);
    }

    /**
     * @return Abbreviations
     */
    public function abbreviations() {
        return new Abbreviations($this);
    }

    /**
     * @param string|array $abbreviationId
     * @return DeviceAbbreviation
     */
    public function abbreviation($abbreviationId) {
        $abbreviationId = is_array($abbreviationId) ? implode(',', $abbreviationId) : $abbreviationId;
        $abbreviations = new Abbreviations($this);
        $abbreviationIdEndpoint = new AbbreviationId($abbreviations, $abbreviationId);
        $abbreviationEndpoint = new DeviceAbbreviation($abbreviationIdEndpoint);
        return $abbreviationEndpoint;
    }
}
