<?php

declare(strict_types=1);

namespace meteocontrol\client\vcomapi\tests\unit\tickets;

use DateTime;
use GuzzleHttp\RequestOptions;
use meteocontrol\client\vcomapi\tests\unit\TestCase;
use meteocontrol\client\vcomapi\model\Outage as OutageModel;

class OutageTest extends TestCase {

    public function testGetOutage(): void {
        $this->api->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('tickets/123/outage'))
            ->willReturn(file_get_contents(__DIR__ . '/responses/getOutage.json'));
        $outage = $this->api->ticket('123')->outage()->get();

        $this->assertEquals(new DateTime('2024-06-01T12:00:00+02:00'), $outage->startedAt);
        $this->assertEquals(null, $outage->endedAt);
        $this->assertEquals(123.45, $outage->affectedPower);
        $this->assertEquals(true, $outage->shouldInfluenceAvailability);
        $this->assertEquals(false, $outage->shouldInfluencePr);
        $this->assertEquals(['Id56789.1', 'Id56789.2'], $outage->components);
    }

    public function testUpdateOutage(): void {
        $outage = $this->getOutage();
        $this->api->expects($this->once())
            ->method('patch')
            ->with(
                $this->identicalTo('tickets/123/outage'),
                [RequestOptions::JSON => ['components' => ['Id56789.1', 'Id56789.2']]],
            );
        $this->api->ticket('123')->outage()->update($outage, ['components']);
    }

    public function testReplaceOutage(): void {
        $outage = $this->getOutage();
        $this->api->expects($this->once())
            ->method('put')
            ->with(
                $this->identicalTo('tickets/123/outage'),
                [
                    RequestOptions::JSON => [
                        'startedAt' => '2024-06-01T12:00:00+02:00',
                        'endedAt' => '2024-06-01T13:00:00+02:00',
                        'affectedPower' => 123.67,
                        'shouldInfluenceAvailability' => false,
                        'shouldInfluencePr' => true,
                        'components' => ['Id56789.1', 'Id56789.2'],
                    ],
                ],
            );
        $this->api->ticket('123')->outage()->replace($outage);
    }

    public function testDeleteOutage(): void {
        $this->api->expects($this->once())
            ->method('delete')
            ->with($this->identicalTo('tickets/123/outage'));
        $this->api->ticket('123')->outage()->delete();
    }

    private function getOutage(): OutageModel {
        $outage = new OutageModel();
        $outage->startedAt = new DateTime('2024-06-01T12:00:00+02:00');
        $outage->endedAt = new DateTime('2024-06-01T13:00:00+02:00');
        $outage->affectedPower = 123.67;
        $outage->shouldInfluenceAvailability = false;
        $outage->shouldInfluencePr = true;
        $outage->components = ['Id56789.1', 'Id56789.2'];
        return $outage;
    }
}
