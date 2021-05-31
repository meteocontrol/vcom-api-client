<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems\system\calculations;

use meteocontrol\client\vcomapi\endpoints\sub\systems\system\Abbreviation as DeviceAbbreviation;
use meteocontrol\client\vcomapi\endpoints\sub\systems\system\Measurements as DeviceMeasurements;

class Abbreviation extends DeviceAbbreviation {

    /**
     * @return Measurements
     */
    public function measurements(): DeviceMeasurements {
        return new Measurements($this);
    }
}
