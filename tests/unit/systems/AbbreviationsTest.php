<?php

namespace meteocontrol\client\vcomapi\tests\unit\systems;

use DateTime;
use GuzzleHttp\RequestOptions;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\client\vcomapi\tests\unit\TestCase;
use meteocontrol\client\vcomapi\model\Measurement;

class AbbreviationsTest extends TestCase {

    /** @var \meteocontrol\client\vcomapi\endpoints\main\Systems */
    private $systemsEndpoint;

    /** @var \meteocontrol\client\vcomapi\endpoints\sub\systems\System */
    private $systemEndpoint;

    public function setup(): void {
        parent::setup();

        $this->systemsEndpoint = $this->api->systems();
        $this->systemEndpoint = $this->api->system('ABCDE');
    }

    public function testGetSystemsAbbreviations() {
        $json = file_get_contents(__DIR__ . '/responses/getAbbreviations.json');

        $this->api->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('systems/abbreviations'))
            ->willReturn($json);

        /** @var string[] */
        $abbreviations = $this->systemsEndpoint->abbreviations()->get();

        $this->assertCount(3, $abbreviations);
        $this->assertEquals('E_Z_EVU', $abbreviations[0]);
        $this->assertEquals('E_DAY', $abbreviations[1]);
        $this->assertEquals('G_M0', $abbreviations[2]);
    }

    public function testGetSystemsMeasurements() {
        $json = file_get_contents(__DIR__ . '/responses/getMeasurements.json');

        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo(
                    'systems/abbreviations/E_Z_EVU/measurements'
                ),
                $this->identicalToUrl([
                    RequestOptions::QUERY => 'from=2016-01-01T00:00:00+02:00&to=2016-01-02T23:59:59+02:00&resolution=day'
                ])
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-01-01T00:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-01-02T23:59:59+02:00'))
            ->withResolution(MeasurementsCriteria::RESOLUTION_DAY);
        /** @var Measurement[] $measurements */
        $measurements = $this->systemsEndpoint->abbreviation('E_Z_EVU')->measurements()->get($criteria);

        $this->assertCount(2, $measurements);
        $this->assertEquals('ABCDE', $measurements[0]->systemKey);
        $this->assertCount(1, $measurements[0]->E_Z_EVU);
        $this->assertEquals('VWXYZ', $measurements[1]->systemKey);
        $this->assertCount(1, $measurements[1]->E_Z_EVU);

        $valuesForSystem1 = $measurements[0]->E_Z_EVU;
        $this->assertEquals('52.182', $valuesForSystem1[0]->value);
        $this->assertEquals('2016-01-01T00:00:00+02:00', $valuesForSystem1[0]->timestamp->format(DATE_ATOM));

        $valuesForSystem2 = $measurements[1]->E_Z_EVU;
        $this->assertEquals('199.175', $valuesForSystem2[0]->value);
        $this->assertEquals('2016-01-01T00:00:00+02:00', $valuesForSystem2[0]->timestamp->format(DATE_ATOM));
    }

    public function testGetSystemsMeasurementsWithMultipleAbbreviation() {
        $json = file_get_contents(__DIR__ . '/responses/getMeasurements2.json');

        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo(
                    'systems/abbreviations/E_Z_EVU,PR/measurements'
                ),
                $this->identicalToUrl([
                    RequestOptions::QUERY => 'from=2016-01-01T00:00:00+02:00&to=2016-01-02T23:59:59+02:00&resolution=day'
                ])
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-01-01T00:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-01-02T23:59:59+02:00'))
            ->withResolution(MeasurementsCriteria::RESOLUTION_DAY);
        /** @var Measurement[] $measurements */
        $measurements = $this->systemsEndpoint->abbreviation(['E_Z_EVU', 'PR'])->measurements()->get($criteria);

        $this->assertCount(2, $measurements);
        $this->assertEquals('ABCDE', $measurements[0]->systemKey);
        $this->assertCount(1, $measurements[0]->E_Z_EVU);
        $this->assertEquals('VWXYZ', $measurements[1]->systemKey);
        $this->assertCount(1, $measurements[1]->E_Z_EVU);

        $valuesForSystem1 = $measurements[0]->E_Z_EVU;
        $this->assertEquals('52.182', $valuesForSystem1[0]->value);
        $this->assertEquals('2016-01-01T00:00:00+02:00', $valuesForSystem1[0]->timestamp->format(DATE_ATOM));
        $valuesForSystem1 = $measurements[0]->PR;
        $this->assertEquals('20', $valuesForSystem1[0]->value);
        $this->assertEquals('2016-01-01T00:00:00+02:00', $valuesForSystem1[0]->timestamp->format(DATE_ATOM));

        $valuesForSystem2 = $measurements[1]->E_Z_EVU;
        $this->assertEquals('199.175', $valuesForSystem2[0]->value);
        $this->assertEquals('2016-01-01T00:00:00+02:00', $valuesForSystem2[0]->timestamp->format(DATE_ATOM));
        $valuesForSystem2 = $measurements[1]->PR;
        $this->assertEquals('20', $valuesForSystem2[0]->value);
        $this->assertEquals('2016-01-01T00:00:00+02:00', $valuesForSystem2[0]->timestamp->format(DATE_ATOM));
    }
}
