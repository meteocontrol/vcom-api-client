<?php

namespace meteocontrol\client\vcomapi\model;

class Session extends BaseModel {

    /** @var UserDetail */
    public $user;

    public static function deserialize(array $data): self {
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
