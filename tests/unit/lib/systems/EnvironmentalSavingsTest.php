<?php

namespace meteocontrol\client\vcomapi\tests\unit\systems;

use DateTime;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\client\vcomapi\tests\unit\TestCase;

class EnvironmentalSavingsTest extends TestCase {

    public function testGetCO2() {
        $json = file_get_contents(__DIR__ . '/responses/getCO2.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/environmental-savings/co2'
                ),
                $this->identicalToUrl(
                    'from=2016-10-10T00:00:00+02:00&to=2016-10-12T00:00:00+02:00&resolution=day'
                )
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria
            ->withDateFrom(DateTime::createFromFormat(DateTime::RFC3339, '2016-10-10T00:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DateTime::RFC3339, '2016-10-12T00:00:00+02:00'))
            ->withResolution(MeasurementsCriteria::RESOLUTION_DAY);

        $co2 = $this->api->system('ABCDE')->environmentalSavings()->co2()->get($criteria);
        $this->assertCount(3, $co2);
        $this->assertEquals('2016-10-10T00:00:00+01:00', $co2[0]->timestamp->format(DateTime::RFC3339));
        $this->assertEquals(10, $co2[0]->value);
        $this->assertEquals('2016-10-11T00:00:00+01:00', $co2[1]->timestamp->format(DateTime::RFC3339));
        $this->assertEquals(20, $co2[1]->value);
        $this->assertEquals('2016-10-12T00:00:00+01:00', $co2[2]->timestamp->format(DateTime::RFC3339));
        $this->assertEquals(30, $co2[2]->value);
    }

    public function testGetTreeEquivalents() {
        $json = file_get_contents(__DIR__ . '/responses/getTreeEquivalents.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/environmental-savings/tree'
                ),
                $this->identicalToUrl(
                    'from=2018-12-12T00:00:00+01:00&to=2018-12-14T00:00:00+01:00&resolution=day'
                )
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria
            ->withDateFrom(DateTime::createFromFormat(DateTime::RFC3339, '2018-12-12T00:00:00+01:00'))
            ->withDateTo(DateTime::createFromFormat(DateTime::RFC3339, '2018-12-14T00:00:00+01:00'))
            ->withResolution(MeasurementsCriteria::RESOLUTION_DAY);

        $tree = $this->api->system('ABCDE')->environmentalSavings()->treeEquivalents()->get($criteria);
        $this->assertEquals('2018-12-12T00:00:00+01:00', $tree[0]->timestamp->format(DateTime::RFC3339));
        $this->assertEquals(20, $tree[0]->value);
        $this->assertEquals('2018-12-13T00:00:00+01:00', $tree[1]->timestamp->format(DateTime::RFC3339));
        $this->assertEquals(40, $tree[1]->value);
        $this->assertEquals('2018-12-14T00:00:00+01:00', $tree[2]->timestamp->format(DateTime::RFC3339));
        $this->assertEquals(60, $tree[2]->value);
    }
}
