<?php

namespace meteocontrol\client\vcomapi\tests\unit\systems;

use DateTime;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\client\vcomapi\model\Abbreviation;
use meteocontrol\client\vcomapi\model\MeasurementValue;
use meteocontrol\client\vcomapi\model\MeasurementValueWithInterval;
use meteocontrol\client\vcomapi\readers\CsvFormat;
use meteocontrol\client\vcomapi\readers\MeasurementsBulkReader;
use meteocontrol\client\vcomapi\tests\unit\TestCase;
use PHPUnit\Framework\Error\Notice;
use UnexpectedValueException;

class CalculationsTest extends TestCase {

    public function testGetCalculationAbbreviations() {
        $json = file_get_contents(__DIR__ . '/responses/getCalculationsAbbreviations.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/calculations/abbreviations'))
            ->willReturn($json);

        /** @var string[] $abbreviations */
        $abbreviations = $this->api->system('ABCDE')->calculations()->abbreviations()->get();

        $this->assertCount(1, $abbreviations);
        $this->assertEquals('PR', $abbreviations[0]);
    }

    public function testGetCalculationSingleAbbreviation() {
        $json = file_get_contents(__DIR__ . '/responses/getCalculationsSingleAbbreviation.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/calculations/abbreviations/PR'))
            ->willReturn($json);

        /** @var Abbreviation $abbreviation */
        $abbreviation = $this->api->system('ABCDE')->calculations()->abbreviation('PR')->get();

        $this->assertEquals('AVG', $abbreviation->aggregation);
        $this->assertEquals('Performance ratio', $abbreviation->description);
        $this->assertEquals(2, $abbreviation->precision);
        $this->assertEquals('%', $abbreviation->unit);
    }

    public function testGetCalculationMeasurements() {
        $json = file_get_contents(__DIR__ . '/responses/getCalculationsMeasurements.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/calculations/abbreviations/berechnet.WR/measurements'
                ),
                $this->identicalToUrl(
                    'from=2016-01-01T00:00:00+02:00&to=2016-01-02T23:59:59+02:00&resolution=day'
                )
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DateTime::RFC3339, '2016-01-01T00:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DateTime::RFC3339, '2016-01-02T23:59:59+02:00'))
            ->withResolution(MeasurementsCriteria::RESOLUTION_DAY);

        /** @var MeasurementValue[] $measurements */
        $measurements = $this->api
            ->system('ABCDE')
            ->calculations()
            ->abbreviation(['berechnet.WR'])
            ->measurements()
            ->get($criteria);
        $values = $measurements['WR'];
        $this->assertCount(2, $values);
        $this->assertEquals(0, $values[0]->value);
        $this->assertEquals('2016-01-01T00:00:00+02:00', $values[0]->timestamp->format(DateTime::RFC3339));
        $this->assertEquals(0, $values[1]->value);
        $this->assertEquals('2016-01-02T00:00:00+02:00', $values[1]->timestamp->format(DateTime::RFC3339));
    }

    public function testGetCalculationMeasurementsWithMultipleAbbreviation() {
        $json = file_get_contents(__DIR__ . '/responses/getCalculationsMeasurements2.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/calculations/abbreviations/berechnet.WR,berechnet.PR/measurements'
                ),
                $this->identicalToUrl(
                    'from=2016-01-01T00:00:00+02:00&to=2016-01-02T23:59:59+02:00&resolution=day'
                )
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DateTime::RFC3339, '2016-01-01T00:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DateTime::RFC3339, '2016-01-02T23:59:59+02:00'))
            ->withResolution(MeasurementsCriteria::RESOLUTION_DAY);

        /** @var MeasurementValue[] $measurements */
        $measurements = $this->api
            ->system('ABCDE')
            ->calculations()
            ->abbreviation(['berechnet.WR', 'berechnet.PR'])
            ->measurements()
            ->get($criteria);
        $this->assertCount(2, $measurements);
        $value = $measurements['WR'];
        $this->assertEquals(0, $value[0]->value);
        $this->assertEquals('2016-01-01T00:00:00+02:00', $value[0]->timestamp->format(DateTime::RFC3339));
        $this->assertEquals(0, $value[1]->value);
        $this->assertEquals('2016-01-02T00:00:00+02:00', $value[1]->timestamp->format(DateTime::RFC3339));
        $value = $measurements['PR'];
        $this->assertEquals(0, $value[0]->value);
        $this->assertEquals('2016-01-01T00:00:00+02:00', $value[0]->timestamp->format(DateTime::RFC3339));
        $this->assertEquals(0, $value[1]->value);
        $this->assertEquals('2016-01-02T00:00:00+02:00', $value[1]->timestamp->format(DateTime::RFC3339));
    }

    public function testGetCalculationMeasurementsWithIntervalIncluded() {
        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DateTime::RFC3339, '2016-01-01T00:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DateTime::RFC3339, '2016-01-01T00:15:00+02:00'))
            ->withResolution(MeasurementsCriteria::RESOLUTION_INTERVAL)
            ->withIntervalIncluded();

        $this>$this->expectException(Notice::class);

        $this->api->system('ABCDE')
            ->calculations()
            ->abbreviation('berechnet.WR')
            ->measurements()
            ->get($criteria);
    }

    public function testGetCalculationMeasurementsWithIntervalIncluded2() {
        $json = file_get_contents(__DIR__ . '/responses/getCalculationsMeasurements.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/calculations/abbreviations/berechnet.WR/measurements'
                ),
                $this->identicalToUrl(
                    'from=2016-01-01T00:00:00+02:00&to=2016-01-01T00:15:00+02:00&resolution=interval&includeInterval=1'
                )
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DateTime::RFC3339, '2016-01-01T00:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DateTime::RFC3339, '2016-01-01T00:15:00+02:00'))
            ->withResolution(MeasurementsCriteria::RESOLUTION_INTERVAL)
            ->withIntervalIncluded();

        /** @var MeasurementValueWithInterval[] $measurements */
        @$measurements = $this->api
            ->system('ABCDE')
            ->calculations()
            ->abbreviation('berechnet.WR')
            ->measurements()
            ->get($criteria);

        $this->assertCount(1, $measurements);
        $measurement = $measurements['WR'];
        $this->assertEquals(0, $measurement[0]->value);
        $this->assertEquals('2016-01-01T00:00:00+02:00', $measurement[0]->timestamp->format(DateTime::RFC3339));
        $this->assertEquals(null, $measurement[0]->interval);
        $this->assertEquals(0, $measurement[1]->value);
        $this->assertEquals('2016-01-02T00:00:00+02:00', $measurement[1]->timestamp->format(DateTime::RFC3339));
        $this->assertEquals(null, $measurement[1]->interval);
    }

    public function testGetCalculationBulkData() {
        $json = file_get_contents(__DIR__ . '/responses/getCalculationsBulk.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/calculations/bulk/measurements'),
                $this->identicalToUrl('from=2016-09-01T00:00:00+02:00&to=2016-09-01T00:15:00+02:00')
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DateTime::RFC3339, '2016-09-01T00:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DateTime::RFC3339, '2016-09-01T00:15:00+02:00'));

        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->calculations()->bulk()->measurements()->get($criteria);

        $this->assertEquals($json, $bulkReader->getAsString());
        $this->assertEquals(json_decode($json, true), $bulkReader->getAsArray());
    }

    public function testGetCalculationBulkDataWithAbbreviationsFilter() {
        $json = file_get_contents(__DIR__ . '/responses/getCalculationsBulkWithAbbreviationsFilter.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/calculations/bulk/measurements'),
                $this->identicalToUrl(
                    'from=2016-09-01T00:00:00+02:00&to=2016-09-01T00:15:00+02:00&abbreviations=AREA,VFG'
                )
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DateTime::RFC3339, '2016-09-01T00:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DateTime::RFC3339, '2016-09-01T00:15:00+02:00'))
            ->withAbbreviation(['AREA', 'VFG']);

        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->calculations()->bulk()->measurements()->get($criteria);

        $this->assertEquals($json, $bulkReader->getAsString());
        $this->assertEquals(json_decode($json, true), $bulkReader->getAsArray());
    }

    public function testGetCalculationsBulkDataWithCsvFormat() {
        $cvsRawData = file_get_contents(__DIR__ . '/responses/bulkCsv/getCalculationBulk.csv');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/calculations/bulk/measurements'),
                $this->identicalToUrl(
                    'from=2016-09-01T00:00:00+02:00&to=2016-09-01T00:15:00+02:00&format=csv'
                )
            )
            ->willReturn($cvsRawData);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DateTime::RFC3339, '2016-09-01T00:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DateTime::RFC3339, '2016-09-01T00:15:00+02:00'))
            ->withFormat(CsvFormat::FORMAT_CSV);
        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->calculations()->bulk()->measurements()->get($criteria);

        $this->assertEquals($cvsRawData, $bulkReader->getAsString());
    }

    public function testGetCalculationsBulkDataWithCsvFormatWithWrongParameter() {
        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DateTime::RFC3339, '2016-09-01T10:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DateTime::RFC3339, '2016-09-01T10:15:00+02:00'))
            ->withFormat(CsvFormat::FORMAT_CSV)
            ->withDelimiter(CsvFormat::DELIMITER_COMMA)
            ->withDecimalPoint(CsvFormat::DECIMAL_POINT_COMMA)
            ->withPrecision(CsvFormat::PRECISION_2);

        $this>$this->expectException(UnexpectedValueException::class);
        $this>$this->expectExceptionMessage("Delimiter and decimal point symbols can't be the same");

        $this->api->system('ABCDE')->calculations()->bulk()->measurements()->get($criteria);
    }

    public function testGetCalculationsSimulation() {
        $json = file_get_contents(__DIR__ . '/responses/getCalculationsSimulation.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/calculations/simulation'))
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DateTime::RFC3339, '2016-11-01T00:00:00+01:00'))
            ->withDateTo(DateTime::createFromFormat(DateTime::RFC3339, '2016-11-02T23:59:59+01:00'));

        $simulationValues = $this->api->system('ABCDE')->calculations()->simulation()->get($criteria);

        $this->assertCount(2, $simulationValues);
        $this->assertEquals('2016-11-01T00:00:00+01:00', $simulationValues[0]->timestamp->format(DateTime::RFC3339));
        $this->assertEquals(2.79430153178, $simulationValues[0]->max);
        $this->assertEquals(2.2862467078199997, $simulationValues[0]->min);
        $this->assertEquals(2.5402741198, $simulationValues[0]->expected);
    }
}
