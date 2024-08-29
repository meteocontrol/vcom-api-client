<?php

namespace meteocontrol\client\vcomapi\tests\unit\systems;

use DateTime;
use GuzzleHttp\RequestOptions;
use InvalidArgumentException;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\client\vcomapi\model\Abbreviation;
use meteocontrol\client\vcomapi\model\DevicesMeasurement;
use meteocontrol\client\vcomapi\model\DevicesMeasurementWithInterval;
use meteocontrol\client\vcomapi\model\Meter;
use meteocontrol\client\vcomapi\model\MeterDetail;
use meteocontrol\client\vcomapi\readers\CsvFormat;
use meteocontrol\client\vcomapi\readers\MeasurementsBulkReader;
use meteocontrol\client\vcomapi\tests\unit\TestCase;
use UnexpectedValueException;

class MetersTest extends TestCase {

    public function testGetMeters() {
        $json = file_get_contents(__DIR__ . '/responses/getMeters.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('systems/ABCDE/meters'))
            ->willReturn($json);

        /** @var Meter[] $meters */
        $meters = $this->api->system('ABCDE')->meters()->get();

        $this->assertCount(1, $meters);
        $this->assertEquals('0773', $meters[0]->id);
        $this->assertEquals('Meter 1', $meters[0]->name);
    }

    public function testGetSingleMeter() {
        $json = file_get_contents(__DIR__ . '/responses/getMeter.json');
        $this->api->expects($this->once())
            ->method('get')
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
            ->method('get')
            ->with($this->identicalTo('systems/ABCDE/meters/0773/abbreviations'))
            ->willReturn($json);

        /** @var string[] $abbreviations */
        $abbreviations = $this->api->system('ABCDE')->meter('0773')->abbreviations()->get();

        $this->assertCount(2, $abbreviations);
        $this->assertEquals('E_DAY', $abbreviations[0]);
        $this->assertEquals('E_INT', $abbreviations[1]);
    }

    public function testGetMeterSingleAbbreviation() {
        $json = file_get_contents(__DIR__ . '/responses/getMeterSingleAbbreviation.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('systems/ABCDE/meters/0773/abbreviations/E_INT'))
            ->willReturn($json);

        /** @var Abbreviation $abbreviation */
        $abbreviation = $this->api->system('ABCDE')->meter('0773')->abbreviation('E_INT')->get();

        $this->assertEquals('SUM', $abbreviation->aggregation);
        $this->assertEquals('Energy generated per interval', $abbreviation->description);
        $this->assertEquals(3, $abbreviation->precision);
        $this->assertEquals('kWh', $abbreviation->unit);
    }

    public function testGetMeterMeasurements() {
        $json = file_get_contents(__DIR__ . '/responses/getMeterMeasurements.json');
        $this->api->expects($this->exactly(2))
            ->method('get')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/meters/12345,67890/abbreviations/E_INT,M_AC_F/measurements'
                ),
                $this->identicalToUrl([
                    RequestOptions::QUERY => 'from=2016-01-01T00:00:00+02:00&to=2016-01-02T23:59:59+02:00&resolution=day'
                ])
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-01-01T00:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-01-02T23:59:59+02:00'))
            ->withResolution(MeasurementsCriteria::RESOLUTION_DAY);

        /** @var DevicesMeasurement $measurements */
        $measurements = $this->api->system('ABCDE')->meter('12345,67890')->abbreviation(['E_INT', 'M_AC_F'])
            ->measurements()->get($criteria);
        $this->assertCount(2, $measurements);
        $abbreviationsMeasurements = $measurements['12345'];
        $values = $abbreviationsMeasurements['E_INT'];
        $this->assertCount(2, $values);
        $this->assertEquals(0.089, $values[0]->value);
        $this->assertEquals('2016-01-01T11:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(0.082, $values[1]->value);
        $this->assertEquals('2016-01-01T11:15:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $values = $abbreviationsMeasurements['M_AC_F'];
        $this->assertCount(2, $values);
        $this->assertEquals(50, $values[0]->value);
        $this->assertEquals('2016-01-01T11:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(55, $values[1]->value);
        $this->assertEquals('2016-01-01T11:15:00+02:00', $values[1]->timestamp->format(DATE_ATOM));

        /** @var DevicesMeasurement $measurements */
        $measurements = $this->api->system('ABCDE')->meter(['12345', '67890'])->abbreviation(['E_INT', 'M_AC_F'])
            ->measurements()->get($criteria);
        $this->assertCount(2, $measurements);
        $abbreviationsMeasurements = $measurements['67890'];
        $values = $abbreviationsMeasurements['E_INT'];
        $this->assertCount(2, $values);
        $this->assertEquals(1.089, $values[0]->value);
        $this->assertEquals('2016-01-01T11:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(1.082, $values[1]->value);
        $this->assertEquals('2016-01-01T11:15:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $values = $abbreviationsMeasurements['M_AC_F'];
        $this->assertCount(2, $values);
        $this->assertEquals(60, $values[0]->value);
        $this->assertEquals('2016-01-01T11:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(65, $values[1]->value);
        $this->assertEquals('2016-01-01T11:15:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
    }

    public function testGetMeterMeasurementsWithIntervalIncluded() {
        $json = file_get_contents(__DIR__ . '/responses/getMeterMeasurementsIncludeInterval.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/meters/12345,67890/abbreviations/E_INT,M_AC_F/measurements'
                ),
                $this->identicalToUrl([
                    RequestOptions::QUERY => 'from=2016-01-01T00:00:00+02:00&to=2016-01-01T23:59:59+02:00&resolution=interval&includeInterval=1'
                ])
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-01-01T00:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-01-01T23:59:59+02:00'))
            ->withResolution(MeasurementsCriteria::RESOLUTION_INTERVAL)
            ->withIntervalIncluded();

        /** @var DevicesMeasurementWithInterval $measurements */
        $measurements = $this->api->system('ABCDE')->meter('12345,67890')->abbreviation(['E_INT', 'M_AC_F'])
            ->measurements()->get($criteria);
        $this->assertCount(2, $measurements);
        $abbreviationsMeasurements = $measurements['12345'];
        $values = $abbreviationsMeasurements['E_INT'];
        $this->assertCount(2, $values);
        $this->assertEquals(0.089, $values[0]->value);
        $this->assertEquals('2016-01-01T11:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[0]->interval);
        $this->assertEquals(0.082, $values[1]->value);
        $this->assertEquals('2016-01-01T11:15:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[1]->interval);

        $values = $abbreviationsMeasurements['M_AC_F'];
        $this->assertCount(2, $values);
        $this->assertEquals(50, $values[0]->value);
        $this->assertEquals(300, $values[0]->interval);
        $this->assertEquals('2016-01-01T11:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(55, $values[1]->value);
        $this->assertEquals('2016-01-01T11:15:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[1]->interval);
    }

    public function testGetMeterMeasurementsWithIntervalIncludedWithWrongResolution() {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"includeInterval" is only accepted with interval resolution');

        $json = file_get_contents(__DIR__ . '/responses/getMeterMeasurements.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/meters/12345,67890/abbreviations/E_INT,M_AC_F/measurements'
                ),
                $this->identicalToUrl([
                    RequestOptions::QUERY => 'from=2016-01-01T00:00:00+02:00&to=2016-01-01T23:59:59+02:00&resolution=day&includeInterval=1'
                ])
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-01-01T00:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-01-01T23:59:59+02:00'))
            ->withResolution(MeasurementsCriteria::RESOLUTION_DAY)
            ->withIntervalIncluded();

        /** @var DevicesMeasurement $measurements */
        $this->api->system('ABCDE')->meter('12345,67890')->abbreviation(['E_INT', 'M_AC_F'])
            ->measurements()->get($criteria);
    }

    public function testGetMeterMeasurementsWithIntervalIncludedWithResolution() {
        $json = file_get_contents(__DIR__ . '/responses/getMeterMeasurements.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/meters/12345,67890/abbreviations/E_INT,M_AC_F/measurements'
                ),
                $this->identicalToUrl([
                    RequestOptions::QUERY => 'from=2016-01-01T00:00:00+02:00&to=2016-01-01T23:59:59+02:00&resolution=interval&includeInterval=1'
                ])
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-01-01T00:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-01-01T23:59:59+02:00'))
            ->withResolution(MeasurementsCriteria::RESOLUTION_INTERVAL)
            ->withIntervalIncluded();

        /** @var DevicesMeasurementWithInterval $measurements */
        @$measurements = $this->api->system('ABCDE')->meter('12345,67890')->abbreviation(['E_INT', 'M_AC_F'])
            ->measurements()->get($criteria);
        $this->assertCount(2, $measurements);
        $abbreviationsMeasurements = $measurements['12345'];
        $values = $abbreviationsMeasurements['E_INT'];
        $this->assertCount(2, $values);
        $this->assertEquals(0.089, $values[0]->value);
        $this->assertEquals('2016-01-01T11:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $values[0]->interval);
        $this->assertEquals(0.082, $values[1]->value);
        $this->assertEquals('2016-01-01T11:15:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $values[1]->interval);

        $values = $abbreviationsMeasurements['M_AC_F'];
        $this->assertCount(2, $values);
        $this->assertEquals(50, $values[0]->value);
        $this->assertEquals(null, $values[0]->interval);
        $this->assertEquals('2016-01-01T11:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(55, $values[1]->value);
        $this->assertEquals('2016-01-01T11:15:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $values[1]->interval);
    }

    public function testGetMetersBulkData() {
        $json = file_get_contents(__DIR__ . '/responses/getMeterBulk.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo('systems/ABCDE/meters/bulk/measurements'),
                $this->identicalToUrl([
                    RequestOptions::QUERY => 'from=2016-09-01T10:00:00+02:00&to=2016-09-01T10:15:00+02:00'
                ])
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-09-01T10:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-09-01T10:15:00+02:00'));

        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->meters()->bulk()->measurements()->get($criteria);

        $this->assertEquals($json, $bulkReader->getAsString());
        $this->assertEquals(json_decode($json, true), $bulkReader->getAsArray());
    }

    public function testGetMetersBulkDataWithAbbreviationsFilter() {
        $json = file_get_contents(__DIR__ . '/responses/getMeterBulkWithAbbreviationsFilter.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo('systems/ABCDE/meters/bulk/measurements'),
                $this->identicalToUrl([
                    RequestOptions::QUERY => 'from=2016-09-01T10:00:00+02:00&to=2016-09-01T10:15:00+02:00&abbreviations=E_INT'
                ])
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-09-01T10:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-09-01T10:15:00+02:00'))
            ->withAbbreviation(['E_INT']);

        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->meters()->bulk()->measurements()->get($criteria);

        $this->assertEquals($json, $bulkReader->getAsString());
        $this->assertEquals(json_decode($json, true), $bulkReader->getAsArray());
    }

    public function testGetMetersBulkDataWithCsvFormat() {
        $cvsRawData = file_get_contents(__DIR__ . '/responses/bulkCsv/getMeterBulk.csv');
        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo('systems/ABCDE/meters/bulk/measurements'),
                $this->identicalToUrl([
                    RequestOptions::QUERY => 'from=2016-09-01T10:00:00+02:00&to=2016-09-01T10:15:00+02:00&format=csv'
                ])
            )
            ->willReturn($cvsRawData);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-09-01T10:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-09-01T10:15:00+02:00'))
            ->withFormat(CsvFormat::FORMAT_CSV);
        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->meters()->bulk()->measurements()->get($criteria);

        $this->assertEquals($cvsRawData, $bulkReader->getAsString());
    }

    public function testGetMetersBulkDataWithCsvFormatWithWrongParameter() {
        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-09-01T10:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-09-01T10:15:00+02:00'))
            ->withFormat(CsvFormat::FORMAT_CSV)
            ->withDelimiter(CsvFormat::DELIMITER_COMMA)
            ->withDecimalPoint(CsvFormat::DECIMAL_POINT_COMMA)
            ->withPrecision(CsvFormat::PRECISION_2);

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage("Delimiter and decimal point symbols can't be the same");

        $this->api->system('ABCDE')->meters()->bulk()->measurements()->get($criteria);
    }
}
