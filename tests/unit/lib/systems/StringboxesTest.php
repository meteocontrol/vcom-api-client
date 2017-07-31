<?php

namespace meteocontrol\client\vcomapi\tests\unit\systems;

use GuzzleHttp\Client;
use meteocontrol\client\vcomapi\ApiClient;
use meteocontrol\client\vcomapi\Config;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\client\vcomapi\handlers\BasicAuthorizationHandler;
use meteocontrol\client\vcomapi\model\Stringbox;
use meteocontrol\client\vcomapi\model\StringboxDetail;
use meteocontrol\client\vcomapi\readers\CsvFormat;
use meteocontrol\client\vcomapi\readers\MeasurementsBulkReader;

class StringboxesTest extends \PHPUnit_Framework_TestCase {

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

    public function testGetStringboxDevices() {
        $json = file_get_contents(__DIR__ . '/responses/getStringboxes.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/stringboxes')
            )
            ->willReturn($json);
        $expectedDevices = $this->getExpectedStringBoxDevices();
        $actualDevices = $this->api->system('ABCDE')->stringboxes()->get();
        $this->assertEquals($expectedDevices, $actualDevices);
    }

    public function testGetSingleStringboxDevice() {
        $json = file_get_contents(__DIR__ . '/responses/getStringbox.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/stringboxes/919859')
            )
            ->willReturn($json);
        $expectedDevice = $this->getExpectedStringBoxDevice();
        $actualDevice = $this->api->system('ABCDE')->stringbox("919859")->get();
        $this->assertEquals($expectedDevice, $actualDevice);
    }

    public function testGetStringboxesBulkData() {
        $json = file_get_contents(__DIR__ . '/responses/getStringboxBulk.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/stringboxes/bulk/measurements'),
                $this->identicalTo('from=2016-09-01T10%3A00%3A00%2B02%3A00&to=2016-09-01T10%3A15%3A00%2B02%3A00')
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-09-01T10:00:00+02:00'))
            ->withDateTo(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-09-01T10:15:00+02:00'));

        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->stringboxes()->bulk()->measurements()->get($criteria);

        $this->assertEquals($json, $bulkReader->getAsString());
        $this->assertEquals(json_decode($json, true), $bulkReader->getAsArray());
    }

    public function testGetStringboxesBulkDataWithCsvFormat() {
        $cvsRawData = file_get_contents(__DIR__ . '/responses/bulkCsv/getStringboxesBulk.csv');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/stringboxes/bulk/measurements'),
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
        $bulkReader = $this->api->system('ABCDE')->stringboxes()->bulk()->measurements()->get($criteria);

        $this->assertEquals($cvsRawData, $bulkReader->getAsString());
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Delimiter and decimal point symbols can't be the same
     */
    public function testGetStringboxesBulkDataWithCsvFormatWithWrongParameter() {
        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-09-01T10:00:00+02:00'))
            ->withDateTo(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-09-01T10:15:00+02:00'))
            ->withFormat(CsvFormat::FORMAT_CSV)
            ->withDelimiter(CsvFormat::DELIMITER_COLON)
            ->withDecimalPoint(CsvFormat::DECIMAL_POINT_COLON);
        $this->api->system('ABCDE')->stringboxes()->bulk()->measurements()->get($criteria);
    }

    /**
     * @return Stringbox[]
     */
    private function getExpectedStringBoxDevices() {
        $device1 = new Stringbox();
        $device1->id = "919859";
        $device1->name = "GAK 11 - 1";
        $device1->serial = "11G1";
        $device2 = new Stringbox();
        $device2->id = "919860";
        $device2->name = "GAK 11 - 2";
        $device2->serial = "11G2";
        return [$device1, $device2];
    }

    /**
     * @return StringboxDetail
     */
    private function getExpectedStringBoxDevice() {
        $device = new StringboxDetail();
        $device->id = "919859";
        $device->name = "GAK 11 - 1";
        $device->serial = "11G1";
        return $device;
    }
}
