<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use GuzzleHttp\RequestOptions;
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
        $json = $this->api->get($this->getUri(), [RequestOptions::QUERY => $criteria->generateQueryString()]);
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
        $this->api->put(
            $this->getUri(),
            [
                RequestOptions::JSON => $fields,
                RequestOptions::QUERY => $criteria->generateQueryString(),
            ],
        );
    }
}
