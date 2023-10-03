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
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2020-09-01T06:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2020-09-01T08:59:59+02:00'))
            ->withResolution(MeasurementsCriteria::RESOLUTION_HOUR);

        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/satellite/irradiance'),
                $this->identicalToUrl('from=2020-09-01T06:00:00+02:00&to=2020-09-01T08:59:59+02:00&resolution=hour')
            )
            ->willReturn($json);

        $irradiance = $this->api->system('ABCDE')->satellite()->irradiance()->get($criteria);

        $this->assertEquals('2020-09-01T07:00:00+02:00', $irradiance[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(0, $irradiance[0]->value);
        $this->assertEquals('2020-09-01T08:00:00+02:00', $irradiance[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(12.3265024145379, $irradiance[1]->value);
        $this->assertEquals('2020-09-01T09:00:00+02:00', $irradiance[2]->timestamp->format(DATE_ATOM));
        $this->assertEquals(54.2568234978603, $irradiance[2]->value);
    }
}
