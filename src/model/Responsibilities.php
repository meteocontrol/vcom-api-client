<?php

namespace meteocontrol\vcomapi\model;

class Responsibilities extends BaseModel {

    /** @var UserDetail */
    public $owner;
    /** @var UserDetail */
    public $operator;
    /** @var UserDetail */
    public $electrician;
    /** @var UserDetail */
    public $invoiceRecipient;
    /** @var UserDetail */
    public $alarmContact;

    /**
     * @param array $data
     * @param null|string $name
     * @return $this
     */
    public static function deserialize(array $data, $name = null): self {
        $object = new static();

        foreach ($data as $key => $value) {
            $object->{$key} = UserDetail::deserialize($value);
        }
        return $object;
    }
}
