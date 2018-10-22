<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems\system\calculations;

use meteocontrol\vcomapi\model\MeasurementValue;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;

class Measurements extends \meteocontrol\client\vcomapi\endpoints\sub\systems\system\Measurements {

    /**
     * @param MeasurementsCriteria $criteria
     * @return MeasurementValue[]
     */
    public function get(MeasurementsCriteria $criteria) {
        if ($criteria->getIntervalIncluded()) {
            trigger_error('"includeInterval" is not supported for calculations.');
        }
        return parent::get($criteria);
    }
}
