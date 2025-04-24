<?php

namespace meteocontrol\client\vcomapi\model;

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
     * @return $this
     */
    public static function deserialize(array $data): self {
        $object = new static();

        foreach ($data as $key => $value) {
            $object->{$key} = UserDetail::deserialize($value);
        }
        return $object;
    }
}
