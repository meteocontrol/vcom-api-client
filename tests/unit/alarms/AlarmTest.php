<?php

declare(strict_types=1);

namespace meteocontrol\client\vcomapi\tests\unit\alarms;

use GuzzleHttp\RequestOptions;
use meteocontrol\client\vcomapi\filters\AlarmsCriteria;
use meteocontrol\client\vcomapi\tests\unit\TestCase;
use meteocontrol\client\vcomapi\model\Alarm;

class AlarmTest extends TestCase {

    public function testGetAlarm(): void {
        $alarm = new Alarm();
        $alarm->id = 12345;

        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo('alarms/12345'),
                $this->identicalToUrl([
                    RequestOptions::QUERY => 'timezone=local',
                ]),
            )
            ->willReturn(file_get_contents(__DIR__ . '/responses/getAlarm.json'));

        $criteria = (new AlarmsCriteria())->withTimezone('local');

        $this->api->alarm($alarm->id)->get($criteria);
    }

    public function testCloseAlarm(): void {
        $alarm = new Alarm();
        $alarm->id = 12345;

        $this->api->expects($this->once())
            ->method('post')
            ->with($this->identicalTo('alarms/12345/close'));

        $this->api->alarm($alarm->id)->close();
    }

    public function testUpdateAlarm(): void {
        $alarm = new Alarm();
        $alarm->id = 12345;
        $alarm->ticketId = null;

        $this->api->expects($this->once())
            ->method('patch')
            ->with(
                $this->identicalTo('alarms/12345'),
                [RequestOptions::JSON => ['ticketId' => null]],
            );

        $this->api->alarm($alarm->id)->update($alarm);
    }
}
