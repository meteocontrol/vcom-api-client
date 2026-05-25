<?php

namespace meteocontrol\client\vcomapi\model;

use DateTime;
use DateTimeInterface;

class WorkOrderForm extends BaseModel {

    /** @var int */
    public $formId;

    /** @var string */
    public $title;

    /** @var DateTime */
    public $lastChangedAt;

    public static function deserialize(array $data): self {
        $object = new static();

        foreach ($data as $key => $value) {
            if ($key === 'lastChangedAt') {
                $object->{$key} = self::parseTimestamp($value);
            } elseif (property_exists($object, $key)) {
                $object->{$key} = self::getPhpValue($value);
            }
        }
        return $object;
    }

    protected function serializeDateTime(DateTimeInterface $dateTime, ?string $key = null): string {
        if ($key === 'lastChangedAt') {
            return $dateTime->format(DATE_ATOM);
        }
        return parent::serializeDateTime($dateTime);
    }

    private static function parseTimestamp(?string $value): ?DateTime {
        return $value ? DateTime::createFromFormat(DATE_ATOM, $value) : null;
    }
}
