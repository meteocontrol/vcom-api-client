<?php

namespace meteocontrol\client\vcomapi\tests\unit\systems;

use GuzzleHttp\Client;
use meteocontrol\client\vcomapi\ApiClient;
use meteocontrol\client\vcomapi\Config;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\client\vcomapi\readers\CsvFormat;
use meteocontrol\client\vcomapi\readers\MeasurementsBulkReader;

class SystemsTest extends \PHPUnit_Framework_TestCase {

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

    public function testGetSystems() {
        $json = file_get_contents(__DIR__ . '/responses/getSystems.json');

        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems'))
            ->willReturn($json);

        /** @var \meteocontrol\client\vcomapi\model\System[] $systems */
        $systems = $this->api->systems()->get();

        $this->assertEquals(2, count($systems));
        $this->assertEquals('ABCDE', $systems[0]->key);
        $this->assertEquals('Meteocontrol PV system', $systems[0]->name);
        $this->assertEquals('VWXYZ', $systems[1]->key);
        $this->assertEquals('Meteocontrol PV system #2', $systems[1]->name);
    }

    public function testGetSystem() {
        $json = file_get_contents(__DIR__ . '/responses/getSystem.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE'))
            ->willReturn($json);

        /** @var \meteocontrol\client\vcomapi\model\SystemDetail $system */
        $system = $this->api->system('ABCDE')->get();

        $this->assertEquals('Augsburg', $system->address->city);
        $this->assertEquals('DE', $system->address->country);
        $this->assertEquals('86157', $system->address->postalCode);
        $this->assertEquals('Spicherer Straße 48', $system->address->street);
        $this->assertEquals(480, $system->elevation);
        $this->assertEquals('test', $system->name);
        $this->assertEquals('2016-01-28', $system->commissionDate->format('Y-m-d'));
        $this->assertEquals(48.3670191, $system->coordinates->latitude);
        $this->assertEquals(10.8681, $system->coordinates->longitude);
        $this->assertEquals('Europe/Berlin', $system->timezone->name);
        $this->assertEquals('+01:00', $system->timezone->utcOffset);
    }

    public function testGetSystemBulkData() {
        $json = file_get_contents(__DIR__ . '/responses/getSystemBulk.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/bulk/measurements'),
                $this->identicalTo('from=2016-10-31T19%3A30%3A00%2B02%3A00&to=2016-11-01T19%3A15%3A00%2B02%3A00')
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-10-31T19:30:00+02:00'))
            ->withDateTo(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-11-01T19:15:00+02:00'));

        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->bulk()->measurements()->get($criteria);

        $this->assertEquals($json, $bulkReader->getAsString());
        $this->assertEquals(json_decode($json, true), $bulkReader->getAsArray());
    }

    public function testGetSystemBulkDataWithCsvFormat() {
        $cvsRawData = file_get_contents(__DIR__ . '/responses/bulkCsv/getSuperBulk.csv');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/bulk/measurements'),
                $this->identicalTo(
                    'from=2016-10-31T19%3A30%3A00%2B02%3A00&to=2016-11-01T19%3A15%3A00%2B02%3A00&format=csv'
                )
            )
            ->willReturn($cvsRawData);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-10-31T19:30:00+02:00'))
            ->withDateTo(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-11-01T19:15:00+02:00'))
            ->withFormat(CsvFormat::FORMAT_CSV);
        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->bulk()->measurements()->get($criteria);

        $this->assertEquals($cvsRawData, $bulkReader->getAsString());
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Delimiter and decimal point symbols can't be the same
     */
    public function testGetSystemBulkDataWithCsvFormatWithWrongParameter() {
        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-09-01T10:00:00+02:00'))
            ->withDateTo(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-09-01T10:15:00+02:00'))
            ->withFormat(CsvFormat::FORMAT_CSV)
            ->withDelimiter(CsvFormat::DELIMITER_COLON)
            ->withDecimalPoint(CsvFormat::DECIMAL_POINT_COLON);
        $this->api->system('ABCDE')->bulk()->measurements()->get($criteria);
    }
}
