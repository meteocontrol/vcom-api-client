<?php

namespace meteocontrol\client\vcomapi\tests\unit\systems;

use DateTime;
use GuzzleHttp\RequestOptions;
use InvalidArgumentException;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\client\vcomapi\model\Abbreviation;
use meteocontrol\client\vcomapi\model\MeasurementValue;
use meteocontrol\client\vcomapi\model\MeasurementValueWithInterval;
use meteocontrol\client\vcomapi\readers\CsvFormat;
use meteocontrol\client\vcomapi\readers\MeasurementsBulkReader;
use meteocontrol\client\vcomapi\tests\unit\TestCase;
use UnexpectedValueException;

class BasicsTest extends TestCase {

    public function testGetBasicsAbbreviations() {
        $json = file_get_contents(__DIR__ . '/responses/getBasicsAbbreviations.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('systems/ABCDE/basics/abbreviations'))
            ->willReturn($json);

        /** @var string[] $abbreviations */
        $abbreviations = $this->api->system('ABCDE')->basics()->abbreviations()->get();

        $this->assertCount(3, $abbreviations);
        $this->assertEquals('E_Z_PV1', $abbreviations[0]);
        $this->assertEquals('E_Z_EVU', $abbreviations[1]);
        $this->assertEquals('G_M0', $abbreviations[2]);
    }

    public function testGetBasicsSingleAbbreviation() {
        $json = file_get_contents(__DIR__ . '/responses/getBasicsSingleAbbreviation.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('systems/ABCDE/basics/abbreviations/E_Z_EVU'))
            ->willReturn($json);

        /** @var Abbreviation $abbreviation */
        $abbreviation = $this->api->system('ABCDE')->basics()->abbreviation('E_Z_EVU')->get();

        $this->assertEquals('SUM', $abbreviation->aggregation);
        $this->assertEquals('Energie aus I-checker', $abbreviation->description);
        $this->assertEquals(null, $abbreviation->precision);
        $this->assertEquals('kWh', $abbreviation->unit);
    }

    public function testGetBasicsMeasurements() {
        $json = file_get_contents(__DIR__ . '/responses/getBasicsMeasurements.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/basics/abbreviations/wr.E_INT/measurements'
                ),
                $this->identicalToUrl([
                    RequestOptions::QUERY => 'from=2016-01-01T00:00:00+02:00&to=2016-01-01T00:15:00+02:00&resolution=interval'
                ])
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-01-01T00:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-01-01T00:15:00+02:00'))
            ->withResolution(MeasurementsCriteria::RESOLUTION_INTERVAL);
        /** @var MeasurementValue[] $measurements */
        $measurements = $this->api->system('ABCDE')->basics()->abbreviation('wr.E_INT')->measurements()->get($criteria);
        $values = $measurements['E_INT'];
        $this->assertCount(2, $values);
        $this->assertEquals(0, $values[0]->value);
        $this->assertEquals('2016-01-01T00:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(0, $values[1]->value);
        $this->assertEquals('2016-01-01T00:15:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
    }

    public function testGetBasicsMeasurementsWithMultipleAbbreviation() {
        $json = file_get_contents(__DIR__ . '/responses/getBasicsMeasurements2.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/basics/abbreviations/wr.E_INT,wr.G_M0/measurements'
                ),
                $this->identicalToUrl([
                    RequestOptions::QUERY => 'from=2016-01-01T00:00:00+02:00&to=2016-01-01T00:15:00+02:00&resolution=interval'
                ])
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-01-01T00:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-01-01T00:15:00+02:00'))
            ->withResolution(MeasurementsCriteria::RESOLUTION_INTERVAL);
        /** @var MeasurementValue[] $measurements */
        $measurements = $this->api
            ->system('ABCDE')
            ->basics()
            ->abbreviation(['wr.E_INT', 'wr.G_M0'])
            ->measurements()
            ->get($criteria);

        $this->assertCount(2, $measurements);
        $values = $measurements['E_INT'];
        $this->assertEquals(0, $values[0]->value);
        $this->assertEquals('2016-01-01T00:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(0, $values[1]->value);
        $this->assertEquals('2016-01-01T00:15:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $values = $measurements['G_M0'];
        $this->assertEquals(1, $values[0]->value);
        $this->assertEquals('2016-01-01T00:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(1, $values[1]->value);
        $this->assertEquals('2016-01-01T00:15:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
    }

    public function testGetBasicsMeasurementsWithMultipleAbbreviationAndIntervalData() {
        $json = file_get_contents(__DIR__ . '/responses/getBasicsMeasurementsIncludeIntervalVersion2.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/basics/abbreviations/wr.E_INT,wr.G_M0/measurements'
                ),
                $this->identicalToUrl([
                    RequestOptions::QUERY => 'from=2016-01-01T00:00:00+02:00&to=2016-01-01T00:15:00+02:00&resolution=interval&includeInterval=1'
                ])
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-01-01T00:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-01-01T00:15:00+02:00'))
            ->withResolution(MeasurementsCriteria::RESOLUTION_INTERVAL)
            ->withIntervalIncluded();

        /** @var MeasurementValueWithInterval[] $measurements */
        $measurements = $this->api
            ->system('ABCDE')
            ->basics()
            ->abbreviation(['wr.E_INT', 'wr.G_M0'])
            ->measurements()
            ->get($criteria);

        $this->assertCount(2, $measurements);
        $values = $measurements['E_INT'];
        $this->assertEquals(0, $values[0]->value);
        $this->assertEquals(300, $values[0]->interval);
        $this->assertEquals('2016-01-01T00:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(0, $values[1]->value);
        $this->assertEquals('2016-01-01T00:15:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[1]->interval);
        $values = $measurements['G_M0'];
        $this->assertEquals(0, $values[0]->value);
        $this->assertEquals(300, $values[0]->interval);
        $this->assertEquals('2016-01-01T00:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(0, $values[1]->value);
        $this->assertEquals('2016-01-01T00:15:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[1]->interval);
    }

    public function testGetBasicsMeasurementsWithIntervalIncludedWithWrongResolution() {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"includeInterval" is only accepted with interval resolution');

        $json = file_get_contents(__DIR__ . '/responses/getBasicsMeasurements.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/basics/abbreviations/wr.E_INT/measurements'
                ),
                $this->identicalToUrl([
                    RequestOptions::QUERY => 'from=2016-01-01T00:00:00+02:00&to=2016-01-01T00:15:00+02:00&resolution=day&includeInterval=1'
                ])
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-01-01T00:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-01-01T00:15:00+02:00'))
            ->withResolution(MeasurementsCriteria::RESOLUTION_DAY)
            ->withIntervalIncluded();

        $this->api->system('ABCDE')->basics()->abbreviation('wr.E_INT')->measurements()->get($criteria);
    }

    public function testGetBasicsMeasurementsWithIntervalIncludedWithResolution() {
        $json = file_get_contents(__DIR__ . '/responses/getBasicsMeasurements.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/basics/abbreviations/wr.E_INT/measurements'
                ),
                $this->identicalToUrl([
                    RequestOptions::QUERY => 'from=2016-01-01T00:00:00+02:00&to=2016-01-01T00:15:00+02:00&resolution=interval&includeInterval=1'
                ])
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-01-01T00:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-01-01T00:15:00+02:00'))
            ->withResolution(MeasurementsCriteria::RESOLUTION_INTERVAL)
            ->withIntervalIncluded();


        /** @var MeasurementValueWithInterval[] $measurements */
        @$measurements = $this->api
            ->system('ABCDE')
            ->basics()
            ->abbreviation('wr.E_INT')
            ->measurements()
            ->get($criteria);
        $this->assertCount(1, $measurements);
        $measurement = $measurements['E_INT'];
        $this->assertEquals(0, $measurement[0]->value);
        $this->assertEquals(null, $measurement[0]->interval);
        $this->assertEquals('2016-01-01T00:00:00+02:00', $measurement[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(0, $measurement[1]->value);
        $this->assertEquals('2016-01-01T00:15:00+02:00', $measurement[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $measurement[1]->interval);
    }

    public function testGetBasicsBulkData() {
        $json = file_get_contents(__DIR__ . '/responses/getBasicsBulk.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo('systems/ABCDE/basics/bulk/measurements'),
                $this->identicalToUrl([
                    RequestOptions::QUERY => 'from=2016-11-01T10:00:00+02:00&to=2016-11-01T10:15:00+02:00'
                ])
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-11-01T10:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-11-01T10:15:00+02:00'));

        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->basics()->bulk()->measurements()->get($criteria);

        $this->assertEquals($json, $bulkReader->getAsString());
        $this->assertEquals(json_decode($json, true), $bulkReader->getAsArray());
    }

    public function testGetBasicsBulkDataWithAbbreviationsFilter() {
        $json = file_get_contents(__DIR__ . '/responses/getBasicsBulkWithAbbreviationsFilter.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo('systems/ABCDE/basics/bulk/measurements'),
                $this->identicalToUrl([
                    RequestOptions::QUERY => 'from=2016-11-01T10:00:00+02:00&to=2016-11-01T10:15:00+02:00&abbreviations=G_M2,H_ON'
                ])
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-11-01T10:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-11-01T10:15:00+02:00'))
            ->withAbbreviation(['G_M2', 'H_ON']);

        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->basics()->bulk()->measurements()->get($criteria);

        $this->assertEquals($json, $bulkReader->getAsString());
        $this->assertEquals(json_decode($json, true), $bulkReader->getAsArray());
    }

    public function testGetBasicsBulkDataWithCsvFormat() {
        $cvsRawData = file_get_contents(__DIR__ . '/responses/bulkCsv/getBasicBulk.csv');
        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo('systems/ABCDE/basics/bulk/measurements'),
                $this->identicalToUrl([
                    RequestOptions::QUERY => 'from=2016-09-01T10:00:00+02:00&to=2016-09-01T10:15:00+02:00&format=csv'
                ])
            )
            ->willReturn($cvsRawData);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-09-01T10:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-09-01T10:15:00+02:00'))
            ->withFormat(CsvFormat::FORMAT_CSV);
        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->basics()->bulk()->measurements()->get($criteria);

        $this->assertEquals($cvsRawData, $bulkReader->getAsString());
    }

    public function testGetBasicsBulkDataWithCsvFormatWithWrongParameter() {
        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-09-01T10:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-09-01T10:15:00+02:00'))
            ->withFormat(CsvFormat::FORMAT_CSV)
            ->withDelimiter(CsvFormat::DELIMITER_COMMA)
            ->withDecimalPoint(CsvFormat::DECIMAL_POINT_COMMA)
            ->withPrecision(CsvFormat::PRECISION_2);

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage("Delimiter and decimal point symbols can't be the same");

        $this->api->system('ABCDE')->basics()->bulk()->measurements()->get($criteria);
    }
}
