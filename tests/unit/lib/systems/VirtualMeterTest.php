<?php

namespace meteocontrol\client\vcomapi\tests\unit\systems;

use DateTime;
use meteocontrol\client\vcomapi\filters\MeterReadingCriteria;
use meteocontrol\client\vcomapi\tests\unit\TestCase;
use meteocontrol\vcomapi\model\VirtualMeter;
use meteocontrol\vcomapi\model\VirtualMeterDetail;
use meteocontrol\vcomapi\model\VirtualMeterReading;

class VirtualMeterTest extends TestCase {

    public function testGetVirtualMeters() {
        $json = file_get_contents(__DIR__ . '/responses/getVirtualMeters.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/virtual-meters'))
            ->willReturn($json);

        /** @var VirtualMeter[] $virtualMeters */
        $virtualMeters = $this->api->system('ABCDE')->virtualMeters()->get();

        $this->assertCount(2, $virtualMeters);
        $this->assertEquals(81297, $virtualMeters[0]->id);
        $this->assertEquals('energy meter', $virtualMeters[0]->name);
        $this->assertEquals('s1234', $virtualMeters[0]->serial);
        $this->assertEquals(81298, $virtualMeters[1]->id);
        $this->assertEquals('energy meter2', $virtualMeters[1]->name);
        $this->assertEquals('s5678', $virtualMeters[1]->serial);
    }

    public function testGetSingleVirtualMeter() {
        $json = file_get_contents(__DIR__ . '/responses/getVirtualMeter.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/virtual-meters/81297'))
            ->willReturn($json);

        /** @var VirtualMeterDetail $virtualMeters */
        $virtualMeter = $this->api->system('ABCDE')->virtualMeter('81297')->get();

        $this->assertEquals(81297, $virtualMeter->id);
        $this->assertEquals('energy meter', $virtualMeter->name);
        $this->assertEquals('s1234', $virtualMeter->serial);
        $this->assertEquals(
            DateTime::createFromFormat('Y-m-d H:i:s', "2016-12-19 00:00:00"),
            $virtualMeter->installationDate
        );
        $this->assertEquals('kWh', $virtualMeter->unit);
    }

    public function testGetLatestVirtualMeterReading() {
        $json = file_get_contents(__DIR__ . '/responses/getVirtualMeterLatestReading.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/virtual-meters/81297/readings'))
            ->willReturn($json);

        $criteria = new MeterReadingCriteria();
        /** @var VirtualMeterReading[] $latestReadings */
        $latestReadings = $this->api->system('ABCDE')->virtualMeter('81297')->readings()->get($criteria);

        $this->assertCount(1, $latestReadings);

        $this->assertEquals(146803874, $latestReadings[0]->id);
        $this->assertEquals('MANUAL', $latestReadings[0]->type);
        $this->assertEquals(new DateTime('2019-11-01T09:30:00+01:00'), $latestReadings[0]->timestamp);
        $this->assertEquals(10000, $latestReadings[0]->value);
    }

    public function testGetLatestAutoVirtualMeterReading() {
        $json = file_get_contents(__DIR__ . '/responses/getVirtualMeterLatestAutoReading.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/virtual-meters/81297/readings'),
                $this->identicalTo('type=AUTO')
            )->willReturn($json);

        $criteria = new MeterReadingCriteria();
        $criteria->withType(MeterReadingCriteria::READING_TYPE_AUTO);
        /** @var VirtualMeterReading[] $latestReadings */
        $latestReadings = $this->api->system('ABCDE')->virtualMeter('81297')->readings()->get($criteria);

        $this->assertCount(1, $latestReadings);

        $this->assertEquals(146803854, $latestReadings[0]->id);
        $this->assertEquals('AUTO', $latestReadings[0]->type);
        $this->assertEquals(new DateTime('2018-03-31T23:59:59+02:00'), $latestReadings[0]->timestamp);
        $this->assertEquals(3958.0235319860999, $latestReadings[0]->value);
    }

    public function testGetLatestManualVirtualMeterReading() {
        $json = file_get_contents(__DIR__ . '/responses/getVirtualMeterLatestManualReading.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/virtual-meters/81297/readings'),
                $this->identicalTo('type=MANUAL')
            )->willReturn($json);

        $criteria = new MeterReadingCriteria();
        $criteria->withType(MeterReadingCriteria::READING_TYPE_MANUAL);
        /** @var VirtualMeterReading[] $latestReadings */
        $latestReadings = $this->api->system('ABCDE')->virtualMeter('81297')->readings()->get($criteria);

        $this->assertCount(1, $latestReadings);

        $this->assertEquals(146803874, $latestReadings[0]->id);
        $this->assertEquals('MANUAL', $latestReadings[0]->type);
        $this->assertEquals(new DateTime('2019-11-01T09:30:00+01:00'), $latestReadings[0]->timestamp);
        $this->assertEquals(10000, $latestReadings[0]->value);
    }

    public function testGetVirtualMeterReading() {
        $json = file_get_contents(__DIR__ . '/responses/getVirtualMeterReading.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/virtual-meters/81297/readings'),
                $this->identicalToUrl('from=2018-02-28T00:00:00+02:00&to=2018-05-01T00:00:00+01:00')
            )->willReturn($json);

        $criteria = new MeterReadingCriteria();
        $criteria->withDateFrom(new DateTime("2018-02-28T00:00:00+02:00"))
            ->withDateTo(new DateTime("2018-05-01T00:00:00+01:00"));
        /** @var VirtualMeterReading[] $latestReadings */
        $latestReadings = $this->api->system('ABCDE')->virtualMeter('81297')->readings()->get($criteria);

        $this->assertCount(3, $latestReadings);

        $this->assertEquals(146803853, $latestReadings[0]->id);
        $this->assertEquals('MANUAL', $latestReadings[0]->type);
        $this->assertEquals(new DateTime('2018-02-28T23:59:59+01:00'), $latestReadings[0]->timestamp);
        $this->assertEquals(3698.2216528918002, $latestReadings[0]->value);

        $this->assertEquals(146803854, $latestReadings[1]->id);
        $this->assertEquals('AUTO', $latestReadings[1]->type);
        $this->assertEquals(new DateTime('2018-03-31T23:59:59+02:00'), $latestReadings[1]->timestamp);
        $this->assertEquals(3958.0235319860999, $latestReadings[1]->value);

        $this->assertEquals(146803855, $latestReadings[2]->id);
        $this->assertEquals('MANUAL', $latestReadings[2]->type);
        $this->assertEquals(new DateTime('2018-04-30T23:59:59+02:00'), $latestReadings[2]->timestamp);
        $this->assertEquals(4294.8163073654996, $latestReadings[2]->value);
    }

    public function testGetAutoVirtualMeterReading() {
        $json = file_get_contents(__DIR__ . '/responses/getVirtualMeterAutoReading.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/virtual-meters/81297/readings'),
                $this->identicalToUrl(
                    'from=2018-02-28T00:00:00+02:00&to=2018-05-01T00:00:00+01:00&type=AUTO'
                )
            )->willReturn($json);

        $criteria = new MeterReadingCriteria();
        $criteria->withDateFrom(new DateTime("2018-02-28T00:00:00+02:00"))
            ->withDateTo(new DateTime("2018-05-01T00:00:00+01:00"))
            ->withType(MeterReadingCriteria::READING_TYPE_AUTO);
        /** @var VirtualMeterReading[] $latestReadings */
        $latestReadings = $this->api->system('ABCDE')->virtualMeter('81297')->readings()->get($criteria);

        $this->assertCount(1, $latestReadings);

        $this->assertEquals(146803854, $latestReadings[0]->id);
        $this->assertEquals('AUTO', $latestReadings[0]->type);
        $this->assertEquals(new DateTime('2018-03-31T23:59:59+02:00'), $latestReadings[0]->timestamp);
        $this->assertEquals(3958.0235319860999, $latestReadings[0]->value);
    }

    public function testGetManualVirtualMeterReading() {
        $json = file_get_contents(__DIR__ . '/responses/getVirtualMeterManualReading.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/virtual-meters/81297/readings'),
                $this->identicalToUrl(
                    'from=2018-02-28T00:00:00+02:00&to=2018-05-01T00:00:00+01:00&type=MANUAL'
                )
            )->willReturn($json);

        $criteria = new MeterReadingCriteria();
        $criteria->withDateFrom(new DateTime("2018-02-28T00:00:00+02:00"))
            ->withDateTo(new DateTime("2018-05-01T00:00:00+01:00"))
            ->withType(MeterReadingCriteria::READING_TYPE_MANUAL);
        /** @var VirtualMeterReading[] $latestReadings */
        $latestReadings = $this->api->system('ABCDE')->virtualMeter('81297')->readings()->get($criteria);

        $this->assertCount(2, $latestReadings);

        $this->assertEquals(146803853, $latestReadings[0]->id);
        $this->assertEquals('MANUAL', $latestReadings[0]->type);
        $this->assertEquals(new DateTime('2018-02-28T23:59:59+01:00'), $latestReadings[0]->timestamp);
        $this->assertEquals(3698.2216528918002, $latestReadings[0]->value);

        $this->assertEquals(146803855, $latestReadings[1]->id);
        $this->assertEquals('MANUAL', $latestReadings[1]->type);
        $this->assertEquals(new DateTime('2018-04-30T23:59:59+02:00'), $latestReadings[1]->timestamp);
        $this->assertEquals(4294.8163073654996, $latestReadings[1]->value);
    }
}
