<?php

namespace meteocontrol\client\vcomapi\model;

class Responsibilities extends BaseModel {

    /** @var User */
    public $owner;

    /** @var User */
    public $operator;

    /** @var User */
    public $electrician;

    /** @var User */
    public $invoiceRecipient;

    /** @var User */
    public $alarmContact;

    /**
     * @param array $data
     * @param null|string $name
     * @return $this
     */
    public static function deserialize(array $data, $name = null) {
        $className = get_called_class();
        $classInstance = new $className();
        foreach ($data as $key => $value) {
            $classInstance->{$key} = UserDetail::deserialize($value);
        }
        return $classInstance;
    }
}
