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

    /**
     * @param array $data
     * @param null|string $name
     * @return $this
     */
    public static function deserialize(array $data, $name = null) {
        $className = get_called_class();
        $classInstance = new $className();
        foreach ($data as $key => $value) {
            if (is_array($value) && $key === "panels") {
                $classInstance->panels = Panel::deserializeArray($value);
            } elseif (is_array($value) && $key === "inverters") {
                $classInstance->inverters = InverterType::deserializeArray($value);
            } elseif (property_exists($className, $key)) {
                $classInstance->{$key} = self::getPhpValue($value);
            }
        }
        return $classInstance;
    }
}
