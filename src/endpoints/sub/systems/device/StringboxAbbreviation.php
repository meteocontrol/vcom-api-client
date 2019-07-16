<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems\device;

use meteocontrol\vcomapi\model\StringboxAbbreviation as AbbreviationModel;

class StringboxAbbreviation extends Abbreviation {

    /**
     * @return AbbreviationModel
     */
    public function get() {
        $abbreviationJson = $this->api->run($this->getUri());
        return AbbreviationModel::deserialize($this->jsonDecode($abbreviationJson, true)['data']);
    }
}
