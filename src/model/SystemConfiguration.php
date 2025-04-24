<?php

namespace meteocontrol\client\vcomapi\model;

class SystemConfiguration extends BaseModel {

    /** @var InverterType */
    public $inverter;
    /** @var int */
    public $mpptCount;
    /** @var int */
    public $numberOfModules;
    /** @var MpptInput[] */
    public $mpptInputs;

    /**
     * @param array $data
     * @return $this
     */
    public static function deserialize(array $data): self {
        $object = new static();

        foreach ($data as $key => $value) {
            if (is_array($value) && $key === "inverter") {
                $object->inverter = InverterType::deserialize($value);
            } elseif (is_array($value) && $key === "mpptInputs") {
                $object->mpptInputs = MpptInput::deserializeArray($value);
            } elseif (property_exists($object, $key)) {
                $object->{$key} = self::getPhpValue($value);
            }
        }
        return $object;
    }
}
