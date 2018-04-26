<?php

namespace meteocontrol\vcomapi\model;

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
        $object = new static();

        foreach ($data as $key => $value) {
            $object->{$key} = UserDetail::deserialize($value);
        }
        return $object;
    }
}
