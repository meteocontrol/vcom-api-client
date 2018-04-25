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
    /** @var string */
    public $currency;

    /**
     * @param array $data
     * @param null|string $name
     * @return $this
     */
    public static function deserialize(array $data, $name = null) {
        $instance = new static();

        foreach ($data as $key => $value) {
            if (is_array($value) && $key === "address") {
                $instance->address = Address::deserialize($value);
            } elseif (is_array($value) && $key === "coordinates") {
                $instance->coordinates = Coordinates::deserialize($value);
            } elseif (is_array($value) && $key === "timezone") {
                $instance->timezone = Timezone::deserialize($value);
            } elseif (property_exists($instance, $key)) {
                $instance->{$key} = self::getPhpValue($value);
            }
        }
        return $instance;
    }

    /**
     * @param \DateTime $dateTime
     * @param null|string $key
     * @return string
     */
    protected function serializeDateTime(\DateTime $dateTime, $key = null) {
        if ($key === 'commissionDate') {
            return $dateTime->format('Y-m-d');
        }
        return parent::serializeDateTime($dateTime);
    }
}
