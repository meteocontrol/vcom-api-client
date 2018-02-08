<?php

namespace meteocontrol\client\vcomapi\tests\unit\systems;

use GuzzleHttp\Client;
use meteocontrol\client\vcomapi\ApiClient;
use meteocontrol\client\vcomapi\Config;
use meteocontrol\client\vcomapi\endpoints\sub\systems\Sensors;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\client\vcomapi\handlers\BasicAuthorizationHandler;
use meteocontrol\client\vcomapi\model\DevicesMeasurement;
use meteocontrol\client\vcomapi\model\Measurement;
use meteocontrol\client\vcomapi\model\SensorDetail;
use meteocontrol\client\vcomapi\readers\CsvFormat;
use meteocontrol\client\vcomapi\readers\MeasurementsBulkReader;

class SensorsTest extends \PHPUnit_Framework_TestCase {

    /** @var \PHPUnit_Framework_MockObject_MockObject | ApiClient */
    private $api;

    public function setup() {
        $config = new Config();
        $client = new Client();
        $authHandler = new BasicAuthorizationHandler($config);
        $this->api = $this->getMockBuilder('\meteocontrol\client\vcomapi\ApiClient')
            ->setConstructorArgs([$client, $authHandler])
            ->setMethods(['run'])
            ->getMock();
    }

    public function testGetSensors() {
        $json = file_get_contents(__DIR__ . '/responses/getSensors.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/sensors'))
            ->willReturn($json);

        /** @var Sensors[] $sensors */
        $sensors = $this->api->system('ABCDE')->sensors()->get();

        $this->assertEquals(3, count($sensors));
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

        $this->assertEquals(2, count($abbreviations));
        $this->assertEquals('E_AH_ABS', $abbreviations[0]);
        $this->assertEquals('E_AH_ABS1', $abbreviations[1]);
    }

    public function testGetSensorSingleAbbreviation() {
        $json = file_get_contents(__DIR__ . '/responses/getSensorSingleAbbreviation.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/sensors/126222/abbreviations/GM_3'))
            ->willReturn($json);

        /** @var \meteocontrol\client\vcomapi\model\Abbreviation $abbreviation */
        $abbreviation = $this->api->system('ABCDE')->sensor('126222')->abbreviation(['GM_3'])->get();

        $this->assertEquals('AVG', $abbreviation->aggregation);
        $this->assertEquals('Irradiance on module plane, subsystem 3', $abbreviation->description);
        $this->assertEquals('3', $abbreviation->precision);
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
                $this->identicalTo(
                    'from=2016-01-01T00%3A00%3A00%2B02%3A00&to=2016-01-02T23%3A59%3A59%2B02%3A00&resolution=interval'
                )
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-01-01T00:00:00+02:00'))
            ->withDateTo(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-01-02T23:59:59+02:00'))
            ->withResolution(MeasurementsCriteria::RESOLUTION_INTERVAL);

        /** @var DevicesMeasurement $measurements */
        $measurements = $this->api->system('ABCDE')->sensor('123')->abbreviation('T_M1,G_M3')->measurements()
            ->get($criteria);

        $this->assertEquals(1, count($measurements));
        $abbreviationsMeasurements = $measurements['123'];
        $values = $abbreviationsMeasurements['T_M1'];
        $this->assertEquals(2, count($values));
        $this->assertEquals(2.054, $values[0]->value);
        $this->assertEquals('2016-01-01 11:00:00', $values[0]->timestamp->format('Y-m-d H:i:s'));
        $this->assertEquals(2.049, $values[1]->value);
        $this->assertEquals('2016-01-01 11:15:00', $values[1]->timestamp->format('Y-m-d H:i:s'));
        $values = $abbreviationsMeasurements['G_M3'];
        $this->assertEquals(2, count($values));
        $this->assertEquals(13.16, $values[0]->value);
        $this->assertEquals('2016-01-01 11:00:00', $values[0]->timestamp->format('Y-m-d H:i:s'));
        $this->assertEquals(13.03, $values[1]->value);
        $this->assertEquals('2016-01-01 11:15:00', $values[1]->timestamp->format('Y-m-d H:i:s'));
    }

    public function testGetSensorsBulkData() {
        $json = file_get_contents(__DIR__ . '/responses/getSensorBulk.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/sensors/bulk/measurements'),
                $this->identicalTo('from=2016-09-01T10%3A00%3A00%2B02%3A00&to=2016-09-01T10%3A15%3A00%2B02%3A00')
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-09-01T10:00:00+02:00'))
            ->withDateTo(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-09-01T10:15:00+02:00'));

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
                $this->identicalTo(
                    'from=2016-09-01T10%3A00%3A00%2B02%3A00&to=2016-09-01T10%3A15%3A00%2B02%3A00&format=csv'
                )
            )
            ->willReturn($cvsRawData);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-09-01T10:00:00+02:00'))
            ->withDateTo(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-09-01T10:15:00+02:00'))
            ->withFormat(CsvFormat::FORMAT_CSV);
        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->sensors()->bulk()->measurements()->get($criteria);

        $this->assertEquals($cvsRawData, $bulkReader->getAsString());
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Delimiter and decimal point symbols can't be the same
     */
    public function testGetSensorsBulkDataWithCsvFormatWithWrongParameter() {
        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-09-01T10:00:00+02:00'))
            ->withDateTo(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-09-01T10:15:00+02:00'))
            ->withFormat(CsvFormat::FORMAT_CSV)
            ->withDelimiter(CsvFormat::DELIMITER_COLON)
            ->withDecimalPoint(CsvFormat::DECIMAL_POINT_COLON);
        $this->api->system('ABCDE')->sensors()->bulk()->measurements()->get($criteria);
    }
}
