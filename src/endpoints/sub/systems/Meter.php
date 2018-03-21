<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\vcomapi\model\MeterDetail;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\AbbreviationId;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\endpoints\sub\systems\device\Abbreviation as DeviceAbbreviation;

class Meter extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return MeterDetail
     */
    public function get() {
        $meterJson = $this->api->run($this->getUri());
        $decodedJson = json_decode($meterJson, true);
        return MeterDetail::deserialize($decodedJson['data']);
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
