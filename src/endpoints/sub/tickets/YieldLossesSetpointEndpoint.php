<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use GuzzleHttp\RequestOptions;
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
        $json = $this->api->get($this->getUri(), [RequestOptions::QUERY => $criteria->generateQueryString()]);
        return YieldLoss::deserialize($this->jsonDecode($json, true)['data']);
    }

    public function replace(YieldLossesCriteria $criteria, YieldLoss $yieldLoss): void {
        if (empty($yieldLoss->comment)) {
            throw new InvalidArgumentException('The comment field is empty.');
        }
        $this->api->put(
            $this->getUri(),
            [
                RequestOptions::JSON => ['comment' => $yieldLoss->comment],
                RequestOptions::QUERY => $criteria->generateQueryString(),
            ],
        );
    }
}
