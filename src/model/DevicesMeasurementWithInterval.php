<?php

namespace meteocontrol\client\vcomapi\model;

class DevicesMeasurementWithInterval extends DevicesMeasurement {

    public static function deserialize(array $data): self {
        $object = new static();

        foreach ($data as $deviceId => $abbreviationMeasurements) {
            $deviceMeasurements = [];
            foreach ($abbreviationMeasurements as $abbreviation => $value) {
                $deviceMeasurements[$abbreviation] = MeasurementValueWithInterval::deserializeArray($value);
            }
            $object->values[$deviceId] = $deviceMeasurements;
        }
        return $object;
    }
}
