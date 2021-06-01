<?php

namespace meteocontrol\vcomapi\model;

use ArrayAccess;
use Countable;

class DevicesMeasurement extends BaseModel implements ArrayAccess, Countable {

    /** @var MeasurementValue[] */
    protected $values = [];

    /**
     * @param array $data
     * @param null|string $name
     * @return $this
     */
    public static function deserialize(array $data, $name = null) {
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

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset): bool {
        return array_key_exists($offset, $this->values);
    }

    /**
     * @param mixed $offset
     */
    public function offsetGet($offset) {
        return $this->values[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value): void {
        $this->values[$offset] = $value;
    }

    /**
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset): void {
        unset($this->values[$offset]);
    }

    /**
     * @return int
     */
    public function count(): int {
        return count($this->values);
    }
}
