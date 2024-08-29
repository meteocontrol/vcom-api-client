<?php

declare(strict_types=1);

namespace meteocontrol\client\vcomapi\endpoints\sub\alarms;

use GuzzleHttp\RequestOptions;
use meteocontrol\client\vcomapi\endpoints\Endpoint;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\vcomapi\model\Alarm as AlarmModel;

class Alarm extends SubEndpoint {

    /**
     * @param Endpoint $parent
     * @param int $alarmId
     */
    public function __construct(Endpoint $parent, int $alarmId) {
        $this->api = $parent->api;
        $this->parent = $parent;
        $this->uri = "/{$alarmId}";
    }

    /**
     * @return AlarmModel
     */
    public function get(): AlarmModel {
        $alarmJson = $this->api->get($this->getUri());
        return AlarmModel::deserialize($this->jsonDecode($alarmJson, true)['data']);
    }

    public function close(): void {
        $this->api->post($this->getUri() . '/close');
    }

    public function update(AlarmModel $alarm): void {
        $this->api->patch($this->getUri(), [RequestOptions::JSON => ['ticketId' => $alarm->ticketId]]);
    }
}
