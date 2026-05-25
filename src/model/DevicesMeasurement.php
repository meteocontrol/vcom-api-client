<?php

namespace meteocontrol\client\vcomapi\model;

use ArrayAccess;
use Countable;

class DevicesMeasurement extends BaseModel implements ArrayAccess, Countable {

    /** @var MeasurementValue[] */
    protected $values = [];

    public static function deserialize(array $data): self {
        $object = new static();

        foreach ($data as $deviceId => $abbreviationMeasurements) {
            $deviceMeasurements = [];
            foreach ($abbreviationMeasurements as $abbreviation => $value) {
                $deviceMeasurements[$abbreviation] = MeasurementValue::deserializeArray($value);
            }
            $object->values[$deviceId] = $deviceMeasurements;
        }
        return $object;
    }

    public function offsetExists($offset): bool {
        return array_key_exists($offset, $this->values);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset) {
        return $this->values[$offset];
    }

    public function offsetSet($offset, $value): void {
        $this->values[$offset] = $value;
    }

    public function offsetUnset($offset): void {
        unset($this->values[$offset]);
    }

    public function count(): int {
        return count($this->values);
    }
}
