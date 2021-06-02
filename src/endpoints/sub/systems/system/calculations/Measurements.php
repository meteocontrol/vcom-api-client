<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems\system\calculations;

use meteocontrol\vcomapi\model\MeasurementValue;
use meteocontrol\client\vcomapi\endpoints\sub\systems\system\Measurements as DeviceMeasurements;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;

class Measurements extends DeviceMeasurements {

    /**
     * @param MeasurementsCriteria $criteria
     * @return MeasurementValue[]
     */
    public function get(MeasurementsCriteria $criteria): array {
        if ($criteria->getIntervalIncluded()) {
            trigger_error('"includeInterval" is not supported for calculations.');
        }
        return parent::get($criteria);
    }
}
