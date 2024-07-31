<?php

declare(strict_types=1);

namespace unit\lib\alarms;

use meteocontrol\client\vcomapi\tests\unit\TestCase;
use meteocontrol\vcomapi\model\Alarm;

class AlarmTest extends TestCase {

    public function testCloseAlarm(): void {
        $alarm = new Alarm();
        $alarm->id = 12345;

        $this->api->expects($this->once())
            ->method("run")
            ->with(
                $this->identicalTo("alarms/12345/close"),
                null,
                null,
                "POST"
            );
        $this->api->alarm($alarm->id)->close();
    }

    public function testUpdateAlarm(): void {
        $alarm = new Alarm();
        $alarm->id = 12345;
        $alarm->ticketId = null;

        $this->api->expects($this->once())
            ->method("run")
            ->with(
                $this->identicalTo("alarms/12345"),
                null,
                json_encode(["ticketId" => null]),
                "PATCH"
            );
        $this->api->alarm($alarm->id)->update($alarm);
    }
}
