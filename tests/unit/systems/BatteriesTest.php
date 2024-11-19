<?php

namespace meteocontrol\client\vcomapi\tests\unit\systems;

use DateTime;
use GuzzleHttp\RequestOptions;
use InvalidArgumentException;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\client\vcomapi\model\Abbreviation;
use meteocontrol\client\vcomapi\model\Battery;
use meteocontrol\client\vcomapi\model\BatteryDetail;
use meteocontrol\client\vcomapi\model\DevicesMeasurement;
use meteocontrol\client\vcomapi\model\DevicesMeasurementWithInterval;
use meteocontrol\client\vcomapi\readers\CsvFormat;
use meteocontrol\client\vcomapi\readers\MeasurementsBulkReader;
use meteocontrol\client\vcomapi\tests\unit\TestCase;
use UnexpectedValueException;

class BatteriesTest extends TestCase {

    public function testGetBatteries() {
        $json = file_get_contents(__DIR__ . '/responses/getBatteries.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('systems/ABCDE/batteries'))
            ->willReturn($json);

        /** @var Battery[] $batteries */
        $batteries = $this->api->system('ABCDE')->batteries()->get();

        $this->assertCount(2, $batteries);
        $this->assertEquals('145103', $batteries[0]->id);
        $this->assertEquals('', $batteries[0]->name);
        $this->assertEquals('123456789', $batteries[0]->uid);
        $this->assertEquals('145104', $batteries[1]->id);
        $this->assertEquals('', $batteries[1]->name);
        $this->assertEquals('12345678', $batteries[1]->uid);
    }

    public function testGetSingleBattery() {
        $json = file_get_contents(__DIR__ . '/responses/getBattery.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('systems/ABCDE/batteries/145103'))
            ->willReturn($json);

        /** @var BatteryDetail $battery */
        $battery = $this->api->system('ABCDE')->battery('145103')->get();

        $this->assertEquals('145103', $battery->id);
        $this->assertEquals('', $battery->name);
        $this->assertEquals('123456789', $battery->uid);
        $this->assertEquals('bat1', $battery->address);
        $this->assertEquals('1.0', $battery->firmware);
    }

    public function testGetBatteryAbbreviations() {
        $json = file_get_contents(__DIR__ . '/responses/getBatteryAbbreviations.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('systems/ABCDE/batteries/145103/abbreviations'))
            ->willReturn($json);

        /** @var string[] $abbreviations */
        $abbreviations = $this->api->system('ABCDE')->battery('145103')->abbreviations()->get();

        $this->assertCount(4, $abbreviations);
        $this->assertEquals('B_CHARGE_LEVEL', $abbreviations[0]);
        $this->assertEquals('B_E_EXP', $abbreviations[1]);
        $this->assertEquals('B_E_IMP', $abbreviations[2]);
        $this->assertEquals('T1', $abbreviations[3]);
    }

    public function testGetBatterySingleAbbreviation() {
        $json = file_get_contents(__DIR__ . '/responses/getBatterySingleAbbreviation.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('systems/ABCDE/batteries/145103/abbreviations/B_CHARGE_LEVEL'))
            ->willReturn($json);

        /** @var Abbreviation $abbreviation */
        $abbreviation = $this->api->system('ABCDE')->battery('145103')->abbreviation('B_CHARGE_LEVEL')->get();

        $this->assertEquals('AVG', $abbreviation->aggregation);
        $this->assertEquals('Charging status', $abbreviation->description);
        $this->assertEquals(2, $abbreviation->precision);
        $this->assertEquals('%', $abbreviation->unit);
    }

    public function testGetBatteryMeasurements() {
        $json = file_get_contents(__DIR__ . '/responses/getBatteryMeasurements.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/batteries/145103,145104/abbreviations/B_CHARGE_LEVEL,B_E_EXP/measurements'
                ),
                $this->identicalToUrl([
                    RequestOptions::QUERY => 'from=2016-10-10T11:00:00+02:00&to=2016-10-10T11:15:00+02:00'
                ])
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-10-10T11:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-10-10T11:15:00+02:00'));

        /** @var DevicesMeasurement $measurements */
        $measurements = $this->api->system('ABCDE')->battery('145103,145104')
            ->abbreviation(['B_CHARGE_LEVEL', 'B_E_EXP'])
            ->measurements()->get($criteria);
        $this->assertCount(2, $measurements);
        $abbreviationsMeasurements = $measurements['145103'];
        $values = $abbreviationsMeasurements['B_CHARGE_LEVEL'];
        $this->assertCount(4, $values);
        $this->assertEquals(80.762, $values[0]->value);
        $this->assertEquals('2016-10-10T11:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(80.782, $values[1]->value);
        $this->assertEquals('2016-10-10T11:05:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(80.802, $values[2]->value);
        $this->assertEquals('2016-10-10T11:10:00+02:00', $values[2]->timestamp->format(DATE_ATOM));
        $this->assertEquals(80.822, $values[3]->value);
        $this->assertEquals('2016-10-10T11:15:00+02:00', $values[3]->timestamp->format(DATE_ATOM));
        $values = $abbreviationsMeasurements['B_E_EXP'];
        $this->assertCount(4, $values);
        $this->assertEquals(1347.762, $values[0]->value);
        $this->assertEquals('2016-10-10T11:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(1347.782, $values[1]->value);
        $this->assertEquals('2016-10-10T11:05:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(1347.802, $values[2]->value);
        $this->assertEquals('2016-10-10T11:10:00+02:00', $values[2]->timestamp->format(DATE_ATOM));
        $this->assertEquals(1347.822, $values[3]->value);
        $this->assertEquals('2016-10-10T11:15:00+02:00', $values[3]->timestamp->format(DATE_ATOM));

        $abbreviationsMeasurements = $measurements['145104'];
        $values = $abbreviationsMeasurements['B_CHARGE_LEVEL'];
        $this->assertCount(4, $values);
        $this->assertEquals(80.772, $values[0]->value);
        $this->assertEquals('2016-10-10T11:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(80.792, $values[1]->value);
        $this->assertEquals('2016-10-10T11:05:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(80.812, $values[2]->value);
        $this->assertEquals('2016-10-10T11:10:00+02:00', $values[2]->timestamp->format(DATE_ATOM));
        $this->assertEquals(80.832, $values[3]->value);
        $this->assertEquals('2016-10-10T11:15:00+02:00', $values[3]->timestamp->format(DATE_ATOM));
        $values = $abbreviationsMeasurements['B_E_EXP'];
        $this->assertCount(4, $values);
        $this->assertEquals(1347.772, $values[0]->value);
        $this->assertEquals('2016-10-10T11:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(1347.792, $values[1]->value);
        $this->assertEquals('2016-10-10T11:05:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(1347.812, $values[2]->value);
        $this->assertEquals('2016-10-10T11:10:00+02:00', $values[2]->timestamp->format(DATE_ATOM));
        $this->assertEquals(1347.832, $values[3]->value);
        $this->assertEquals('2016-10-10T11:15:00+02:00', $values[3]->timestamp->format(DATE_ATOM));
    }

    public function testGetBatteryMeasurementsWithIntervalIncluded() {
        $json = file_get_contents(__DIR__ . '/responses/getBatteryMeasurementsIncludeInterval.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/batteries/145103,145104/abbreviations/B_CHARGE_LEVEL,B_E_EXP/measurements'
                ),
                $this->identicalToUrl([
                    RequestOptions::QUERY => 'from=2016-10-10T11:00:00+02:00&to=2016-10-10T11:15:00+02:00&includeInterval=1'
                ])
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-10-10T11:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-10-10T11:15:00+02:00'))
            ->withIntervalIncluded();

        /** @var DevicesMeasurementWithInterval $measurements */
        $measurements = $this->api->system('ABCDE')->battery('145103,145104')
            ->abbreviation(['B_CHARGE_LEVEL', 'B_E_EXP'])
            ->measurements()->get($criteria);
        $this->assertCount(2, $measurements);
        $abbreviationsMeasurements = $measurements['145103'];
        $values = $abbreviationsMeasurements['B_CHARGE_LEVEL'];
        $this->assertCount(4, $values);
        $this->assertEquals(80.762, $values[0]->value);
        $this->assertEquals('2016-10-10T11:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[0]->interval);
        $this->assertEquals(80.782, $values[1]->value);
        $this->assertEquals('2016-10-10T11:05:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[1]->interval);
        $this->assertEquals(80.802, $values[2]->value);
        $this->assertEquals('2016-10-10T11:10:00+02:00', $values[2]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[2]->interval);
        $this->assertEquals(80.822, $values[3]->value);
        $this->assertEquals('2016-10-10T11:15:00+02:00', $values[3]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[3]->interval);

        $values = $abbreviationsMeasurements['B_E_EXP'];
        $this->assertCount(4, $values);
        $this->assertEquals(1347.762, $values[0]->value);
        $this->assertEquals('2016-10-10T11:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[0]->interval);
        $this->assertEquals(1347.782, $values[1]->value);
        $this->assertEquals('2016-10-10T11:05:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[1]->interval);
        $this->assertEquals(1347.802, $values[2]->value);
        $this->assertEquals('2016-10-10T11:10:00+02:00', $values[2]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[2]->interval);
        $this->assertEquals(1347.822, $values[3]->value);
        $this->assertEquals('2016-10-10T11:15:00+02:00', $values[3]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[3]->interval);


        $abbreviationsMeasurements = $measurements['145104'];
        $values = $abbreviationsMeasurements['B_CHARGE_LEVEL'];
        $this->assertCount(4, $values);
        $this->assertEquals(80.772, $values[0]->value);
        $this->assertEquals('2016-10-10T11:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[0]->interval);
        $this->assertEquals(80.792, $values[1]->value);
        $this->assertEquals('2016-10-10T11:05:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[1]->interval);
        $this->assertEquals(80.812, $values[2]->value);
        $this->assertEquals('2016-10-10T11:10:00+02:00', $values[2]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[2]->interval);
        $this->assertEquals(80.832, $values[3]->value);
        $this->assertEquals('2016-10-10T11:15:00+02:00', $values[3]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[3]->interval);

        $values = $abbreviationsMeasurements['B_E_EXP'];
        $this->assertCount(4, $values);
        $this->assertEquals(1347.772, $values[0]->value);
        $this->assertEquals('2016-10-10T11:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[0]->interval);
        $this->assertEquals(1347.792, $values[1]->value);
        $this->assertEquals('2016-10-10T11:05:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[1]->interval);
        $this->assertEquals(1347.812, $values[2]->value);
        $this->assertEquals('2016-10-10T11:10:00+02:00', $values[2]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[2]->interval);
        $this->assertEquals(1347.832, $values[3]->value);
        $this->assertEquals('2016-10-10T11:15:00+02:00', $values[3]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[3]->interval);
    }

    public function testGetBatteryMeasurementsWithIntervalIncludedWithWrongResolution() {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"includeInterval" is only accepted with interval resolution');

        $json = file_get_contents(__DIR__ . '/responses/getBatteryMeasurements.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/batteries/145103,145104/abbreviations/B_CHARGE_LEVEL,B_E_EXP/measurements'
                ),
                $this->identicalToUrl([
                    RequestOptions::QUERY => 'from=2016-10-10T11:00:00+02:00&to=2016-10-10T11:15:00+02:00&resolution=day&includeInterval=1'
                ])
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-10-10T11:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-10-10T11:15:00+02:00'))
            ->withResolution(MeasurementsCriteria::RESOLUTION_DAY)
            ->withIntervalIncluded();

        $this->api->system('ABCDE')->battery('145103,145104')
            ->abbreviation(['B_CHARGE_LEVEL', 'B_E_EXP'])
            ->measurements()->get($criteria);
    }

    public function testGetBatteryMeasurementsWithIntervalIncludedWithResolution() {
        $json = file_get_contents(__DIR__ . '/responses/getBatteryMeasurements.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/batteries/145103,145104/abbreviations/B_CHARGE_LEVEL,B_E_EXP/measurements'
                ),
                $this->identicalToUrl([
                    RequestOptions::QUERY => 'from=2016-10-10T11:00:00+02:00&to=2016-10-10T11:15:00+02:00&resolution=interval&includeInterval=1'
                ])
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-10-10T11:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-10-10T11:15:00+02:00'))
            ->withResolution(MeasurementsCriteria::RESOLUTION_INTERVAL)
            ->withIntervalIncluded();

        /** @var DevicesMeasurementWithInterval $measurements */
        @$measurements = $this->api->system('ABCDE')->battery('145103,145104')
            ->abbreviation(['B_CHARGE_LEVEL', 'B_E_EXP'])
            ->measurements()->get($criteria);
        $this->assertCount(2, $measurements);
        $abbreviationsMeasurements = $measurements['145103'];
        $values = $abbreviationsMeasurements['B_CHARGE_LEVEL'];
        $this->assertCount(4, $values);
        $this->assertEquals(80.762, $values[0]->value);
        $this->assertEquals('2016-10-10T11:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $values[0]->interval);
        $this->assertEquals(80.782, $values[1]->value);
        $this->assertEquals('2016-10-10T11:05:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $values[1]->interval);
        $this->assertEquals(80.802, $values[2]->value);
        $this->assertEquals('2016-10-10T11:10:00+02:00', $values[2]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $values[2]->interval);
        $this->assertEquals(80.822, $values[3]->value);
        $this->assertEquals('2016-10-10T11:15:00+02:00', $values[3]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $values[3]->interval);

        $values = $abbreviationsMeasurements['B_E_EXP'];
        $this->assertCount(4, $values);
        $this->assertEquals(1347.762, $values[0]->value);
        $this->assertEquals('2016-10-10T11:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $values[0]->interval);
        $this->assertEquals(1347.782, $values[1]->value);
        $this->assertEquals('2016-10-10T11:05:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $values[1]->interval);
        $this->assertEquals(1347.802, $values[2]->value);
        $this->assertEquals('2016-10-10T11:10:00+02:00', $values[2]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $values[2]->interval);
        $this->assertEquals(1347.822, $values[3]->value);
        $this->assertEquals('2016-10-10T11:15:00+02:00', $values[3]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $values[3]->interval);


        $abbreviationsMeasurements = $measurements['145104'];
        $values = $abbreviationsMeasurements['B_CHARGE_LEVEL'];
        $this->assertCount(4, $values);
        $this->assertEquals(80.772, $values[0]->value);
        $this->assertEquals('2016-10-10T11:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $values[0]->interval);
        $this->assertEquals(80.792, $values[1]->value);
        $this->assertEquals('2016-10-10T11:05:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $values[1]->interval);
        $this->assertEquals(80.812, $values[2]->value);
        $this->assertEquals('2016-10-10T11:10:00+02:00', $values[2]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $values[2]->interval);
        $this->assertEquals(80.832, $values[3]->value);
        $this->assertEquals('2016-10-10T11:15:00+02:00', $values[3]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $values[3]->interval);

        $values = $abbreviationsMeasurements['B_E_EXP'];
        $this->assertCount(4, $values);
        $this->assertEquals(1347.772, $values[0]->value);
        $this->assertEquals('2016-10-10T11:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $values[0]->interval);
        $this->assertEquals(1347.792, $values[1]->value);
        $this->assertEquals('2016-10-10T11:05:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $values[1]->interval);
        $this->assertEquals(1347.812, $values[2]->value);
        $this->assertEquals('2016-10-10T11:10:00+02:00', $values[2]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $values[2]->interval);
        $this->assertEquals(1347.832, $values[3]->value);
        $this->assertEquals('2016-10-10T11:15:00+02:00', $values[3]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $values[3]->interval);
    }

    public function testGetBatteriesBulkData() {
        $json = file_get_contents(__DIR__ . '/responses/getBatteryBulk.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo('systems/ABCDE/batteries/bulk/measurements'),
                $this->identicalToUrl([
                    RequestOptions::QUERY => 'from=2016-10-10T11:00:00+02:00&to=2016-10-10T11:15:00+02:00'
                ])
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-10-10T11:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-10-10T11:15:00+02:00'));

        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->batteries()->bulk()->measurements()->get($criteria);

        $this->assertEquals($json, $bulkReader->getAsString());
        $this->assertEquals(json_decode($json, true), $bulkReader->getAsArray());
    }

    public function testGetBatteriesBulkDataWithAbbreviationsFilter() {
        $json = file_get_contents(__DIR__ . '/responses/getBatteryBulkWithAbbreviationsFilter.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo('systems/ABCDE/batteries/bulk/measurements'),
                $this->identicalToUrl([
                    RequestOptions::QUERY => 'from=2016-10-10T11:00:00+02:00&to=2016-10-10T11:15:00+02:00&abbreviations=B_CHARGE_LEVEL,T1'
                ])
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-10-10T11:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-10-10T11:15:00+02:00'))
            ->withAbbreviation(['B_CHARGE_LEVEL', 'T1']);

        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->batteries()->bulk()->measurements()->get($criteria);

        $this->assertEquals($json, $bulkReader->getAsString());
        $this->assertEquals(json_decode($json, true), $bulkReader->getAsArray());
    }

    public function testGetBatteriesBulkDataWithCsvFormat() {
        $cvsRawData = file_get_contents(__DIR__ . '/responses/bulkCsv/getBatteryBulk.csv');
        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo('systems/ABCDE/batteries/bulk/measurements'),
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
        $bulkReader = $this->api->system('ABCDE')->batteries()->bulk()->measurements()->get($criteria);

        $this->assertEquals($cvsRawData, $bulkReader->getAsString());
    }

    public function testGetBatteriesBulkDataWithCsvFormatWithWrongParameter() {
        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-09-01T10:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-09-01T10:15:00+02:00'))
            ->withFormat(CsvFormat::FORMAT_CSV)
            ->withDelimiter(CsvFormat::DELIMITER_COMMA)
            ->withDecimalPoint(CsvFormat::DECIMAL_POINT_COMMA)
            ->withPrecision(CsvFormat::PRECISION_2);

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage("Delimiter and decimal point symbols can't be the same");

        $this->api->system('ABCDE')->batteries()->bulk()->measurements()->get($criteria);
    }
}
