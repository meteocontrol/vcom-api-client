<?php

namespace meteocontrol\client\vcomapi\model;

use DateTime;

class SiteAccess extends BaseModel {

    public const STATUS_UNREGISTERED = 'unregistered';
    public const STATUS_REGISTERED = 'registered';
    public const STATUS_EXCEEDED = 'exceeded';

    /** @var System */
    public $system;
    /** @var string */
    public $status;
    /** @var string */
    public $name;
    /** @var string|null */
    public $comment;
    /** @var DateTime */
    public $checkIn;
    /** @var DateTime|null */
    public $checkOut;

    public static function deserialize(array $data): self {
        $object = new static();

        foreach ($data as $key => $value) {
            if (is_array($value) && $key === 'system') {
                $object->system = System::deserialize($value);
            } elseif (property_exists($object, $key)) {
                $object->{$key} = self::getPhpValue($value);
            }
        }
        return $object;
    }
}
