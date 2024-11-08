<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems\device;

use meteocontrol\client\vcomapi\model\Abbreviation as AbbreviationModel;
use meteocontrol\client\vcomapi\model\StringboxAbbreviation as StringboxAbbreviationModel;

class StringboxAbbreviation extends Abbreviation {

    /**
     * @return StringboxAbbreviationModel
     */
    public function get(): AbbreviationModel {
        $abbreviationJson = $this->api->get($this->getUri());
        return StringboxAbbreviationModel::deserialize($this->jsonDecode($abbreviationJson, true)['data']);
    }
}
