<?php

namespace meteocontrol\client\vcomapi\tests\unit\systems;

use DateTime;
use InvalidArgumentException;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\client\vcomapi\model\DevicesMeasurement;
use meteocontrol\client\vcomapi\model\DevicesMeasurementWithInterval;
use meteocontrol\client\vcomapi\model\Stringbox;
use meteocontrol\client\vcomapi\model\StringboxDetail;
use meteocontrol\client\vcomapi\readers\CsvFormat;
use meteocontrol\client\vcomapi\readers\MeasurementsBulkReader;
use meteocontrol\client\vcomapi\tests\unit\TestCase;
use UnexpectedValueException;

class StringboxesTest extends TestCase {

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
                $this->identicalTo('systems/ABCDE/stringboxes/816639')
            )
            ->willReturn($json);
        $expectedDevice = $this->getExpectedStringBoxDevice();
        $actualDevice = $this->api->system('ABCDE')->stringbox("816639")->get();
        $this->assertEquals($expectedDevice, $actualDevice);
    }

    public function testGetStringboxAbbreviations() {
        $json = file_get_contents(__DIR__ . '/responses/getStringboxAbbreviations.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/stringboxes/816639/abbreviations'))
            ->willReturn($json);

        /** @var string[] $abbreviations */
        $abbreviations = $this->api->system('ABCDE')->stringbox('816639')->abbreviations()->get();

        $this->assertCount(10, $abbreviations);
        $this->assertEquals('D_IN2', $abbreviations[0]);
        $this->assertEquals('I1', $abbreviations[1]);
        $this->assertEquals('I1_N', $abbreviations[2]);
        $this->assertEquals('I2', $abbreviations[3]);
        $this->assertEquals('I2_N', $abbreviations[4]);
        $this->assertEquals('I3', $abbreviations[5]);
        $this->assertEquals('I3_N', $abbreviations[6]);
        $this->assertEquals('STATE', $abbreviations[7]);
        $this->assertEquals('T1', $abbreviations[8]);
        $this->assertEquals('U_DC', $abbreviations[9]);
    }

    public function testGetStringboxSingleAbbreviation() {
        $json = file_get_contents(__DIR__ . '/responses/getStringboxSingleAbbreviation.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/stringboxes/816639/abbreviations/I1'))
            ->willReturn($json);

        /** @var \meteocontrol\client\vcomapi\model\StringboxAbbreviation $abbreviation */
        $abbreviation = $this->api->system('ABCDE')->stringbox('816639')->abbreviation('I1')->get();

        $this->assertEquals('AVG', $abbreviation->aggregation);
        $this->assertEquals('Current DC', $abbreviation->description);
        $this->assertEquals(null, $abbreviation->precision);
        $this->assertEquals('A', $abbreviation->unit);
        $this->assertEquals(true, $abbreviation->active);
    }

    public function testGetStringboxMeasurements() {
        $json = file_get_contents(__DIR__ . '/responses/getStringboxMeasurements.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/stringboxes/816639,816640/abbreviations/I1,I2/measurements'
                ),
                $this->identicalToUrl(
                    'from=2016-10-31T15:10:00+02:00&to=2016-10-31T15:15:00+02:00'
                )
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-10-31T15:10:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-10-31T15:15:00+02:00'));

        /** @var DevicesMeasurement $measurements */
        $measurements = $this->api->system('ABCDE')->stringbox('816639,816640')
            ->abbreviation(['I1', 'I2'])
            ->measurements()->get($criteria);
        $this->assertCount(2, $measurements);
        $abbreviationsMeasurements = $measurements['816639'];
        $values = $abbreviationsMeasurements['I1'];
        $this->assertCount(2, $values);
        $this->assertEqualsWithDelta(0.4512, $values[0]->value, 0.0001);
        $this->assertEquals('2016-10-31T15:10:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEqualsWithDelta(0.6075, $values[1]->value, 0.0001);
        $this->assertEquals('2016-10-31T15:15:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $values = $abbreviationsMeasurements['I2'];
        $this->assertCount(2, $values);
        $this->assertEqualsWithDelta(0.4668, $values[0]->value, 0.0001);
        $this->assertEquals('2016-10-31T15:10:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEqualsWithDelta(0.6237, $values[1]->value, 0.0001);
        $this->assertEquals('2016-10-31T15:15:00+02:00', $values[1]->timestamp->format(DATE_ATOM));

        $abbreviationsMeasurements = $measurements['816640'];
        $values = $abbreviationsMeasurements['I1'];
        $this->assertCount(2, $values);
        $this->assertEqualsWithDelta(0.4382, $values[0]->value, 0.0001);
        $this->assertEquals('2016-10-31T15:10:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEqualsWithDelta(0.6149, $values[1]->value, 0.0001);
        $this->assertEquals('2016-10-31T15:15:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $values = $abbreviationsMeasurements['I2'];
        $this->assertCount(2, $values);
        $this->assertEqualsWithDelta(0.4226, $values[0]->value, 0.0001);
        $this->assertEquals('2016-10-31T15:10:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEqualsWithDelta(0.5962, $values[1]->value, 0.0001);
        $this->assertEquals('2016-10-31T15:15:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
    }

    public function testGetStringboxMeasurementsWithIntervalIncluded() {
        $json = file_get_contents(__DIR__ . '/responses/getStringboxMeasurementsIncludeInterval.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/stringboxes/816639,816640/abbreviations/I1,I2/measurements'
                ),
                $this->identicalToUrl(
                    'from=2016-10-31T15:10:00+02:00&to=2016-10-31T15:15:00+02:00&includeInterval=1'
                )
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-10-31T15:10:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-10-31T15:15:00+02:00'))
            ->withIntervalIncluded();

        /** @var DevicesMeasurementWithInterval $measurements */
        $measurements = $this->api->system('ABCDE')->stringbox('816639,816640')
            ->abbreviation(['I1', 'I2'])
            ->measurements()->get($criteria);
        $this->assertCount(2, $measurements);
        $abbreviationsMeasurements = $measurements['816639'];
        $values = $abbreviationsMeasurements['I1'];
        $this->assertCount(2, $values);
        $this->assertEqualsWithDelta(0.4512, $values[0]->value, 0.0001);
        $this->assertEquals('2016-10-31T15:10:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[0]->interval);
        $this->assertEqualsWithDelta(0.6075, $values[1]->value, 0.0001);
        $this->assertEquals('2016-10-31T15:15:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[1]->interval);
        $values = $abbreviationsMeasurements['I2'];
        $this->assertCount(2, $values);
        $this->assertEqualsWithDelta(0.4668, $values[0]->value, 0.0001);
        $this->assertEquals('2016-10-31T15:10:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[0]->interval);
        $this->assertEqualsWithDelta(0.6237, $values[1]->value, 0.0001);
        $this->assertEquals('2016-10-31T15:15:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[1]->interval);

        $abbreviationsMeasurements = $measurements['816640'];
        $values = $abbreviationsMeasurements['I1'];
        $this->assertCount(2, $values);
        $this->assertEqualsWithDelta(0.4382, $values[0]->value, 0.0001);
        $this->assertEquals('2016-10-31T15:10:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[0]->interval);
        $this->assertEqualsWithDelta(0.6149, $values[1]->value, 0.0001);
        $this->assertEquals('2016-10-31T15:15:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[1]->interval);
        $values = $abbreviationsMeasurements['I2'];
        $this->assertCount(2, $values);
        $this->assertEqualsWithDelta(0.4226, $values[0]->value, 0.0001);
        $this->assertEquals('2016-10-31T15:10:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[0]->interval);
        $this->assertEqualsWithDelta(0.5962, $values[1]->value, 0.0001);
        $this->assertEquals('2016-10-31T15:15:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[1]->interval);
    }

    public function testGetStringboxMeasurementsWithIntervalIncludedWithWrongResolution() {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"includeInterval" is only accepted with interval resolution');

        $json = file_get_contents(__DIR__ . '/responses/getStringboxMeasurements.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/stringboxes/816639,816640/abbreviations/I1,I2/measurements'
                ),
                $this->identicalToUrl(
                    'from=2016-10-31T15:10:00+02:00&to=2016-10-31T15:15:00+02:00&resolution=day&includeInterval=1'
                )
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-10-31T15:10:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-10-31T15:15:00+02:00'))
            ->withResolution(MeasurementsCriteria::RESOLUTION_DAY)
            ->withIntervalIncluded();

        $this->api->system('ABCDE')->stringbox('816639,816640')
            ->abbreviation(['I1', 'I2'])
            ->measurements()->get($criteria);
    }

    public function testGetStringboxMeasurementsWithIntervalIncludedWithResolution() {
        $json = file_get_contents(__DIR__ . '/responses/getStringboxMeasurements.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/stringboxes/816639,816640/abbreviations/I1,I2/measurements'
                ),
                $this->identicalToUrl(
                    'from=2016-10-31T15:10:00+02:00&to=2016-10-31T15:15:00+02:00&resolution=interval&includeInterval=1'
                )
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-10-31T15:10:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-10-31T15:15:00+02:00'))
            ->withResolution(MeasurementsCriteria::RESOLUTION_INTERVAL)
            ->withIntervalIncluded();

        /** @var DevicesMeasurementWithInterval $measurements */
        @$measurements = $this->api->system('ABCDE')->stringbox('816639,816640')
            ->abbreviation(['I1', 'I2'])
            ->measurements()->get($criteria);
        $this->assertCount(2, $measurements);
        $abbreviationsMeasurements = $measurements['816639'];
        $values = $abbreviationsMeasurements['I1'];
        $this->assertCount(2, $values);
        $this->assertEqualsWithDelta(0.4512, $values[0]->value, 0.0001);
        $this->assertEquals('2016-10-31T15:10:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $values[0]->interval);
        $this->assertEqualsWithDelta(0.6075, $values[1]->value, 0.0001);
        $this->assertEquals('2016-10-31T15:15:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $values[1]->interval);
        $values = $abbreviationsMeasurements['I2'];
        $this->assertCount(2, $values);
        $this->assertEqualsWithDelta(0.4668, $values[0]->value, 0.0001);
        $this->assertEquals('2016-10-31T15:10:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $values[0]->interval);
        $this->assertEqualsWithDelta(0.6237, $values[1]->value, 0.0001);
        $this->assertEquals('2016-10-31T15:15:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $values[1]->interval);

        $abbreviationsMeasurements = $measurements['816640'];
        $values = $abbreviationsMeasurements['I1'];
        $this->assertCount(2, $values);
        $this->assertEqualsWithDelta(0.4382, $values[0]->value, 0.0001);
        $this->assertEquals('2016-10-31T15:10:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $values[0]->interval);
        $this->assertEqualsWithDelta(0.6149, $values[1]->value, 0.0001);
        $this->assertEquals('2016-10-31T15:15:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $values[1]->interval);
        $values = $abbreviationsMeasurements['I2'];
        $this->assertCount(2, $values);
        $this->assertEqualsWithDelta(0.4226, $values[0]->value, 0.0001);
        $this->assertEquals('2016-10-31T15:10:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $values[0]->interval);
        $this->assertEqualsWithDelta(0.5962, $values[1]->value, 0.0001);
        $this->assertEquals('2016-10-31T15:15:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $values[1]->interval);
    }

    public function testGetStringboxesBulkData() {
        $json = file_get_contents(__DIR__ . '/responses/getStringboxBulk.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/stringboxes/bulk/measurements'),
                $this->identicalToUrl('from=2016-09-01T10:00:00+02:00&to=2016-09-01T10:15:00+02:00')
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-09-01T10:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-09-01T10:15:00+02:00'));

        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->stringboxes()->bulk()->measurements()->get($criteria);

        $this->assertEquals($json, $bulkReader->getAsString());
        $this->assertEquals(json_decode($json, true), $bulkReader->getAsArray());
    }

    public function testGetStringboxesBulkDataWithAbbreviationsFilter() {
        $json = file_get_contents(__DIR__ . '/responses/getStringboxBulkWithAbbreviationsFilter.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/stringboxes/bulk/measurements'),
                $this->identicalToUrl(
                    'from=2016-09-01T10:00:00+02:00&to=2016-09-01T10:15:00+02:00&abbreviations=I1,I8_N'
                )
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-09-01T10:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-09-01T10:15:00+02:00'))
            ->withAbbreviation(['I1', 'I8_N']);

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
                $this->identicalToUrl(
                    'from=2016-09-01T10:00:00+02:00&to=2016-09-01T10:15:00+02:00&format=csv'
                )
            )
            ->willReturn($cvsRawData);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-09-01T10:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-09-01T10:15:00+02:00'))
            ->withFormat(CsvFormat::FORMAT_CSV);
        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->stringboxes()->bulk()->measurements()->get($criteria);

        $this->assertEquals($cvsRawData, $bulkReader->getAsString());
    }

    public function testGetStringboxesBulkDataWithActiveOnlyOption() {
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/stringboxes/bulk/measurements'),
                $this->identicalToUrl(
                    'from=2016-09-01T10:00:00+02:00&to=2016-09-01T10:15:00+02:00&activeOnly=1'
                )
            )
            ->willReturn('');

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-09-01T10:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-09-01T10:15:00+02:00'))
            ->withActiveOnly();

        $this->api->system('ABCDE')->stringboxes()->bulk()->measurements()->get($criteria);
    }

    public function testGetStringboxesBulkDataWithCsvFormatWithWrongParameter() {
        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-09-01T10:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-09-01T10:15:00+02:00'))
            ->withFormat(CsvFormat::FORMAT_CSV)
            ->withDelimiter(CsvFormat::DELIMITER_COMMA)
            ->withDecimalPoint(CsvFormat::DECIMAL_POINT_COMMA)
            ->withPrecision(CsvFormat::PRECISION_2);

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage("Delimiter and decimal point symbols can't be the same");

        $this->api->system('ABCDE')->stringboxes()->bulk()->measurements()->get($criteria);
    }

    /**
     * @return Stringbox[]
     */
    private function getExpectedStringBoxDevices() {
        $device1 = new Stringbox();
        $device1->id = "816639";
        $device1->name = "E18.S01 A";
        $device2 = new Stringbox();
        $device2->id = "816640";
        $device2->name = "E18.S02 B";
        return [$device1, $device2];
    }

    /**
     * @return StringboxDetail
     */
    private function getExpectedStringBoxDevice() {
        $device = new StringboxDetail();
        $device->id = "816639";
        $device->name = "E18.S01 A";
        $device->serial = "12933";
        $device->scaleFactor = 2.0;
        return $device;
    }
}
