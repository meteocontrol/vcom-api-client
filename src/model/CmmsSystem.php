<?php

namespace meteocontrol\client\vcomapi\model;

use DateTime;
use DateTimeInterface;

class CmmsSystem extends BaseModel {

    /** @var DateTime|null */
    public $activeUntil;

    /** @var DateTime|null */
    public $activeSince;

    /** @var string */
    public $key;

    /** @var string */
    public $name;

    /** @var int|null */
    public $renew;

    public static function deserialize(array $data): self {
        $object = new static();

        foreach ($data as $key => $value) {
            if (in_array($key, ['activeUntil', 'activeSince'])) {
                $object->{$key} = self::parseTimestamp($value);
            } elseif (property_exists($object, $key)) {
                $object->{$key} = self::getPhpValue($value);
            }
        }
        return $object;
    }

    protected function serializeDateTime(DateTimeInterface $dateTime, ?string $key = null): string {
        if (in_array($key, ['activeUntil', 'activeSince'])) {
            return $dateTime->format('Y-m-d');
        }
        return parent::serializeDateTime($dateTime);
    }

    private static function parseTimestamp(?string $value): ?DateTime {
        return $value ? DateTime::createFromFormat('Y-m-d H:i:s', $value . ' 00:00:00') : null;
    }
}
