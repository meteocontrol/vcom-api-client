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
        $object = new static();

        foreach ($data as $key => $value) {
            if (is_array($value) && $key === "user") {
                $object->user = UserDetail::deserialize($value);
            } elseif (property_exists($object, $key)) {
                $object->{$key} = self::getPhpValue($value);
            }
        }
        return $object;
    }
}
