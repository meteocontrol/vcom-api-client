<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use InvalidArgumentException;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\LinearEquationCriteria;
use meteocontrol\client\vcomapi\filters\YieldLossesCriteria;
use meteocontrol\client\vcomapi\model\YieldLoss;

class LinearEquation extends SubEndpoint {

    public function __construct(EndpointInterface $parent) {
        $this->uri = '/linear-equation';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    public function get(LinearEquationCriteria $criteria): YieldLoss {
        $json = $this->api->run($this->getUri(), $criteria->generateQueryString());
        return YieldLoss::deserialize($this->jsonDecode($json, true)['data']);
    }

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
