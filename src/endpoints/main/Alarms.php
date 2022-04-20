<?php

declare(strict_types=1);

namespace meteocontrol\client\vcomapi\endpoints\main;

use meteocontrol\client\vcomapi\ApiClient;
use meteocontrol\client\vcomapi\filters\AlarmsCriteria;
use meteocontrol\vcomapi\model\Alarm as AlarmModel;

class Alarms extends MainEndpoint {

    /**
     * @param ApiClient $apiClient
     */
    public function __construct(ApiClient $apiClient) {
        $this->uri = 'alarms';
        $this->api = $apiClient;
    }

    /**
     * @param AlarmsCriteria $criteria
     * @return AlarmModel[]
     */
    public function find(AlarmsCriteria $criteria): array {
        $alarmsJson = $this->api->run($this->uri, $criteria->generateQueryString());
        return AlarmModel::deserializeArray($this->jsonDecode($alarmsJson, true)["data"]);
    }
}
