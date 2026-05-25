<?php

namespace meteocontrol\client\vcomapi\model;

use DateTime;
use DateTimeInterface;

class CmmsAssignee extends BaseModel {

    /** @var string */
    public $username;

    /** @var string */
    public $status;

    /** @var DateTime */
    public $statusDateAt;

    /** @var string */
    public $statusReason;

    public static function deserialize(array $data): self {
        $object = new static();

        foreach ($data as $key => $value) {
            if ($key === 'statusDateAt') {
                $object->{$key} = self::parseTimestamp($value);
            } elseif (property_exists($object, $key)) {
                $object->{$key} = self::getPhpValue($value);
            }
        }
        return $object;
    }

    protected function serializeDateTime(DateTimeInterface $dateTime, ?string $key = null): string {
        if ($key === 'statusDateAt') {
            return $dateTime->format(DATE_ATOM);
        }
        return parent::serializeDateTime($dateTime);
    }

    private static function parseTimestamp(?string $value): ?DateTime {
        return $value ? DateTime::createFromFormat(DATE_ATOM, $value) : null;
    }
}
