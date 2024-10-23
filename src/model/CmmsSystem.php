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

    /**
     * @param array $data
     * @return $this
     */
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

    /**
     * @param DateTimeInterface $dateTime
     * @param null|string $key
     * @return string
     */
    protected function serializeDateTime(DateTimeInterface $dateTime, $key = null): string {
        if (in_array($key, ['activeUntil', 'activeSince'])) {
            return $dateTime->format('Y-m-d');
        }
        return parent::serializeDateTime($dateTime);
    }

    /**
     * @param string|null $value
     * @return DateTime|null
     */
    private static function parseTimestamp(?string $value): ?DateTime {
        return $value ? DateTime::createFromFormat('Y-m-d H:i:s', $value . ' 00:00:00') : null;
    }
}
