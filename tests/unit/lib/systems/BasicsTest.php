<?php

namespace meteocontrol\client\vcomapi\tests\unit\systems;

use GuzzleHttp\Client;
use meteocontrol\client\vcomapi\ApiClient;
use meteocontrol\client\vcomapi\Config;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\client\vcomapi\handlers\BasicAuthorizationHandler;
use meteocontrol\client\vcomapi\model\Measurement;
use meteocontrol\client\vcomapi\model\MeasurementValue;
use meteocontrol\client\vcomapi\readers\CsvFormat;
use meteocontrol\client\vcomapi\readers\MeasurementsBulkReader;

class BasicsTest extends \PHPUnit_Framework_TestCase {

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

    public function testGetBasicsAbbreviations() {
        $json = file_get_contents(__DIR__ . '/responses/getBasicsAbbreviations.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/basics/abbreviations'))
            ->willReturn($json);

        /** @var string[] $abbreviations */
        $abbreviations = $this->api->system('ABCDE')->basics()->abbreviations()->get();

        $this->assertEquals(3, count($abbreviations));
        $this->assertEquals('E_Z_PV1', $abbreviations[0]);
        $this->assertEquals('E_Z_EVU', $abbreviations[1]);
        $this->assertEquals('G_M0', $abbreviations[2]);
    }

    public function testGetBasicsSingleAbbreviation() {
        $json = file_get_contents(__DIR__ . '/responses/getBasicsSingleAbbreviation.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/basics/abbreviations/E_Z_EVU'))
            ->willReturn($json);

        /** @var \meteocontrol\client\vcomapi\model\Abbreviation $abbreviation */
        $abbreviation = $this->api->system('ABCDE')->basics()->abbreviation('E_Z_EVU')->get();

        $this->assertEquals('SUM', $abbreviation->aggregation);
        $this->assertEquals('Energie aus I-checker', $abbreviation->description);
        $this->assertEquals(null, $abbreviation->precision);
        $this->assertEquals('kWh', $abbreviation->unit);
    }

    public function testGetBasicsMeasurements() {
        $json = file_get_contents(__DIR__ . '/responses/getBasicsMeasurements.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/basics/abbreviations/wr.E_INT/measurements'
                ),
                $this->identicalTo(
                    'from=2016-01-01T00%3A00%3A00%2B02%3A00&to=2016-01-01T00%3A15%3A00%2B02%3A00&resolution=interval'
                )
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-01-01T00:00:00+02:00'))
            ->withDateTo(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-01-01T00:15:00+02:00'))
            ->withResolution(Measurement::RESOLUTION_INTERVAL);
        /** @var MeasurementValue[] $measurements */
        $measurements = $this->api->system('ABCDE')->basics()->abbreviation('wr.E_INT')->measurements()->get($criteria);
        $this->assertEquals(2, count($measurements));
        $this->assertEquals(0, $measurements[0]->value);
        $this->assertEquals('2016-01-01 00:00:00', $measurements[0]->timestamp->format('Y-m-d H:i:s'));
        $this->assertEquals(0, $measurements[1]->value);
        $this->assertEquals('2016-01-01 00:15:00', $measurements[1]->timestamp->format('Y-m-d H:i:s'));
    }

    public function testGetBasicsBulkData() {
        $json = file_get_contents(__DIR__ . '/responses/getBasicsBulk.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/basics/bulk/measurements'),
                $this->identicalTo('from=2016-11-01T10%3A00%3A00%2B02%3A00&to=2016-11-01T10%3A15%3A00%2B02%3A00')
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-11-01T10:00:00+02:00'))
            ->withDateTo(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-11-01T10:15:00+02:00'));

        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->basics()->bulk()->measurements()->get($criteria);

        $this->assertEquals($json, $bulkReader->getAsString());
        $this->assertEquals(json_decode($json, true), $bulkReader->getAsArray());
    }

    public function testGetBasicsBulkDataWithCsvFormat() {
        $cvsRawData = file_get_contents(__DIR__ . '/responses/bulkCsv/getBasicBulk.csv');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/basics/bulk/measurements'),
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
        $bulkReader = $this->api->system('ABCDE')->basics()->bulk()->measurements()->get($criteria);

        $this->assertEquals($cvsRawData, $bulkReader->getAsString());
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Delimiter and decimal point symbols can't be the same
     */
    public function testGetBasicsBulkDataWithCsvFormatWithWrongParameter() {
        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-09-01T10:00:00+02:00'))
            ->withDateTo(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-09-01T10:15:00+02:00'))
            ->withFormat(CsvFormat::FORMAT_CSV)
            ->withDelimiter(CsvFormat::DELIMITER_COLON)
            ->withDecimalPoint(CsvFormat::DECIMAL_POINT_COLON);
        $this->api->system('ABCDE')->basics()->bulk()->measurements()->get($criteria);
    }
}
