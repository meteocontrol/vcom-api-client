<?php

namespace meteocontrol\client\vcomapi\endpoints;

use InvalidArgumentException;
use meteocontrol\client\vcomapi\ApiClient;
use meteocontrol\client\vcomapi\ApiClientException;

abstract class Endpoint implements EndpointInterface {

    /** @var ApiClient */
    protected $api;

    /** @var string */
    protected $uri;

    /** @var Endpoint */
    protected $parent;

    /**
     * @return string
     */
    abstract public function getUri(): string;

    /**
     * @return ApiClient
     */
    final public function getApiClient(): ApiClient {
        return $this->api;
    }

    /**
     * @param array $filters
     * @param object $source
     * @return array
     * @throws InvalidArgumentException
     */
    final protected function applyFilter(array $filters, $source): array {
        $returns = [];
        foreach ($filters as $filter) {
            if (!isset($source->$filter)) {
                throw new InvalidArgumentException("No property: [$filter] found!");
            }
            $returns[$filter] = $this->getStringValue($source->$filter);
        }
        return $returns;
    }

    /**
     * @param mixed $json
     * @param bool $assoc
     * @return mixed
     * @throws ApiClientException
     */
    final protected function jsonDecode($json, bool $assoc = false) {
        $decoded = json_decode($json, $assoc);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $message = "Failed to deserialize body as json: '" . $json . "', error: '" . json_last_error_msg() . "'";
            throw new ApiClientException($message, 500);
        }
        return $decoded;
    }

    /**
     * @param mixed $value
     * @return string
     */
    private function getStringValue($value): string {
        if ($value instanceof \DateTime) {
            return $value->format(\DateTime::RFC3339);
        }
        return $value;
    }
}
