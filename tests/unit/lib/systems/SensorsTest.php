<?php

namespace meteocontrol\client\vcomapi\tests\unit\systems;

use DateTime;
use meteocontrol\client\vcomapi\endpoints\sub\systems\Sensors;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\client\vcomapi\model\Abbreviation;
use meteocontrol\client\vcomapi\model\DevicesMeasurement;
use meteocontrol\client\vcomapi\model\DevicesMeasurementWithInterval;
use meteocontrol\client\vcomapi\model\SensorDetail;
use meteocontrol\client\vcomapi\readers\CsvFormat;
use meteocontrol\client\vcomapi\readers\MeasurementsBulkReader;
use meteocontrol\client\vcomapi\tests\unit\TestCase;
use PHPUnit\Framework\Error\Notice;
use UnexpectedValueException;

class SensorsTest extends TestCase {

    public function testGetSensors() {
        $json = file_get_contents(__DIR__ . '/responses/getSensors.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/sensors'))
            ->willReturn($json);

        /** @var Sensors[] $sensors */
        $sensors = $this->api->system('ABCDE')->sensors()->get();

        $this->assertCount(3, $sensors);
        $this->assertEquals(126222, $sensors[0]->id);
        $this->assertEquals('Pyranometer SMPx (Modbus)', $sensors[0]->name);
        $this->assertEquals(126312, $sensors[1]->id);
        $this->assertEquals('Irradiation sensor M&T / mc Si-420TC-T (4 - 20mA)', $sensors[1]->name);
        $this->assertEquals(126313, $sensors[2]->id);
        $this->assertEquals('Temperature sensor PT1000 sensor with integrated converter (0 - 10V)', $sensors[2]->name);
    }

    public function testGetSingleSensor() {
        $json = file_get_contents(__DIR__ . '/responses/getSensor.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/sensors/126222'))
            ->willReturn($json);

        /** @var SensorDetail $sensor */
        $sensor = $this->api->system('ABCDE')->sensor('126222')->get();

        $this->assertEquals(126222, $sensor->id);
        $this->assertEquals('Pyranometer SMPx (Modbus)', $sensor->name);
        $this->assertEquals('7', $sensor->address);
        $this->assertEquals('1.0', $sensor->firmware);
    }

    public function testGetSensorAbbreviations() {
        $json = file_get_contents(__DIR__ . '/responses/getSensorAbbreviations.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/sensors/126222/abbreviations'))
            ->willReturn($json);

        /** @var string[] $abbreviations */
        $abbreviations = $this->api->system('ABCDE')->sensor('126222')->abbreviations()->get();

        $this->assertCount(2, $abbreviations);
        $this->assertEquals('E_AH_ABS', $abbreviations[0]);
        $this->assertEquals('E_AH_ABS1', $abbreviations[1]);
    }

    public function testGetSensorSingleAbbreviation() {
        $json = file_get_contents(__DIR__ . '/responses/getSensorSingleAbbreviation.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/sensors/126222/abbreviations/GM_3'))
            ->willReturn($json);

        /** @var Abbreviation $abbreviation */
        $abbreviation = $this->api->system('ABCDE')->sensor('126222')->abbreviation(['GM_3'])->get();

        $this->assertEquals('AVG', $abbreviation->aggregation);
        $this->assertEquals('Irradiance on module plane, subsystem 3', $abbreviation->description);
        $this->assertEquals(3, $abbreviation->precision);
        $this->assertEquals('W/m²', $abbreviation->unit);
    }

    public function testGetSensorMeasurements() {
        $json = file_get_contents(__DIR__ . '/responses/getSensorMeasurements.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/sensors/123/abbreviations/T_M1,G_M3/measurements'
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
        $measurements = $this->api->system('ABCDE')->sensor('123')->abbreviation('T_M1,G_M3')->measurements()
            ->get($criteria);

        $this->assertCount(1, $measurements);
        $abbreviationsMeasurements = $measurements['123'];
        $values = $abbreviationsMeasurements['T_M1'];
        $this->assertCount(2, $values);
        $this->assertEquals(2.054, $values[0]->value);
        $this->assertEquals('2016-01-01T11:00:00+02:00', $values[0]->timestamp->format(DateTime::RFC3339));
        $this->assertEquals(2.049, $values[1]->value);
        $this->assertEquals('2016-01-01T11:15:00+02:00', $values[1]->timestamp->format(DateTime::RFC3339));
        $values = $abbreviationsMeasurements['G_M3'];
        $this->assertCount(2, $values);
        $this->assertEquals(13.16, $values[0]->value);
        $this->assertEquals('2016-01-01T11:00:00+02:00', $values[0]->timestamp->format(DateTime::RFC3339));
        $this->assertEquals(13.03, $values[1]->value);
        $this->assertEquals('2016-01-01T11:15:00+02:00', $values[1]->timestamp->format(DateTime::RFC3339));
    }

    public function testGetSensorMeasurementsWithIntervalIncluded() {
        $json = file_get_contents(__DIR__ . '/responses/getSensorMeasurementsIncludeInterval.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/sensors/123/abbreviations/T_M1,G_M3/measurements'
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
        $measurements = $this->api->system('ABCDE')->sensor('123')->abbreviation('T_M1,G_M3')->measurements()
            ->get($criteria);

        $this->assertCount(1, $measurements);
        $abbreviationsMeasurements = $measurements['123'];
        $values = $abbreviationsMeasurements['T_M1'];
        $this->assertCount(2, $values);
        $this->assertEquals(2.054, $values[0]->value);
        $this->assertEquals('2016-01-01T11:00:00+02:00', $values[0]->timestamp->format(DateTime::RFC3339));
        $this->assertEquals(300, $values[0]->interval);
        $this->assertEquals(2.049, $values[1]->value);
        $this->assertEquals('2016-01-01T11:15:00+02:00', $values[1]->timestamp->format(DateTime::RFC3339));
        $this->assertEquals(300, $values[1]->interval);

        $values = $abbreviationsMeasurements['G_M3'];
        $this->assertCount(2, $values);
        $this->assertEquals(13.16, $values[0]->value);
        $this->assertEquals('2016-01-01T11:00:00+02:00', $values[0]->timestamp->format(DateTime::RFC3339));
        $this->assertEquals(300, $values[0]->interval);
        $this->assertEquals(13.03, $values[1]->value);
        $this->assertEquals('2016-01-01T11:15:00+02:00', $values[1]->timestamp->format(DateTime::RFC3339));
        $this->assertEquals(300, $values[1]->interval);
    }

    public function testGetSensorMeasurementsWithIntervalIncludedWithWrongResolution() {
        $json = file_get_contents(__DIR__ . '/responses/getSensorMeasurements.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/sensors/123/abbreviations/T_M1,G_M3/measurements'
                ),
                $this->identicalToUrl(
                    'from=2016-01-01T00:00:00+02:00&to=2016-01-02T23:59:59+02:00&resolution=day&includeInterval=1'
                )
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DateTime::RFC3339, '2016-01-01T00:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DateTime::RFC3339, '2016-01-02T23:59:59+02:00'))
            ->withResolution(MeasurementsCriteria::RESOLUTION_DAY)
            ->withIntervalIncluded();

        $this>$this->expectException(Notice::class);

        $this->api->system('ABCDE')->sensor('123')->abbreviation('T_M1,G_M3')->measurements()
            ->get($criteria);
    }

    public function testGetSensorMeasurementsWithIntervalIncludedWithWrongResolution2() {
        $json = file_get_contents(__DIR__ . '/responses/getSensorMeasurements.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/sensors/123/abbreviations/T_M1,G_M3/measurements'
                ),
                $this->identicalToUrl(
                    'from=2016-01-01T00:00:00+02:00&to=2016-01-02T23:59:59+02:00&resolution=day&includeInterval=1'
                )
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DateTime::RFC3339, '2016-01-01T00:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DateTime::RFC3339, '2016-01-02T23:59:59+02:00'))
            ->withResolution(MeasurementsCriteria::RESOLUTION_DAY)
            ->withIntervalIncluded();

        /** @var DevicesMeasurementWithInterval $measurements */
        @$measurements = $this->api->system('ABCDE')->sensor('123')->abbreviation('T_M1,G_M3')->measurements()
            ->get($criteria);

        $this->assertCount(1, $measurements);
        $abbreviationsMeasurements = $measurements['123'];
        $values = $abbreviationsMeasurements['T_M1'];
        $this->assertCount(2, $values);
        $this->assertEquals(2.054, $values[0]->value);
        $this->assertEquals('2016-01-01T11:00:00+02:00', $values[0]->timestamp->format(DateTime::RFC3339));
        $this->assertEquals(null, $values[0]->interval);
        $this->assertEquals(2.049, $values[1]->value);
        $this->assertEquals('2016-01-01T11:15:00+02:00', $values[1]->timestamp->format(DateTime::RFC3339));
        $this->assertEquals(null, $values[1]->interval);

        $values = $abbreviationsMeasurements['G_M3'];
        $this->assertCount(2, $values);
        $this->assertEquals(13.16, $values[0]->value);
        $this->assertEquals('2016-01-01T11:00:00+02:00', $values[0]->timestamp->format(DateTime::RFC3339));
        $this->assertEquals(null, $values[0]->interval);
        $this->assertEquals(13.03, $values[1]->value);
        $this->assertEquals('2016-01-01T11:15:00+02:00', $values[1]->timestamp->format(DateTime::RFC3339));
        $this->assertEquals(null, $values[1]->interval);
    }

    public function testGetSensorsBulkData() {
        $json = file_get_contents(__DIR__ . '/responses/getSensorBulk.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/sensors/bulk/measurements'),
                $this->identicalToUrl('from=2016-09-01T10:00:00+02:00&to=2016-09-01T10:15:00+02:00')
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DateTime::RFC3339, '2016-09-01T10:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DateTime::RFC3339, '2016-09-01T10:15:00+02:00'));

        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->sensors()->bulk()->measurements()->get($criteria);

        $this->assertEquals($json, $bulkReader->getAsString());
        $this->assertEquals(json_decode($json, true), $bulkReader->getAsArray());
    }

    public function testGetSensorsBulkDataWithAbbreviationsFilter() {
        $json = file_get_contents(__DIR__ . '/responses/getSensorBulkWithAbbreviationsFilter.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/sensors/bulk/measurements'),
                $this->identicalToUrl(
                    'from=2016-09-01T10:00:00+02:00&to=2016-09-01T10:15:00+02:00&abbreviations=E_AH_ABS'
                )
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DateTime::RFC3339, '2016-09-01T10:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DateTime::RFC3339, '2016-09-01T10:15:00+02:00'))
            ->withAbbreviation(['E_AH_ABS']);

        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->sensors()->bulk()->measurements()->get($criteria);

        $this->assertEquals($json, $bulkReader->getAsString());
        $this->assertEquals(json_decode($json, true), $bulkReader->getAsArray());
    }

    public function testGetSensorsBulkDataWithCsvFormat() {
        $cvsRawData = file_get_contents(__DIR__ . '/responses/bulkCsv/getSensorBulk.csv');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/sensors/bulk/measurements'),
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
        $bulkReader = $this->api->system('ABCDE')->sensors()->bulk()->measurements()->get($criteria);

        $this->assertEquals($cvsRawData, $bulkReader->getAsString());
    }

    public function testGetSensorsBulkDataWithCsvFormatWithWrongParameter() {
        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DateTime::RFC3339, '2016-09-01T10:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DateTime::RFC3339, '2016-09-01T10:15:00+02:00'))
            ->withFormat(CsvFormat::FORMAT_CSV)
            ->withDelimiter(CsvFormat::DELIMITER_COMMA)
            ->withDecimalPoint(CsvFormat::DECIMAL_POINT_COMMA)
            ->withPrecision(CsvFormat::PRECISION_2);

        $this>$this->expectException(UnexpectedValueException::class);
        $this>$this->expectExceptionMessage("Delimiter and decimal point symbols can't be the same");

        $this->api->system('ABCDE')->sensors()->bulk()->measurements()->get($criteria);
    }
}
