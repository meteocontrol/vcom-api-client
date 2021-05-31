<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems\system\calculations;

class Abbreviation extends \meteocontrol\client\vcomapi\endpoints\sub\systems\system\Abbreviation {

    /**
     * @return Measurements
     */
    public function measurements() {
        return new Measurements($this);
    }
}
