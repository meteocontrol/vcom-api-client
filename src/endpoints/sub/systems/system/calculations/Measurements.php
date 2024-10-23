<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems\system\calculations;

use InvalidArgumentException;
use meteocontrol\client\vcomapi\endpoints\sub\systems\system\Measurements as DeviceMeasurements;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\client\vcomapi\model\MeasurementValue;

class Measurements extends DeviceMeasurements {

    /**
     * @param MeasurementsCriteria $criteria
     * @return MeasurementValue[]
     */
    public function get(MeasurementsCriteria $criteria): array {
        if ($criteria->getIntervalIncluded()) {
            throw new InvalidArgumentException('"includeInterval" is not supported for calculations.');
        }
        return parent::get($criteria);
    }
}
