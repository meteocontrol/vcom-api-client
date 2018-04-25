<?php

namespace meteocontrol\vcomapi\model;

use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;

class DevicesMeasurement extends BaseModel implements \ArrayAccess, \Countable {

    /** @deprecated */
    const RESOLUTION_INTERVAL = MeasurementsCriteria::RESOLUTION_INTERVAL;
    /** @deprecated */
    const RESOLUTION_DAY = MeasurementsCriteria::RESOLUTION_DAY;
    /** @deprecated */
    const RESOLUTION_MONTH = MeasurementsCriteria::RESOLUTION_MONTH;
    /** @deprecated */
    const RESOLUTION_YEAR = MeasurementsCriteria::RESOLUTION_YEAR;

    /** @var MeasurementValue[] */
    private $values = [];

    /**
     * @param array $data
     * @param null|string $name
     * @return $this
     */
    public static function deserialize(array $data, $name = null) {
        $instance = new static();

        foreach ($data as $deviceId => $abbreviationMeasurements) {
            $deviceMeasurements = [];
            foreach ($abbreviationMeasurements as $abbreviation => $value) {
                $deviceMeasurements[$abbreviation] = MeasurementValue::deserializeArray($value);
            }
            $instance->values[$deviceId] = $deviceMeasurements;
        }
        return $instance;
    }

    /**
     * Whether a offset exists
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param string $offset <p>
     * An offset to check for.
     * </p>
     * @return bool true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset) {
        return array_key_exists($offset, $this->values);
    }

    /**
     * Offset to retrieve
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param string $offset <p>
     * The offset to retrieve.
     * </p>
     * @return MeasurementValue[]
     * @since 5.0.0
     */
    public function offsetGet($offset) {
        return $this->values[$offset];
    }

    /**
     * Offset to set
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param string $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value) {
        $this->values[$offset] = $value;
    }

    /**
     * Offset to unset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param string $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset) {
        unset($this->values[$offset]);
    }

    /**
     * Count elements of an object
     *
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an int.
     * </p>
     * <p>
     * The return value is cast to an int.
     * @since 5.1.0
     */
    public function count() {
        return count($this->values);
    }
}
