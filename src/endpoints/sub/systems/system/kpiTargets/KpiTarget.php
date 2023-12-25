<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems\system\kpiTargets;

use InvalidArgumentException;
use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;

class KpiTarget extends SubEndpoint {

    public function __construct(EndpointInterface $parent, string $uri) {
        $this->uri = $uri;
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return float[]
     */
    public function get(): array {
        $json = $this->api->run($this->getUri());
        return $this->jsonDecode($json, true)['data'];
    }

    /**
     * @param float[] $monthlyTargetValues
     * @return void
     */
    public function set(array $monthlyTargetValues): void {
        if (count($monthlyTargetValues) !== 12) {
            throw new InvalidArgumentException('The size of target values must be 12 (targets for each month)');
        }
        if (max(...$monthlyTargetValues) > 100.0) {
            throw new InvalidArgumentException('Maximum allowed target is 100.00');
        }
        if (min(...$monthlyTargetValues) < 0.0) {
            throw new InvalidArgumentException('Minimum allowed target is 0.00');
        }
        $this->api->run($this->getUri(), null, json_encode($monthlyTargetValues), 'PUT');
    }

    public function delete(): void {
        $this->api->run($this->getUri(), null, null, 'DELETE');
    }
}
