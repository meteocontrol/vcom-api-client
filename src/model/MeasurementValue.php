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
            if (property_exists($object, $key)) {
                $object->{$key} = self::getPhpValue($value);
            }
        }
        return $object;
    }
}
