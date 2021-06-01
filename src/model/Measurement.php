<?php

namespace meteocontrol\vcomapi\model;

class Measurement extends BaseModel {

    /** @var string */
    public $systemKey;

    /**
     * @param array $data
     * @return $this
     */
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

    /**
     * @param array $decodedJsonArray
     * @return array
     */
    public static function deserializeArray(array $decodedJsonArray): array {
        $objects = [];
        foreach ($decodedJsonArray as $item) {
            $objects[] = self::deserialize($item);
        }
        return $objects;
    }
}
