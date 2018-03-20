<?php

namespace meteocontrol\vcomapi\model;

class SystemDetail extends BaseModel {

    /** @var Address */
    public $address;

    /** @var int */
    public $elevation;

    /** @var \DateTime */
    public $commissionDate;

    /** @var Coordinates */
    public $coordinates;

    /** @var string */
    public $name;

    /** @var Timezone */
    public $timezone;

    /**
     * @param array $data
     * @param null|string $name
     * @return $this
     */
    public static function deserialize(array $data, $name = null) {
        $className = get_called_class();
        $classInstance = new $className();
        foreach ($data as $key => $value) {
            if (is_array($value) && $key === "address") {
                $classInstance->address = Address::deserialize($value);
            } elseif (is_array($value) && $key === "coordinates") {
                $classInstance->coordinates = Coordinates::deserialize($value);
            } elseif (is_array($value) && $key === "timezone") {
                $classInstance->timezone = Timezone::deserialize($value);
            } elseif (property_exists($className, $key)) {
                $classInstance->{$key} = self::getPhpValue($value);
            }
        }
        return $classInstance;
    }
}
