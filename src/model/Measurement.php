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
        $object = new static();

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $object->{$key} = MeasurementValue::deserializeArray($value);
            } else {
                $object->{$key} = self::getPhpValue($value);
            }
        }
        return $object;
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
