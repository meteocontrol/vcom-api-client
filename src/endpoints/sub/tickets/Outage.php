<?php

declare(strict_types=1);

namespace meteocontrol\client\vcomapi\endpoints\sub\tickets;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\vcomapi\model\Outage as OutageModel;

class Outage extends SubEndpoint {

    public function __construct(EndpointInterface $parent) {
        $this->uri = '/outage';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    public function get(): OutageModel {
        $outageJson = $this->api->run($this->getUri());
        return OutageModel::deserialize(json_decode($outageJson, true)['data']);
    }

    public function update(OutageModel $outage, array $updateFilter = null): void {
        if (!$updateFilter) {
            $fields = [
                'startedAt' => $outage->startedAt->format(DATE_ATOM),
                'endedAt' => is_null($outage->endedAt) ? null : $outage->endedAt->format(DATE_ATOM),
                'affectedPower' => $outage->affectedPower,
                'shouldInfluenceAvailability' => $outage->shouldInfluenceAvailability,
                'shouldInfluencePr' => $outage->shouldInfluencePr,
                'components' => $outage->components,
            ];
        } else {
            $fields = $this->applyFilter($updateFilter, $outage);
        }
        $this->api->run($this->getUri(), null, json_encode($fields), 'PATCH');
    }

    public function replace(OutageModel $outage): void {
        $this->api->run(
            $this->getUri(),
            null,
            json_encode(
                [
                    'startedAt' => $outage->startedAt->format(DATE_ATOM),
                    'endedAt' => is_null($outage->endedAt) ? null : $outage->endedAt->format(DATE_ATOM),
                    'affectedPower' => $outage->affectedPower,
                    'shouldInfluenceAvailability' => $outage->shouldInfluenceAvailability,
                    'shouldInfluencePr' => $outage->shouldInfluencePr,
                    'components' => $outage->components,
                ],
            ),
            'PUT',
        );
    }

    public function delete(): void {
        $this->api->run($this->getUri(), null, null, 'DELETE');
    }
}
