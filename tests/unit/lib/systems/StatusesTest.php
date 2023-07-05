<?php

namespace meteocontrol\client\vcomapi\tests\unit\systems;

use DateTime;
use meteocontrol\client\vcomapi\endpoints\sub\systems\Statuses;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\client\vcomapi\model\DevicesMeasurement;
use meteocontrol\client\vcomapi\model\DevicesMeasurementWithInterval;
use meteocontrol\client\vcomapi\model\StatusDetail;
use meteocontrol\client\vcomapi\readers\CsvFormat;
use meteocontrol\client\vcomapi\readers\MeasurementsBulkReader;
use meteocontrol\client\vcomapi\tests\unit\TestCase;
use UnexpectedValueException;

class StatusesTest extends TestCase {

    public function testGetStatuses() {
        $json = file_get_contents(__DIR__ . '/responses/getStatuses.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/statuses'))
            ->willReturn($json);

        /** @var Statuses[] $statuses */
        $statuses = $this->api->system('ABCDE')->statuses()->get();

        $this->assertCount(2, $statuses);
        $this->assertEquals(10001, $statuses[0]->id);
        $this->assertEquals('Meldung LS TST', $statuses[0]->name);
        $this->assertEquals(10002, $statuses[1]->id);
        $this->assertEquals('Warnung Trafotemperatur', $statuses[1]->name);
    }

    public function testGetSingleStatus() {
        $json = file_get_contents(__DIR__ . '/responses/getStatus.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/statuses/10001'))
            ->willReturn($json);

        /** @var StatusDetail $status */
        $status = $this->api->system('ABCDE')->status('10001')->get();

        $this->assertEquals(10001, $status->id);
        $this->assertEquals('Meldung LS TST', $status->name);
        $this->assertEquals('BT1980004134-D_IN1', $status->address);
        $this->assertEquals('Huawei', $status->vendor);
        $this->assertEquals('SmartLogger 2000 DI Status', $status->model);
        $this->assertEquals('V200R002C20SPC119', $status->firmware);
    }

    public function testGetStatusAbbreviations() {
        $json = file_get_contents(__DIR__ . '/responses/getStatusAbbreviations.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/statuses/10001/abbreviations'))
            ->willReturn($json);

        /** @var string[] $abbreviations */
        $abbreviations = $this->api->system('ABCDE')->status('10001')->abbreviations()->get();

        $this->assertCount(2, $abbreviations);
        $this->assertEquals('STATE1', $abbreviations[0]);
        $this->assertEquals('STATE2', $abbreviations[1]);
    }

    public function testGetStatusMeasurements() {
        $json = file_get_contents(__DIR__ . '/responses/getStatusMeasurements.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/statuses/10001/abbreviations/STATE1,STATE2/measurements'
                ),
                $this->identicalToUrl(
                    'from=2016-01-01T00:00:00+02:00&to=2016-01-02T23:59:59+02:00&resolution=interval'
                )
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DateTime::RFC3339, '2016-01-01T00:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DateTime::RFC3339, '2016-01-02T23:59:59+02:00'))
            ->withResolution(MeasurementsCriteria::RESOLUTION_INTERVAL);

        /** @var DevicesMeasurement $measurements */
        $measurements = $this->api->system('ABCDE')->status('10001')->abbreviation('STATE1,STATE2')->measurements()
            ->get($criteria);

        $this->assertCount(1, $measurements);
        $abbreviationsMeasurements = $measurements['10001'];
        $values = $abbreviationsMeasurements['STATE1'];
        $this->assertCount(2, $values);
        $this->assertEquals(1, $values[0]->value);
        $this->assertEquals('2016-01-01T11:00:00+02:00', $values[0]->timestamp->format(DateTime::RFC3339));
        $this->assertEquals(0, $values[1]->value);
        $this->assertEquals('2016-01-01T11:15:00+02:00', $values[1]->timestamp->format(DateTime::RFC3339));
        $values = $abbreviationsMeasurements['STATE2'];
        $this->assertCount(2, $values);
        $this->assertEquals(1, $values[0]->value);
        $this->assertEquals('2016-01-01T11:00:00+02:00', $values[0]->timestamp->format(DateTime::RFC3339));
        $this->assertEquals(1, $values[1]->value);
        $this->assertEquals('2016-01-01T11:15:00+02:00', $values[1]->timestamp->format(DateTime::RFC3339));
    }

    public function testGetStatusMeasurementsWithIntervalIncluded() {
        $json = file_get_contents(__DIR__ . '/responses/getStatusMeasurementsIncludeInterval.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/statuses/10001/abbreviations/STATE1,STATE2/measurements'
                ),
                $this->identicalToUrl(
                    'from=2016-01-01T00:00:00+02:00&to=2016-01-02T23:59:59+02:00&resolution=interval&includeInterval=1'
                )
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DateTime::RFC3339, '2016-01-01T00:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DateTime::RFC3339, '2016-01-02T23:59:59+02:00'))
            ->withResolution(MeasurementsCriteria::RESOLUTION_INTERVAL)
            ->withIntervalIncluded();

        /** @var DevicesMeasurementWithInterval $measurements */
        $measurements = $this->api->system('ABCDE')->status('10001')->abbreviation('STATE1,STATE2')->measurements()
            ->get($criteria);

        $this->assertCount(1, $measurements);
        $abbreviationsMeasurements = $measurements['10001'];
        $values = $abbreviationsMeasurements['STATE1'];
        $this->assertCount(2, $values);
        $this->assertEquals(1, $values[0]->value);
        $this->assertEquals('2016-01-01T11:00:00+02:00', $values[0]->timestamp->format(DateTime::RFC3339));
        $this->assertEquals(300, $values[0]->interval);
        $this->assertEquals(0, $values[1]->value);
        $this->assertEquals('2016-01-01T11:15:00+02:00', $values[1]->timestamp->format(DateTime::RFC3339));
        $this->assertEquals(300, $values[1]->interval);

        $values = $abbreviationsMeasurements['STATE2'];
        $this->assertCount(2, $values);
        $this->assertEquals(1, $values[0]->value);
        $this->assertEquals('2016-01-01T11:00:00+02:00', $values[0]->timestamp->format(DateTime::RFC3339));
        $this->assertEquals(300, $values[0]->interval);
        $this->assertEquals(1, $values[1]->value);
        $this->assertEquals('2016-01-01T11:15:00+02:00', $values[1]->timestamp->format(DateTime::RFC3339));
        $this->assertEquals(300, $values[1]->interval);
    }

    public function testGetStatusesBulkData() {
        $json = file_get_contents(__DIR__ . '/responses/getStatusBulk.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/statuses/bulk/measurements'),
                $this->identicalToUrl('from=2016-09-01T10:00:00+02:00&to=2016-09-01T10:15:00+02:00')
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DateTime::RFC3339, '2016-09-01T10:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DateTime::RFC3339, '2016-09-01T10:15:00+02:00'));

        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->statuses()->bulk()->measurements()->get($criteria);

        $this->assertEquals($json, $bulkReader->getAsString());
        $this->assertEquals(json_decode($json, true), $bulkReader->getAsArray());
    }

    public function testGetStatusesBulkDataWithAbbreviationsFilter() {
        $json = file_get_contents(__DIR__ . '/responses/getStatusBulkWithAbbreviationsFilter.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/statuses/bulk/measurements'),
                $this->identicalToUrl(
                    'from=2016-09-01T10:00:00+02:00&to=2016-09-01T10:15:00+02:00&abbreviations=STATE1'
                )
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DateTime::RFC3339, '2016-09-01T10:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DateTime::RFC3339, '2016-09-01T10:15:00+02:00'))
            ->withAbbreviation(['STATE1']);

        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->statuses()->bulk()->measurements()->get($criteria);

        $this->assertEquals($json, $bulkReader->getAsString());
        $this->assertEquals(json_decode($json, true), $bulkReader->getAsArray());
    }

    public function testGetStatusesBulkDataWithCsvFormat() {
        $cvsRawData = file_get_contents(__DIR__ . '/responses/bulkCsv/getStatusBulk.csv');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/statuses/bulk/measurements'),
                $this->identicalToUrl(
                    'from=2016-09-01T10:00:00+02:00&to=2016-09-01T10:15:00+02:00&format=csv'
                )
            )
            ->willReturn($cvsRawData);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DateTime::RFC3339, '2016-09-01T10:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DateTime::RFC3339, '2016-09-01T10:15:00+02:00'))
            ->withFormat(CsvFormat::FORMAT_CSV);
        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->statuses()->bulk()->measurements()->get($criteria);

        $this->assertEquals($cvsRawData, $bulkReader->getAsString());
    }

    public function testGetStatusesBulkDataWithCsvFormatWithWrongParameter() {
        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DateTime::RFC3339, '2016-09-01T10:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DateTime::RFC3339, '2016-09-01T10:15:00+02:00'))
            ->withFormat(CsvFormat::FORMAT_CSV)
            ->withDelimiter(CsvFormat::DELIMITER_COMMA)
            ->withDecimalPoint(CsvFormat::DECIMAL_POINT_COMMA)
            ->withPrecision(CsvFormat::PRECISION_2);

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage("Delimiter and decimal point symbols can't be the same");

        $this->api->system('ABCDE')->statuses()->bulk()->measurements()->get($criteria);
    }
}
