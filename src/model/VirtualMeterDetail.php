<?php
namespace meteocontrol\client\vcomapi\model;

use DateTime;
use DateTimeInterface;

class VirtualMeterDetail extends BaseModel {

    /** @var string */
    public $id;
    /** @var string */
    public $name;
    /** @var string */
    public $serial;
    /** @var DateTime */
    public $installationDate;
    /** @var string */
    public $unit;

    /**
     * @param array $data
     * @return $this
     */
    public static function deserialize(array $data): self {
        $object = new static();
        foreach ($data as $key => $value) {
            if (property_exists($object, $key)) {
                if ($key === 'installationDate') {
                    $object->{$key} = self::deserializeInstallationDate($value);
                } else {
                    $object->{$key} = static::getPhpValue($value);
                }
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
        return $dateTime->format('Y-m-d');
    }

    /**
     * @param string $dateString
     * @return bool|DateTime
     */
    private static function deserializeInstallationDate(string $dateString) {
        return DateTime::createFromFormat('Y-m-d H:i:s', "{$dateString} 00:00:00");
    }
}
