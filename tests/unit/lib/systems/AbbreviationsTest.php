<?php

namespace meteocontrol\client\vcomapi\tests\unit\systems;

use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\client\vcomapi\filters\PaginationCriteria;
use meteocontrol\client\vcomapi\tests\unit\TestCase;

class AbbreviationsTest extends TestCase {

    /** @var \meteocontrol\client\vcomapi\endpoints\main\Systems */
    private $systemsEndpoint;

    /** @var \meteocontrol\client\vcomapi\endpoints\sub\systems\System */
    private $systemEndpoint;

    public function setup() {
        parent::setup();

        $this->systemsEndpoint = $this->api->systems();
        $this->systemEndpoint = $this->api->system('ABCDE');
    }

    public function testGetSystemsAbbreviations() {
        $json = file_get_contents(__DIR__ . '/responses/getAbbreviations.json');

        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/abbreviations'))
            ->willReturn($json);

        /** @var string[] */
        $abbreviations = $this->systemsEndpoint->abbreviations()->get();

        $this->assertEquals(3, count($abbreviations));
        $this->assertEquals('E_Z_EVU', $abbreviations[0]);
        $this->assertEquals('E_DAY', $abbreviations[1]);
        $this->assertEquals('G_M0', $abbreviations[2]);
    }

    public function testGetSystemsMeasurements() {
        $json = file_get_contents(__DIR__ . '/responses/getMeasurements.json');

        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo(
                    'systems/abbreviations/E_Z_EVU/measurements'
                ),
                $this->identicalTo(
                    'from=2016-01-01T00%3A00%3A00%2B02%3A00&to=2016-01-02T23%3A59%3A59%2B02%3A00&resolution=day'
                )
            )
            ->willReturn($json);

        $criteria = new PaginationCriteria();
        $criteria->withDateFrom(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-01-01T00:00:00+02:00'))
            ->withDateTo(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-01-02T23:59:59+02:00'))
            ->withResolution(MeasurementsCriteria::RESOLUTION_DAY);
        $paginationData = $this->systemsEndpoint->abbreviation('E_Z_EVU')->measurements()->get($criteria);
        $this->assertEquals(2, $paginationData->totalCount);
        $this->assertEquals(10, $paginationData->pageSize);
        $this->assertNull($paginationData->links->prev);
        $this->assertEquals('nextUrl', $paginationData->links->next);

        $measurements = $paginationData->data;
        $this->assertEquals(2, count($measurements));
        $this->assertEquals('ABCDE', $measurements[0]->systemKey);
        $this->assertEquals(1, count($measurements[0]->E_Z_EVU));
        $this->assertEquals('VWXYZ', $measurements[1]->systemKey);
        $this->assertEquals(1, count($measurements[1]->E_Z_EVU));

        $valuesForSystem1 = $measurements[0]->E_Z_EVU;
        $this->assertEquals('52.182', $valuesForSystem1[0]->value);
        $this->assertEquals('2016-01-01T00:00:00+02:00', $valuesForSystem1[0]->timestamp->format(\DateTime::RFC3339));

        $valuesForSystem2 = $measurements[1]->E_Z_EVU;
        $this->assertEquals('199.175', $valuesForSystem2[0]->value);
        $this->assertEquals('2016-01-01T00:00:00+02:00', $valuesForSystem2[0]->timestamp->format(\DateTime::RFC3339));
    }

    public function testGetSystemsMeasurementsWithMultipleAbbreviation() {
        $json = file_get_contents(__DIR__ . '/responses/getMeasurements2.json');

        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo(
                    'systems/abbreviations/E_Z_EVU,PR/measurements'
                ),
                $this->identicalTo(
                    'from=2016-01-01T00%3A00%3A00%2B02%3A00&to=2016-01-02T23%3A59%3A59%2B02%3A00&resolution=day' .
                    '&skip=0&limit=10'
                )
            )
            ->willReturn($json);

        $criteria = new PaginationCriteria();
        $criteria->withDateFrom(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-01-01T00:00:00+02:00'))
            ->withDateTo(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-01-02T23:59:59+02:00'))
            ->withResolution(MeasurementsCriteria::RESOLUTION_DAY)
            ->withSkip(0)
            ->withLimit(10);
        $paginationData = $this->systemsEndpoint->abbreviation(['E_Z_EVU', 'PR'])->measurements()->get($criteria);
        $this->assertEquals(2, $paginationData->totalCount);
        $this->assertEquals(10, $paginationData->pageSize);
        $this->assertEquals('prevUrl', $paginationData->links->prev);
        $this->assertNull($paginationData->links->next);

        $measurements = $paginationData->data;
        $this->assertEquals(2, count($measurements));
        $this->assertEquals('ABCDE', $measurements[0]->systemKey);
        $this->assertEquals(1, count($measurements[0]->E_Z_EVU));
        $this->assertEquals('VWXYZ', $measurements[1]->systemKey);
        $this->assertEquals(1, count($measurements[1]->E_Z_EVU));

        $valuesForSystem1 = $measurements[0]->E_Z_EVU;
        $this->assertEquals('52.182', $valuesForSystem1[0]->value);
        $this->assertEquals('2016-01-01T00:00:00+02:00', $valuesForSystem1[0]->timestamp->format(\DateTime::RFC3339));
        $valuesForSystem1 = $measurements[0]->PR;
        $this->assertEquals('20', $valuesForSystem1[0]->value);
        $this->assertEquals('2016-01-01T00:00:00+02:00', $valuesForSystem1[0]->timestamp->format(\DateTime::RFC3339));

        $valuesForSystem2 = $measurements[1]->E_Z_EVU;
        $this->assertEquals('199.175', $valuesForSystem2[0]->value);
        $this->assertEquals('2016-01-01T00:00:00+02:00', $valuesForSystem2[0]->timestamp->format(\DateTime::RFC3339));
        $valuesForSystem2 = $measurements[1]->PR;
        $this->assertEquals('20', $valuesForSystem2[0]->value);
        $this->assertEquals('2016-01-01T00:00:00+02:00', $valuesForSystem2[0]->timestamp->format(\DateTime::RFC3339));
    }
}
