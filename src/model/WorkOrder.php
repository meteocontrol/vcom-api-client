<?php

namespace meteocontrol\vcomapi\model;

use DateTime;

class WorkOrder extends BaseModel {

    /** @var int */
    public $workOrderId;

    /** @var string */
    public $title;

    /** @var DateTime */
    public $lastChangedAt;

    /**
     * @param array $data
     * @return $this
     */
    public static function deserialize(array $data) {
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

    /**
     * @param DateTime $dateTime
     * @param null|string $key
     * @return string
     */
    protected function serializeDateTime(DateTime $dateTime, $key = null): string {
        if ($key === 'lastChangedAt') {
            return $dateTime->format(DATE_ATOM);
        }
        return parent::serializeDateTime($dateTime);
    }

    /**
     * @param string|null $value
     * @return DateTime|null
     */
    private static function parseTimestamp(?string $value): ?DateTime {
        return $value ? DateTime::createFromFormat(DATE_ATOM, $value) : null;
    }
}
