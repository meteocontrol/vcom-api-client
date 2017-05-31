<?php

namespace meteocontrol\client\vcomapi\model;

class UserDetail extends BaseModel {

    /** @var int */
    public $id;
    /** @var string */
    public $title;
    /** @var string */
    public $firstName;
    /** @var string */
    public $lastName;
    /** @var string */
    public $username;
    /** @var string */
    public $email;
    /** @var string */
    public $language;
    /** @var string */
    public $company;
    /** @var string */
    public $fax;
    /** @var string */
    public $telephone;
    /** @var string */
    public $cellphone;
    /** @var ExtendedAddress */
    public $address;
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
                $classInstance->address = ExtendedAddress::deserialize($value);
            } elseif (is_array($value) && $key === "timezone") {
                $classInstance->timezone = Timezone::deserialize($value);
            } elseif (property_exists($className, $key)) {
                $classInstance->{$key} = self::getPhpValue($value);
            }
        }
        return $classInstance;
    }
}
