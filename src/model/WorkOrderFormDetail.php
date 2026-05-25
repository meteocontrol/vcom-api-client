<?php

namespace meteocontrol\client\vcomapi\model;

use DateTime;
use DateTimeInterface;

class WorkOrderFormDetail extends BaseModel {

    /** @var int */
    public $formId;

    /** @var int */
    public $workOrderId;

    /** @var string */
    public $title;

    /** @var string */
    public $form;

    /** @var string */
    public $description;

    /** @var string */
    public $data;

    /** @var string */
    public $originalData;

    /** @var DateTime */
    public $savedAt;

    /** @var DateTime */
    public $createdAt;

    /** @var DateTime */
    public $completedAt;

    /** @var DateTime */
    public $lastChangedAt;

    /** @var string */
    public $completedBy;

    /** @var string */
    public $changedBy;

    /** @var bool */
    public $editable;

    public static function deserialize(array $data): self {
        $object = new static();

        foreach ($data as $key => $value) {
            if (in_array($key, ['savedAt', 'createdAt', 'completedAt', 'lastChangedAt'])) {
                $object->{$key} = self::parseTimestamp($value);
            } elseif (property_exists($object, $key)) {
                $object->{$key} = self::getPhpValue($value);
            }
        }
        return $object;
    }

    protected function serializeDateTime(DateTimeInterface $dateTime, ?string $key = null): string {
        if (in_array($key, ['savedAt', 'createdAt', 'completedAt', 'lastChangedAt'])) {
            return $dateTime->format(DATE_ATOM);
        }
        return parent::serializeDateTime($dateTime);
    }

    private static function parseTimestamp(?string $value): ?DateTime {
        return $value ? DateTime::createFromFormat(DATE_ATOM, $value) : null;
    }
}
