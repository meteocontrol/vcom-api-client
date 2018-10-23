<?php

namespace meteocontrol\vcomapi\model;

class UserDetail extends BaseModel {

    /** @var string */
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
    /** @var bool */
    public $hasVcom;

    /**
     * @param array $data
     * @param null|string $name
     * @return $this
     */
    public static function deserialize(array $data, $name = null) {
        $object = new static();

        foreach ($data as $key => $value) {
            if (is_array($value) && $key === "address") {
                $object->address = ExtendedAddress::deserialize($value);
            } elseif (is_array($value) && $key === "timezone") {
                $object->timezone = Timezone::deserialize($value);
            } elseif (property_exists($object, $key)) {
                $object->{$key} = self::getPhpValue($value);
            }
        }
        return $object;
    }
}
