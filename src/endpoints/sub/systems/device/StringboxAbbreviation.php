<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems\device;

use meteocontrol\client\vcomapi\model\StringboxAbbreviation as StringboxAbbreviationModel;

class StringboxAbbreviation extends Abbreviation {

    public function get(): StringboxAbbreviationModel {
        $abbreviationJson = $this->api->get($this->getUri());
        return StringboxAbbreviationModel::deserialize($this->jsonDecode($abbreviationJson, true)['data']);
    }
}
