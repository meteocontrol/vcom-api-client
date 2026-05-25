<?php

namespace meteocontrol\client\vcomapi\model;

use DateTime;
use DateTimeInterface;
use JsonSerializable;
use stdClass;

abstract class BaseModel extends stdClass implements JsonSerializable {

    public static function deserialize(array $data): self {
        $object = new static();
        foreach ($data as $key => $value) {
            if (property_exists($object, $key)) {
                $object->{$key} = static::getPhpValue($value);
            }
        }
        return $object;
    }

    public static function deserializeArray(array $decodedJsonArray): array {
        $objects = [];
        foreach ($decodedJsonArray as $item) {
            $objects[] = static::deserialize($item);
        }
        return $objects;
    }

    public function jsonSerialize(): array {
        $values = get_object_vars($this);

        foreach ($values as $key => $value) {
            if ($value instanceof DateTimeInterface) {
                $values[$key] = $this->serializeDateTime($value, $key);
            }
        }

        return $values;
    }

    protected function serializeDateTime(DateTimeInterface $dateTime, ?string $key = null): string {
        return $dateTime->format(DATE_ATOM);
    }

    /**
     * @param array | string | int | float | null $value
     * @return DateTime | string | int | float | null
     */
    protected static function getPhpValue($value) {
        if (self::isRFC3339DateString($value)) {
            return DateTime::createFromFormat(DATE_ATOM, $value);
        }

        return $value;
    }

    /**
     * @param array|string|null $dateString
     * @return bool|DateTime
     */
    private static function isRFC3339DateString($dateString) {
        return is_string($dateString) && DateTime::createFromFormat(DATE_ATOM, $dateString);
    }
}
