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
        $className = get_called_class();
        $classInstance = new $className();
        foreach ($data as $key => $value) {
            if (is_array($value) && $key === "user") {
                $classInstance->user = UserDetail::deserialize($value);
            } elseif (property_exists($className, $key)) {
                $classInstance->{$key} = self::getPhpValue($value);
            }
        }
        return $classInstance;
    }
}
