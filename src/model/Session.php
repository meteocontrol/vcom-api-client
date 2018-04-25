<?php

namespace meteocontrol\vcomapi\model;

class Session extends BaseModel {

    /** @var UserDetail */
    public $user;

    /**
     * @param array $data
     * @param null|string $name
     * @return $this
     */
    public static function deserialize(array $data, $name = null) {
        $instance = new static();

        foreach ($data as $key => $value) {
            if (is_array($value) && $key === "user") {
                $instance->user = UserDetail::deserialize($value);
            } elseif (property_exists($instance, $key)) {
                $instance->{$key} = self::getPhpValue($value);
            }
        }
        return $instance;
    }
}
