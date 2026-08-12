<?php

declare(strict_types=1);

namespace meteocontrol\client\vcomapi\tests\unit\alarms;

use GuzzleHttp\RequestOptions;
use meteocontrol\client\vcomapi\filters\AlarmsCriteria;
use meteocontrol\client\vcomapi\tests\unit\TestCase;

class AlarmsTest extends TestCase {

    public function testGetAlarms(): void {
        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo('alarms'),
                $this->identicalToUrl([
                    RequestOptions::QUERY => 'systemKey=ABCDE' .
                        '&status=closed' .
                        '&severity=critical' .
                        '&alarmType=communication-outage' .
                        '&timezone=local',
                ]),
            )
            ->willReturn(file_get_contents(__DIR__ . '/responses/getAlarms.json'));

        $criteria = new AlarmsCriteria();
        $criteria->withSystemKey('ABCDE')
            ->withStatus('closed')
            ->withSeverity('critical')
            ->withAlarmType('communication-outage')
            ->withTimezone('local');

        $this->api->alarms()->find($criteria);
    }
}
