<?php

namespace meteocontrol\client\vcomapi\tests\unit\systems;

use DateTime;
use DateTimeZone;
use GuzzleHttp\RequestOptions;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\client\vcomapi\filters\SystemCriteria;
use meteocontrol\client\vcomapi\readers\CsvFormat;
use meteocontrol\client\vcomapi\readers\MeasurementsBulkReader;
use meteocontrol\client\vcomapi\tests\unit\TestCase;
use UnexpectedValueException;

class SystemsTest extends TestCase {

    public function testGetSystems() {
        $json = file_get_contents(__DIR__ . '/responses/getSystems.json');

        $this->api->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('systems'))
            ->willReturn($json);

        $systems = $this->api->systems()->get();

        $this->assertCount(2, $systems);
        $this->assertEquals('ABCDE', $systems[0]->key);
        $this->assertEquals('Meteocontrol PV system', $systems[0]->name);
        $this->assertEmpty($systems[0]->tags);
        $this->assertEquals('VWXYZ', $systems[1]->key);
        $this->assertEquals('Meteocontrol PV system #2', $systems[1]->name);
        $this->assertEmpty($systems[1]->tags);
    }

    public function testGetSystemsWithTags() {
        $json = file_get_contents(__DIR__ . '/responses/getSystemsWithTags.json');

        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo('systems'),
                $this->identicalToUrl([
                    RequestOptions::QUERY => 'tags=tag 1,tag 2'
                ])
            )
            ->willReturn($json);

        $criteria = new SystemCriteria();
        $criteria->withTags(['tag 1', 'tag 2']);
        $systems = $this->api->systems()->get($criteria);

        $this->assertCount(2, $systems);
        $this->assertEquals('ABCDE', $systems[0]->key);
        $this->assertEquals('Meteocontrol PV system', $systems[0]->name);
        $this->assertEquals(['tag 1'], $systems[0]->tags);
        $this->assertEquals('VWXYZ', $systems[1]->key);
        $this->assertEquals('Meteocontrol PV system #2', $systems[1]->name);
        $this->assertEquals(['tag 2'], $systems[1]->tags);
    }

    public function testGetSystem() {
        $json = file_get_contents(__DIR__ . '/responses/getSystem.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('systems/ABCDE'))
            ->willReturn($json);

        $system = $this->api->system('ABCDE')->get();

        $this->assertEquals('Augsburg', $system->address->city);
        $this->assertEquals('DE', $system->address->country);
        $this->assertEquals('86157', $system->address->postalCode);
        $this->assertEquals('Spicherer Straße 48', $system->address->street);
        $this->assertEquals(480, $system->elevation);
        $this->assertEquals('test', $system->name);
        $this->assertEquals(
            new DateTime('2016-01-28 00:00:00', new DateTimeZone('Europe/Berlin')),
            $system->commissionDate
        );
        $this->assertEquals(48.3670191, $system->coordinates->latitude);
        $this->assertEquals(10.8681, $system->coordinates->longitude);
        $this->assertEquals('Europe/Berlin', $system->timezone->name);
        $this->assertEquals('+01:00', $system->timezone->utcOffset);
        $this->assertEquals('additional information', $system->additionalInformation);
    }

    public function testGetSystemBulkData() {
        $json = file_get_contents(__DIR__ . '/responses/getSystemBulk.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo('systems/ABCDE/bulk/measurements'),
                $this->identicalToUrl([
                    RequestOptions::QUERY => 'from=2016-11-01T11:00:00+02:00&to=2016-11-01T11:05:00+02:00'
                ])
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-11-01T11:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-11-01T11:05:00+02:00'));

        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->bulk()->measurements()->get($criteria);

        $this->assertEquals($json, $bulkReader->getAsString());
        $this->assertEquals(json_decode($json, true), $bulkReader->getAsArray());
    }

    public function testGetSystemBulkDataWithAbbreviationsFilter() {
        $json = file_get_contents(__DIR__ . '/responses/getSystemBulkWithAbbreviationsFilter.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo('systems/ABCDE/bulk/measurements'),
                $this->identicalToUrl([
                    RequestOptions::QUERY => 'from=2016-11-01T11:00:00+02:00&to=2016-11-01T11:05:00+02:00&abbreviations=G_M2,AREA,E_DAY,E_INT,SRAD,D_IN1'
                ])
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-11-01T11:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-11-01T11:05:00+02:00'))
            ->withAbbreviation(['G_M2', 'AREA', 'E_DAY', 'E_INT', 'SRAD', 'D_IN1']);

        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->bulk()->measurements()->get($criteria);

        $this->assertEquals($json, $bulkReader->getAsString());
        $this->assertEquals(json_decode($json, true), $bulkReader->getAsArray());
    }

    public function testGetSystemBulkDataWithDeviceIdsAndAbbreviationsFilter() {
        $json = file_get_contents(__DIR__ . '/responses/getSystemBulkWithDeviceIdsAndAbbreviationsFilter.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo('systems/ABCDE/bulk/measurements'),
                $this->identicalToUrl([
                    RequestOptions::QUERY => 'from=2016-11-01T11:00:00+02:00&to=2016-11-01T11:05:00+02:00&deviceIds=Id73872.1,118045&abbreviations=E_DAY,E_INT'
                ])
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-11-01T11:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-11-01T11:05:00+02:00'))
            ->withDeviceIds(['Id73872.1', '118045'])
            ->withAbbreviation(['E_DAY', 'E_INT']);

        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->bulk()->measurements()->get($criteria);

        $this->assertEquals($json, $bulkReader->getAsString());
        $this->assertEquals(json_decode($json, true), $bulkReader->getAsArray());
    }

    public function testGetSystemBulkDataWithCsvFormat() {
        $cvsRawData = file_get_contents(__DIR__ . '/responses/bulkCsv/getSuperBulk.csv');
        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo('systems/ABCDE/bulk/measurements'),
                $this->identicalToUrl([
                    RequestOptions::QUERY => 'from=2016-09-01T10:15:00+02:00&to=2016-09-01T10:30:00+02:00&format=csv'
                ])
            )
            ->willReturn($cvsRawData);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-09-01T10:15:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-09-01T10:30:00+02:00'))
            ->withFormat(CsvFormat::FORMAT_CSV);
        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->bulk()->measurements()->get($criteria);

        $this->assertEquals($cvsRawData, $bulkReader->getAsString());
    }

    public function testGetSystemBulkDataWithCsvFormatWithWrongParameter() {
        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-09-01T10:15:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-09-01T10:30:00+02:00'))
            ->withFormat(CsvFormat::FORMAT_CSV)
            ->withDelimiter(CsvFormat::DELIMITER_COMMA)
            ->withDecimalPoint(CsvFormat::DECIMAL_POINT_COMMA)
            ->withPrecision(CsvFormat::PRECISION_2);

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage("Delimiter and decimal point symbols can't be the same");

        $this->api->system('ABCDE')->bulk()->measurements()->get($criteria);
    }
}
