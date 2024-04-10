<?php

declare(strict_types=1);

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use InvalidArgumentException;
use meteocontrol\client\vcomapi\filters\YieldLossesCriteria;
use meteocontrol\client\vcomapi\model\YieldLoss;

class GridOperator extends GridOperatorReadOnly {

    public function replace(YieldLossesCriteria $criteria, YieldLoss $yieldLoss): void {
        if (!$yieldLoss->isValid()) {
            throw new InvalidArgumentException('Yield loss is invalid!');
        }
        $fields = [
            'realLostYield' => $yieldLoss->realLostYield,
            'comment' => $yieldLoss->comment,
        ];
        $this->api->run($this->getUri(), $criteria->generateQueryString(), json_encode($fields), 'PUT');
    }
}
