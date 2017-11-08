<?php

namespace meteocontrol\client\vcomapi\tests\unit\systems;

use GuzzleHttp\Client;
use meteocontrol\client\vcomapi\ApiClient;
use meteocontrol\client\vcomapi\Config;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\client\vcomapi\handlers\BasicAuthorizationHandler;
use meteocontrol\client\vcomapi\model\DevicesMeasurement;
use meteocontrol\client\vcomapi\model\Measurement;
use meteocontrol\client\vcomapi\model\Meter;
use meteocontrol\client\vcomapi\model\MeterDetail;
use meteocontrol\client\vcomapi\readers\CsvFormat;
use meteocontrol\client\vcomapi\readers\MeasurementsBulkReader;

class MetersTest extends \PHPUnit_Framework_TestCase {

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

    public function testGetMeters() {
        $json = file_get_contents(__DIR__ . '/responses/getMeters.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/meters'))
            ->willReturn($json);

        /** @var Meter[] $meters */
        $meters = $this->api->system('ABCDE')->meters()->get();

        $this->assertEquals(1, count($meters));
        $this->assertEquals('0773', $meters[0]->id);
        $this->assertEquals('Meter 1', $meters[0]->name);
    }

    public function testGetSingleMeter() {
        $json = file_get_contents(__DIR__ . '/responses/getMeter.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/meters/0773'))
            ->willReturn($json);

        /** @var MeterDetail $meter */
        $meter = $this->api->system('ABCDE')->meter('0773')->get();

        $this->assertEquals(773, $meter->id);
        $this->assertEquals('Meter 1', $meter->name);
        $this->assertEquals('12', $meter->address);
        $this->assertEquals('1.0', $meter->firmware);
    }

    public function testGetMeterAbbreviations() {
        $json = file_get_contents(__DIR__ . '/responses/getMeterAbbreviations.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/meters/0773/abbreviations'))
            ->willReturn($json);

        /** @var string[] $abbreviations */
        $abbreviations = $this->api->system('ABCDE')->meter('0773')->abbreviations()->get();

        $this->assertEquals(2, count($abbreviations));
        $this->assertEquals('E_DAY', $abbreviations[0]);
        $this->assertEquals('E_INT', $abbreviations[1]);
    }

    public function testGetMeterSingleAbbreviation() {
        $json = file_get_contents(__DIR__ . '/responses/getMeterSingleAbbreviation.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/meters/0773/abbreviations/E_INT'))
            ->willReturn($json);

        /** @var \meteocontrol\client\vcomapi\model\Abbreviation $abbreviation */
        $abbreviation = $this->api->system('ABCDE')->meter('0773')->abbreviation('E_INT')->get();

        $this->assertEquals('SUM', $abbreviation->aggregation);
        $this->assertEquals('Energy generated per interval', $abbreviation->description);
        $this->assertEquals('3', $abbreviation->precision);
        $this->assertEquals('kWh', $abbreviation->unit);
    }

    public function testGetMeterMeasurements() {
        $json = file_get_contents(__DIR__ . '/responses/getMeterMeasurements.json');
        $this->api->expects($this->exactly(2))
            ->method('run')
        ->with(
            $this->identicalTo(
                'systems/ABCDE/meters/12345,67890/abbreviations/E_INT,M_AC_F/measurements'
            ),
            $this->identicalTo(
                'from=2016-01-01T00%3A00%3A00%2B02%3A00&to=2016-01-02T23%3A59%3A59%2B02%3A00&resolution=day'
            )
        )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-01-01T00:00:00+02:00'))
            ->withDateTo(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-01-02T23:59:59+02:00'))
            ->withResolution(Measurement::RESOLUTION_DAY);

        /** @var DevicesMeasurement $measurements */
        $measurements = $this->api->system('ABCDE')->meter('12345,67890')->abbreviation(['E_INT', 'M_AC_F'])
            ->measurements()->get($criteria);
        $this->assertEquals(2, count($measurements));
        $abbreviationsMeasurements = $measurements['12345'];
        $values = $abbreviationsMeasurements['E_INT'];
        $this->assertEquals(2, count($values));
        $this->assertEquals(0.089, $values[0]->value);
        $this->assertEquals('2016-01-01 11:00:00', $values[0]->timestamp->format('Y-m-d H:i:s'));
        $this->assertEquals(0.082, $values[1]->value);
        $this->assertEquals('2016-01-01 11:15:00', $values[1]->timestamp->format('Y-m-d H:i:s'));
        $values = $abbreviationsMeasurements['M_AC_F'];
        $this->assertEquals(2, count($values));
        $this->assertEquals(50, $values[0]->value);
        $this->assertEquals('2016-01-01 11:00:00', $values[0]->timestamp->format('Y-m-d H:i:s'));
        $this->assertEquals(55, $values[1]->value);
        $this->assertEquals('2016-01-01 11:15:00', $values[1]->timestamp->format('Y-m-d H:i:s'));

        /** @var DevicesMeasurement $measurements */
        $measurements = $this->api->system('ABCDE')->meter(['12345', '67890'])->abbreviation(['E_INT', 'M_AC_F'])
            ->measurements()->get($criteria);
        $this->assertEquals(2, count($measurements));
        $abbreviationsMeasurements = $measurements['67890'];
        $values = $abbreviationsMeasurements['E_INT'];
        $this->assertEquals(2, count($values));
        $this->assertEquals(1.089, $values[0]->value);
        $this->assertEquals('2016-01-01 11:00:00', $values[0]->timestamp->format('Y-m-d H:i:s'));
        $this->assertEquals(1.082, $values[1]->value);
        $this->assertEquals('2016-01-01 11:15:00', $values[1]->timestamp->format('Y-m-d H:i:s'));
        $values = $abbreviationsMeasurements['M_AC_F'];
        $this->assertEquals(2, count($values));
        $this->assertEquals(60, $values[0]->value);
        $this->assertEquals('2016-01-01 11:00:00', $values[0]->timestamp->format('Y-m-d H:i:s'));
        $this->assertEquals(65, $values[1]->value);
        $this->assertEquals('2016-01-01 11:15:00', $values[1]->timestamp->format('Y-m-d H:i:s'));
    }

    public function testGetMetersBulkData() {
        $json = file_get_contents(__DIR__ . '/responses/getMeterBulk.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/meters/bulk/measurements'),
                $this->identicalTo('from=2016-09-01T10%3A00%3A00%2B02%3A00&to=2016-09-01T10%3A15%3A00%2B02%3A00')
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-09-01T10:00:00+02:00'))
            ->withDateTo(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-09-01T10:15:00+02:00'));

        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->meters()->bulk()->measurements()->get($criteria);

        $this->assertEquals($json, $bulkReader->getAsString());
        $this->assertEquals(json_decode($json, true), $bulkReader->getAsArray());
    }

    public function testGetMetersBulkDataWithCsvFormat() {
        $cvsRawData = file_get_contents(__DIR__ . '/responses/bulkCsv/getMeterBulk.csv');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/meters/bulk/measurements'),
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
        $bulkReader = $this->api->system('ABCDE')->meters()->bulk()->measurements()->get($criteria);

        $this->assertEquals($cvsRawData, $bulkReader->getAsString());
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Delimiter and decimal point symbols can't be the same
     */
    public function testGetMetersBulkDataWithCsvFormatWithWrongParameter() {
        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-09-01T10:00:00+02:00'))
            ->withDateTo(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-09-01T10:15:00+02:00'))
            ->withFormat(CsvFormat::FORMAT_CSV)
            ->withDelimiter(CsvFormat::DELIMITER_COLON)
            ->withDecimalPoint(CsvFormat::DECIMAL_POINT_COLON);
        $this->api->system('ABCDE')->meters()->bulk()->measurements()->get($criteria);
    }
}
