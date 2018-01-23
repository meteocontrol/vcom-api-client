<?php

namespace meteocontrol\client\vcomapi\model;

use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;

class Measurement extends BaseModel {

    /** @deprecated */
    const RESOLUTION_INTERVAL = MeasurementsCriteria::RESOLUTION_INTERVAL;
    /** @deprecated */
    const RESOLUTION_HOUR = MeasurementsCriteria::RESOLUTION_HOUR;
    /** @deprecated */
    const RESOLUTION_DAY = MeasurementsCriteria::RESOLUTION_DAY;
    /** @deprecated */
    const RESOLUTION_MONTH = MeasurementsCriteria::RESOLUTION_MONTH;
    /** @deprecated */
    const RESOLUTION_YEAR = MeasurementsCriteria::RESOLUTION_YEAR;

    /** @var string */
    public $systemKey;

    /**
     * @param array $data
     * @return $this
     */
    public static function deserialize(array $data) {
        $className = get_called_class();
        $classInstance = new $className();
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $classInstance->{$key} = MeasurementValue::deserializeArray($value);
            } else {
                $classInstance->{$key} = self::getPhpValue($value);
            }
        }
        return $classInstance;
    }

    /**
     * @param array $decodedJsonArray
     * @return array
     */
    public static function deserializeArray(array $decodedJsonArray) {
        $objects = [];
        foreach ($decodedJsonArray as $item) {
            $objects[] = self::deserialize($item);
        }
        return $objects;
    }
}
