<?php

namespace meteocontrol\client\vcomapi\tests\unit\systems;

use GuzzleHttp\Client;
use meteocontrol\client\vcomapi\ApiClient;
use meteocontrol\client\vcomapi\Config;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\client\vcomapi\model\DevicesMeasurement;
use meteocontrol\client\vcomapi\model\Inverter;
use meteocontrol\client\vcomapi\model\InverterDetail;
use meteocontrol\client\vcomapi\readers\CsvFormat;
use meteocontrol\client\vcomapi\readers\MeasurementsBulkReader;

class InvertersTest extends \PHPUnit_Framework_TestCase {

    /** @var \PHPUnit_Framework_MockObject_MockObject | ApiClient */
    private $api;

    public function setup() {
        $config = new Config();
        $client = new Client();
        $this->api = $this->getMockBuilder('\meteocontrol\client\vcomapi\ApiClient')
            ->setConstructorArgs([$config, $client])
            ->setMethods(['run'])
            ->getMock();
    }

    public function testGetInverters() {
        $json = file_get_contents(__DIR__ . '/responses/getInverters.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/inverters'))
            ->willReturn($json);

        /** @var Inverter[] $inverters */
        $inverters = $this->api->system('ABCDE')->inverters()->get();

        $this->assertEquals(1, count($inverters));
        $this->assertEquals('Id30773.25', $inverters[0]->id);
        $this->assertEquals('Halle A - WR 1', $inverters[0]->name);
        $this->assertEquals('123456789', $inverters[0]->serial);
    }

    public function testGetSingleInveter() {
        $json = file_get_contents(__DIR__ . '/responses/getInverter.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/inverters/Id30773.25'))
            ->willReturn($json);

        /** @var InverterDetail $inverter */
        $inverter = $this->api->system('ABCDE')->inverter('Id30773.25')->get();

        $this->assertEquals('Id30773.25', $inverter->id);
        $this->assertEquals('TLX 15 k', $inverter->model);
        $this->assertEquals('Danfoss', $inverter->vendor);
        $this->assertEquals('123456789', $inverter->serial);
        $this->assertEquals('Halle A - WR 1', $inverter->name);
    }

    public function testGetInverterAbbreviations() {
        $json = file_get_contents(__DIR__ . '/responses/getInverterAbbreviations.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/inverters/Id30773.25/abbreviations'))
            ->willReturn($json);

        /** @var string[] $abbreviations */
        $abbreviations = $this->api->system('ABCDE')->inverter('Id30773.25')->abbreviations()->get();

        $this->assertEquals(2, count($abbreviations));
        $this->assertEquals('E_TOTAL', $abbreviations[0]);
        $this->assertEquals('E_INT', $abbreviations[1]);
    }

    public function testGetInverterSingleAbbreviation() {
        $json = file_get_contents(__DIR__ . '/responses/getInverterSingleAbbreviation.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/inverters/Id30773.25/abbreviations/E_TOTAL'))
            ->willReturn($json);

        /** @var \meteocontrol\client\vcomapi\model\Abbreviation $abbreviation */
        $abbreviation = $this->api->system('ABCDE')->inverter('Id30773.25')->abbreviation('E_TOTAL')->get();

        $this->assertEquals('MAX', $abbreviation->aggregation);
        $this->assertEquals('Total Energy', $abbreviation->description);
        $this->assertEquals('0', $abbreviation->precision);
        $this->assertEquals('kWh', $abbreviation->unit);
    }

    public function testGetInverterMeasurements() {
        $json = file_get_contents(__DIR__ . '/responses/getInverterMeasurements.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/inverters/Id12345.1/abbreviations/E_INT/measurements'
                ),
                $this->identicalTo(
                    'from=2016-01-01T00%3A00%3A00%2B00%3A00&to=2016-01-02T23%3A59%3A59%2B00%3A00&resolution=day'
                )
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-01-01T00:00:00+00:00'))
            ->withDateTo(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-01-02T23:59:59+00:00'))
            ->withResolution(DevicesMeasurement::RESOLUTION_DAY);

        /** @var DevicesMeasurement $measurements */
        $measurements = $this->api->system('ABCDE')->inverter('Id12345.1')->abbreviation('E_INT')->measurements()
            ->get($criteria);

        $this->assertEquals(1, count($measurements));
        $abbreviationsMeasurements = $measurements['Id12345.1'];
        $values = $abbreviationsMeasurements['E_INT'];
        $this->assertEquals(5, count($values));
        $this->assertEquals(0.089, $values[0]->value);
        $this->assertEquals('2016-01-01 11:00:00', $values[0]->timestamp->format('Y-m-d H:i:s'));
        $this->assertEquals(0.082, $values[1]->value);
        $this->assertEquals('2016-01-01 11:15:00', $values[1]->timestamp->format('Y-m-d H:i:s'));
        $this->assertEquals(0.078, $values[2]->value);
        $this->assertEquals('2016-01-01 11:30:00', $values[2]->timestamp->format('Y-m-d H:i:s'));
        $this->assertEquals(0.089, $values[3]->value);
        $this->assertEquals('2016-01-01 11:45:00', $values[3]->timestamp->format('Y-m-d H:i:s'));
        $this->assertEquals(0.095, $values[4]->value);
        $this->assertEquals('2016-01-01 12:00:00', $values[4]->timestamp->format('Y-m-d H:i:s'));
    }

    public function testGetInverterBulkData() {
        $json = file_get_contents(__DIR__ . '/responses/getInverterBulk.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/inverters/bulk/measurements'),
                $this->identicalTo('from=2016-09-01T10%3A00%3A00%2B02%3A00&to=2016-09-01T10%3A15%3A00%2B02%3A00')
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-09-01T10:00:00+02:00'))
            ->withDateTo(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-09-01T10:15:00+02:00'));

        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->inverters()->bulk()->measurements()->get($criteria);

        $this->assertEquals($json, $bulkReader->getAsString());
        $this->assertEquals(json_decode($json, true), $bulkReader->getAsArray());
    }

    public function testGetSensorsBulkDataWithCsvFormat() {
        $cvsRawData = file_get_contents(__DIR__ . '/responses/bulkCsv/getInverterBulk.csv');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/inverters/bulk/measurements'),
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
        $bulkReader = $this->api->system('ABCDE')->inverters()->bulk()->measurements()->get($criteria);

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
        $this->api->system('ABCDE')->inverters()->bulk()->measurements()->get($criteria);
    }
}
