<?php

declare(strict_types=1);

namespace meteocontrol\vcomapi\model;

use DateTime;

class Alarm extends BaseModel {

    public const STATUS_OPEN = "open";
    public const STATUS_CLOSED = "closed";

    public const ALARM_TYPE_TOTAL_OUTAGE = "total-outage";
    public const ALARM_TYPE_DATA_OUTAGE = "data-outage";
    public const ALARM_TYPE_COMMUNICATION_OUTAGE = "communication-outage";
    public const ALARM_TYPE_MISPRODUCTION = "misproduction";
    public const ALARM_TYPE_STRING_OUTAGE = "string-outage";
    public const ALARM_TYPE_SENSOR_OUTAGE = "sensor-outage";
    public const ALARM_TYPE_CUSTOM = "custom";

    public const COMPONENT_TYPE_SYSTEM = "system";
    public const COMPONENT_TYPE_SUBSYSTEM = "subsystem";
    public const COMPONENT_TYPE_DATALOGGER = "datalogger";
    public const COMPONENT_TYPE_INVERTER = "inverter";
    public const COMPONENT_TYPE_SENSOR = "sensor";
    public const COMPONENT_TYPE_METER = "meter";
    public const COMPONENT_TYPE_PPC = "ppc";
    public const COMPONENT_TYPE_BATTERY = "battery";
    public const COMPONENT_TYPE_TRACKER = "tracker";
    public const COMPONENT_TYPE_STRINGBOX = "stringbox";
    public const COMPONENT_TYPE_STATUS = "status";
    public const COMPONENT_TYPE_GENSET = "genset";

    public const SEVERITY_NORMAL = "normal";
    public const SEVERITY_HIGH = "high";
    public const SEVERITY_CRITICAL = "critical";

    /** @var int */
    public $id;

    /** @var string */
    public $systemKey;

    /** @var int|null */
    public $ticketId;

    /** @var string */
    public $alarmType;

    /** @var AlarmComponent */
    public $component;

    /** @var string */
    public $severity;

    /** @var DateTime */
    public $createdAt;

    /** @var DateTime */
    public $lastChangedAt;

    /** @var string */
    public $status;

    /** @var string */
    public $duration;

    /** @var float|null */
    public $affectedPower;

    /** @var float|null */
    public $losses;

    /** @var DateTime */
    public $startedAt;

    /**
     * @param array $data
     * @return static
     */
    public static function deserialize(array $data): self {
        $object = new static();
        foreach ($data as $key => $value) {
            if ($key === "component" && is_array($value)) {
                $object->component = AlarmComponent::deserialize($value);
            } elseif (property_exists($object, $key)) {
                $object->{$key} = self::getPhpValue($value);
            }
        }
        return $object;
    }
}
