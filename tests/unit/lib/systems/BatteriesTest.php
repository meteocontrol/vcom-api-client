<?php

namespace meteocontrol\client\vcomapi\tests\unit\systems;

use GuzzleHttp\Client;
use meteocontrol\client\vcomapi\ApiClient;
use meteocontrol\client\vcomapi\Config;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\client\vcomapi\handlers\BasicAuthorizationHandler;
use meteocontrol\client\vcomapi\model\DevicesMeasurement;
use meteocontrol\client\vcomapi\model\Battery;
use meteocontrol\client\vcomapi\model\BatteryDetail;
use meteocontrol\client\vcomapi\readers\CsvFormat;
use meteocontrol\client\vcomapi\readers\MeasurementsBulkReader;

class BatteriesTest extends \PHPUnit_Framework_TestCase {

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

    public function testGetBatteries() {
        $json = file_get_contents(__DIR__ . '/responses/getBatteries.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/batteries'))
            ->willReturn($json);

        /** @var Battery[] $batteries */
        $batteries = $this->api->system('ABCDE')->batteries()->get();

        $this->assertEquals(2, count($batteries));
        $this->assertEquals('145103', $batteries[0]->id);
        $this->assertEquals('', $batteries[0]->name);
        $this->assertEquals('145104', $batteries[1]->id);
        $this->assertEquals('', $batteries[1]->name);
    }

    public function testGetSingleBattery() {
        $json = file_get_contents(__DIR__ . '/responses/getBattery.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/batteries/145103'))
            ->willReturn($json);

        /** @var BatteryDetail $battery */
        $battery = $this->api->system('ABCDE')->battery('145103')->get();

        $this->assertEquals(145103, $battery->id);
        $this->assertEquals('', $battery->name);
        $this->assertEquals('bat1', $battery->address);
    }

    public function testGetBatteryAbbreviations() {
        $json = file_get_contents(__DIR__ . '/responses/getBatteryAbbreviations.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/batteries/145103/abbreviations'))
            ->willReturn($json);

        /** @var string[] $abbreviations */
        $abbreviations = $this->api->system('ABCDE')->battery('145103')->abbreviations()->get();

        $this->assertEquals(4, count($abbreviations));
        $this->assertEquals('B_CHARGE_LEVEL', $abbreviations[0]);
        $this->assertEquals('B_E_EXP', $abbreviations[1]);
        $this->assertEquals('B_E_IMP', $abbreviations[2]);
        $this->assertEquals('T1', $abbreviations[3]);
    }

    public function testGetBatterySingleAbbreviation() {
        $json = file_get_contents(__DIR__ . '/responses/getBatterySingleAbbreviation.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/batteries/145103/abbreviations/B_CHARGE_LEVEL'))
            ->willReturn($json);

        /** @var \meteocontrol\client\vcomapi\model\Abbreviation $abbreviation */
        $abbreviation = $this->api->system('ABCDE')->battery('145103')->abbreviation('B_CHARGE_LEVEL')->get();

        $this->assertEquals('AVG', $abbreviation->aggregation);
        $this->assertEquals('Charging status', $abbreviation->description);
        $this->assertEquals(2, $abbreviation->precision);
        $this->assertEquals('%', $abbreviation->unit);
    }

    public function testGetBatteryMeasurements() {
        $json = file_get_contents(__DIR__ . '/responses/getBatteryMeasurements.json');
        $this->api->expects($this->exactly(1))
            ->method('run')
        ->with(
            $this->identicalTo(
                'systems/ABCDE/batteries/145103,145104/abbreviations/B_CHARGE_LEVEL,B_E_EXP/measurements'
            ),
            $this->identicalTo(
                'from=2016-10-10T11%3A00%3A00%2B02%3A00&to=2016-10-10T11%3A15%3A00%2B02%3A00'
            )
        )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-10-10T11:00:00+02:00'))
            ->withDateTo(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-10-10T11:15:00+02:00'));

        /** @var DevicesMeasurement $measurements */
        $measurements = $this->api->system('ABCDE')->battery('145103,145104')
            ->abbreviation(['B_CHARGE_LEVEL', 'B_E_EXP'])
            ->measurements()->get($criteria);
        $this->assertEquals(2, count($measurements));
        $abbreviationsMeasurements = $measurements['145103'];
        $values = $abbreviationsMeasurements['B_CHARGE_LEVEL'];
        $this->assertEquals(4, count($values));
        $this->assertEquals(80.762, $values[0]->value);
        $this->assertEquals('2016-10-10 11:00:00', $values[0]->timestamp->format('Y-m-d H:i:s'));
        $this->assertEquals(80.782, $values[1]->value);
        $this->assertEquals('2016-10-10 11:05:00', $values[1]->timestamp->format('Y-m-d H:i:s'));
        $this->assertEquals(80.802, $values[2]->value);
        $this->assertEquals('2016-10-10 11:10:00', $values[2]->timestamp->format('Y-m-d H:i:s'));
        $this->assertEquals(80.822, $values[3]->value);
        $this->assertEquals('2016-10-10 11:15:00', $values[3]->timestamp->format('Y-m-d H:i:s'));
        $values = $abbreviationsMeasurements['B_E_EXP'];
        $this->assertEquals(4, count($values));
        $this->assertEquals(1347.762, $values[0]->value);
        $this->assertEquals('2016-10-10 11:00:00', $values[0]->timestamp->format('Y-m-d H:i:s'));
        $this->assertEquals(1347.782, $values[1]->value);
        $this->assertEquals('2016-10-10 11:05:00', $values[1]->timestamp->format('Y-m-d H:i:s'));
        $this->assertEquals(1347.802, $values[2]->value);
        $this->assertEquals('2016-10-10 11:10:00', $values[2]->timestamp->format('Y-m-d H:i:s'));
        $this->assertEquals(1347.822, $values[3]->value);
        $this->assertEquals('2016-10-10 11:15:00', $values[3]->timestamp->format('Y-m-d H:i:s'));

        $abbreviationsMeasurements = $measurements['145104'];
        $values = $abbreviationsMeasurements['B_CHARGE_LEVEL'];
        $this->assertEquals(4, count($values));
        $this->assertEquals(80.772, $values[0]->value);
        $this->assertEquals('2016-10-10 11:00:00', $values[0]->timestamp->format('Y-m-d H:i:s'));
        $this->assertEquals(80.792, $values[1]->value);
        $this->assertEquals('2016-10-10 11:05:00', $values[1]->timestamp->format('Y-m-d H:i:s'));
        $this->assertEquals(80.812, $values[2]->value);
        $this->assertEquals('2016-10-10 11:10:00', $values[2]->timestamp->format('Y-m-d H:i:s'));
        $this->assertEquals(80.832, $values[3]->value);
        $this->assertEquals('2016-10-10 11:15:00', $values[3]->timestamp->format('Y-m-d H:i:s'));
        $values = $abbreviationsMeasurements['B_E_EXP'];
        $this->assertEquals(4, count($values));
        $this->assertEquals(1347.772, $values[0]->value);
        $this->assertEquals('2016-10-10 11:00:00', $values[0]->timestamp->format('Y-m-d H:i:s'));
        $this->assertEquals(1347.792, $values[1]->value);
        $this->assertEquals('2016-10-10 11:05:00', $values[1]->timestamp->format('Y-m-d H:i:s'));
        $this->assertEquals(1347.812, $values[2]->value);
        $this->assertEquals('2016-10-10 11:10:00', $values[2]->timestamp->format('Y-m-d H:i:s'));
        $this->assertEquals(1347.832, $values[3]->value);
        $this->assertEquals('2016-10-10 11:15:00', $values[3]->timestamp->format('Y-m-d H:i:s'));
    }

    public function testGetBatteriesBulkData() {
        $json = file_get_contents(__DIR__ . '/responses/getBatteryBulk.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/batteries/bulk/measurements'),
                $this->identicalTo('from=2016-10-10T11%3A00%3A00%2B02%3A00&to=2016-10-10T11%3A15%3A00%2B02%3A00')
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-10-10T11:00:00+02:00'))
            ->withDateTo(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-10-10T11:15:00+02:00'));

        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->batteries()->bulk()->measurements()->get($criteria);

        $this->assertEquals($json, $bulkReader->getAsString());
        $this->assertEquals(json_decode($json, true), $bulkReader->getAsArray());
    }

    public function testGetBatteriesBulkDataWithCsvFormat() {
        $cvsRawData = file_get_contents(__DIR__ . '/responses/bulkCsv/getBatteryBulk.csv');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/batteries/bulk/measurements'),
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
        $bulkReader = $this->api->system('ABCDE')->batteries()->bulk()->measurements()->get($criteria);

        $this->assertEquals($cvsRawData, $bulkReader->getAsString());
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Delimiter and decimal point symbols can't be the same
     */
    public function testGetBatteriesBulkDataWithCsvFormatWithWrongParameter() {
        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-09-01T10:00:00+02:00'))
            ->withDateTo(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-09-01T10:15:00+02:00'))
            ->withFormat(CsvFormat::FORMAT_CSV)
            ->withDelimiter(CsvFormat::DELIMITER_COLON)
            ->withDecimalPoint(CsvFormat::DECIMAL_POINT_COLON);
        $this->api->system('ABCDE')->batteries()->bulk()->measurements()->get($criteria);
    }
}
