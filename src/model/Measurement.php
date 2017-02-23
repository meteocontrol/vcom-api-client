<?php

namespace meteocontrol\client\vcomapi\model;

class Measurement extends BaseModel {

    const RESOLUTION_INTERVAL = 'interval';
    const RESOLUTION_DAY = 'day';
    const RESOLUTION_MONTH = 'month';
    const RESOLUTION_YEAR = 'year';

    /** @var string */
    public $systemKey;

    /**
     * @param array $data
     * @param null|string $name
     * @return $this
     */
    public static function deserialize(array $data, $name = null) {
        $className = get_called_class();
        $classInstance = new $className();
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $classInstance->$key = MeasurementValue::deserializeArray($value);
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
        foreach ($decodedJsonArray as $key => $item) {
            $objects[] = self::deserialize($item, $key);
        }
        return $objects;
    }
}
