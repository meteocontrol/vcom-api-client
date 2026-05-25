<?php

namespace meteocontrol\client\vcomapi\model;

class Measurement extends BaseModel {

    /** @var string */
    public $systemKey;

    public static function deserialize(array $data): self {
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

    public static function deserializeArray(array $decodedJsonArray): array {
        $objects = [];
        foreach ($decodedJsonArray as $item) {
            $objects[] = self::deserialize($item);
        }
        return $objects;
    }
}
