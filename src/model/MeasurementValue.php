<?php

namespace meteocontrol\client\vcomapi\model;

use meteocontrol\client\vcomapi\ApiClientException;

class MeasurementValue extends BaseModel {
    /** @var \DateTime */
    public $timestamp;
    /** @var string */
    public $value;

    public static function deserialize(array $data, $name = null) {
        $className = get_called_class();
        $classInstance = new $className();
        foreach ($data as $key => $value) {
            if ($key === "timestamp") {
                $classInstance->timestamp = self::parseTimestamp($value);
            } elseif (property_exists($className, $key)) {
                $classInstance->{$key} = self::getPhpValue($value);
            }
        }
        return $classInstance;
    }

    /**
     * @param $value
     * @return \DateTime
     * @throws ApiClientException
     */
    private static function parseTimestamp($value) {
        if (self::isYearString($value)) {
            return \DateTime::createFromFormat('Y-m-d H:i:s', $value . '-01-01 00:00:00');
        } elseif (self::isMonthString($value)) {
            return \DateTime::createFromFormat('Y-m-d H:i:s', $value . '-01 00:00:00');
        } else {
            return self::getPhpValue($value);
        }
    }

    /**
     * @param $dateString
     * @return bool
     */
    private static function isYearString($dateString) {
        return preg_match('/^[0-9]{4}$/', $dateString) == 1;
    }

    /**
     * @param $dateString
     * @return bool
     */
    private static function isMonthString($dateString) {
        return preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])$/', $dateString) == 1;
    }
}
