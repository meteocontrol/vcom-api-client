<?php

declare(strict_types=1);

namespace meteocontrol\client\vcomapi\tests\unit\systems;

use DateTime;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\client\vcomapi\tests\unit\TestCase;

class SatelliteTest extends TestCase {

    public function testGetIrradiance(): void {
        $json = file_get_contents(__DIR__ . '/responses/getIrradiance.json');

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DateTime::RFC3339, '2016-11-15T00:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DateTime::RFC3339, '2016-11-15T23:59:59+02:00'))
            ->withResolution(MeasurementsCriteria::RESOLUTION_HOUR);

        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/satellite/irradiance'),
                $this->identicalToUrl('from=2016-11-15T00:00:00+02:00&to=2016-11-15T23:59:59+02:00&resolution=hour')
            )
            ->willReturn($json);

        $irradiance = $this->api->system('ABCDE')->satellite()->irradiance()->get($criteria);

        $this->assertEquals('2016-11-15T00:00:00+02:00', $irradiance[0]->timestamp->format(DateTime::RFC3339));
        $this->assertNull($irradiance[0]->value);
        $this->assertEquals('2016-11-15T10:00:00+02:00', $irradiance[10]->timestamp->format(DateTime::RFC3339));
        $this->assertEqualsWithDelta(46.239, $irradiance[10]->value, 0.001);
        $this->assertEquals('2016-11-15T20:00:00+02:00', $irradiance[20]->timestamp->format(DateTime::RFC3339));
        $this->assertEqualsWithDelta(382.698, $irradiance[20]->value, 0.001);
    }
}
