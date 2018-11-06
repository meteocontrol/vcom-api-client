<?php

namespace meteocontrol\vcomapi\model;

class MeasurementValue extends BaseModel {

    /** @var \DateTime */
    public $timestamp;
    /** @var string */
    public $value;

    public static function deserialize(array $data, $name = null) {
        $object = new static();
        foreach ($data as $key => $value) {
            if ($key === "timestamp") {
                $object->timestamp = self::parseTimestamp($value);
            } elseif (property_exists($object, $key)) {
                $object->{$key} = self::getPhpValue($value);
            }
        }
        return $object;
    }

    /**
     * @param string $value
     * @return \DateTime
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
     * @param string $dateString
     * @return bool
     */
    private static function isYearString($dateString) {
        return preg_match('/^[0-9]{4}$/', $dateString) == 1;
    }

    /**
     * @param string $dateString
     * @return bool
     */
    private static function isMonthString($dateString) {
        return preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])$/', $dateString) == 1;
    }
}
