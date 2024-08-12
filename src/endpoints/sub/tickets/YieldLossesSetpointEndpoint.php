<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use InvalidArgumentException;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\YieldLossesCriteria;
use meteocontrol\client\vcomapi\model\YieldLoss;

class YieldLossesSetpointEndpoint extends SubEndpoint {

    public function __construct(EndpointInterface $parent, string $uri) {
        $this->uri = $uri;
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    public function get(YieldLossesCriteria $criteria): YieldLoss {
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
