<?php

namespace meteocontrol\client\vcomapi\model;

class TechnicalData extends BaseModel {

    /** @var float */
    public $nominalPower;
    /** @var float */
    public $siteArea;
    /** @var Panel[] */
    public $panels;
    /** @var InverterType[] */
    public $inverters;
    /** @var SystemConfiguration[] */
    public $systemConfigurations;

    /**
     * @param array $data
     * @return $this
     */
    public static function deserialize(array $data): self {
        $object = new static();

        foreach ($data as $key => $value) {
            if (is_array($value) && $key === "panels") {
                $object->panels = Panel::deserializeArray($value);
            } elseif (is_array($value) && $key === "inverters") {
                $object->inverters = InverterType::deserializeArray($value);
            } elseif (is_array($value) && $key === "systemConfigurations") {
                $object->systemConfigurations = SystemConfiguration::deserializeArray($value);
            } elseif (property_exists($object, $key)) {
                $object->{$key} = self::getPhpValue($value);
            }
        }
        return $object;
    }
}
