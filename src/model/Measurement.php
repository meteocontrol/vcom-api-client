<?php

namespace meteocontrol\vcomapi\model;

use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;

class Measurement extends BaseModel {

    /** @deprecated */
    const RESOLUTION_INTERVAL = MeasurementsCriteria::RESOLUTION_INTERVAL;
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
        $instance = new static();

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $instance->{$key} = MeasurementValue::deserializeArray($value);
            } else {
                $instance->{$key} = self::getPhpValue($value);
            }
        }
        return $instance;
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
