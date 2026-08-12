<?php

declare(strict_types=1);

namespace meteocontrol\client\vcomapi\endpoints\sub\alarms;

use GuzzleHttp\RequestOptions;
use meteocontrol\client\vcomapi\endpoints\Endpoint;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\filters\AlarmsCriteria;
use meteocontrol\client\vcomapi\model\Alarm as AlarmModel;

class Alarm extends SubEndpoint {

    public function __construct(Endpoint $parent, int $alarmId) {
        $this->api = $parent->api;
        $this->parent = $parent;
        $this->uri = "/{$alarmId}";
    }

    public function get(?AlarmsCriteria $criteria = null): AlarmModel {
        $options = [];
        if ($criteria) {
            $options = [RequestOptions::QUERY => $criteria->generateQueryString()];
        }
        $alarmJson = $this->api->get($this->getUri(), $options);
        return AlarmModel::deserialize($this->jsonDecode($alarmJson, true)['data']);
    }

    public function close(): void {
        $this->api->post($this->getUri() . '/close');
    }

    public function update(AlarmModel $alarm): void {
        $this->api->patch($this->getUri(), [RequestOptions::JSON => ['ticketId' => $alarm->ticketId]]);
    }
}
