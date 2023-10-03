<?php

namespace meteocontrol\client\vcomapi\tests\unit\systems;

use DateTime;
use InvalidArgumentException;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\client\vcomapi\model\DevicesMeasurementWithInterval;
use meteocontrol\client\vcomapi\readers\CsvFormat;
use meteocontrol\client\vcomapi\readers\MeasurementsBulkReader;
use meteocontrol\client\vcomapi\tests\unit\TestCase;
use meteocontrol\vcomapi\model\Abbreviation;
use meteocontrol\vcomapi\model\DevicesMeasurement;
use meteocontrol\vcomapi\model\Tracker;
use meteocontrol\vcomapi\model\TrackerDetail;
use UnexpectedValueException;

class TrackersTest extends TestCase {

    public function testGetTrackers() {
        $json = file_get_contents(__DIR__ . '/responses/getTrackers.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/trackers'))
            ->willReturn($json);

        /** @var Tracker[] $trackers */
        $trackers = $this->api->system('ABCDE')->trackers()->get();

        $this->assertCount(2, $trackers);
        $this->assertEquals('30001', $trackers[0]->id);
        $this->assertEquals('Tracker 1', $trackers[0]->name);
        $this->assertEquals('30002', $trackers[1]->id);
        $this->assertEquals('Tracker 2', $trackers[1]->name);
    }

    public function testGetSingleTracker() {
        $json = file_get_contents(__DIR__ . '/responses/getTracker.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/trackers/30001'))
            ->willReturn($json);

        /** @var TrackerDetail $tracker */
        $tracker = $this->api->system('ABCDE')->tracker('30001')->get();

        $this->assertEquals('30001', $tracker->id);
        $this->assertEquals('Tracker 1', $tracker->name);
        $this->assertEquals('10.80.67.101:502-1-1', $tracker->address);
        $this->assertEquals('IDEEMATEC', $tracker->vendor);
        $this->assertEquals('HORIZON 2', $tracker->model);
        $this->assertEquals('1.1', $tracker->firmware);
    }

    public function testGetTrackerAbbreviations() {
        $json = file_get_contents(__DIR__ . '/responses/getTrackerAbbreviations.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/trackers/30001/abbreviations'))
            ->willReturn($json);

        /** @var string[] $abbreviations */
        $abbreviations = $this->api->system('ABCDE')->tracker('30001')->abbreviations()->get();

        $this->assertCount(4, $abbreviations);
        $this->assertEquals('AZIMUTH', $abbreviations[0]);
        $this->assertEquals('ELEVATION', $abbreviations[1]);
        $this->assertEquals('TRACKER_TYPE', $abbreviations[2]);
        $this->assertEquals('STATE', $abbreviations[3]);
    }

    public function testGetTrackerSingleAbbreviation() {
        $json = file_get_contents(__DIR__ . '/responses/getTrackerSingleAbbreviation.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/trackers/30001/abbreviations/ELEVATION'))
            ->willReturn($json);

        /** @var Abbreviation $abbreviation */
        $abbreviation = $this->api->system('ABCDE')->tracker('30001')->abbreviation('ELEVATION')->get();

        $this->assertEquals('AVG', $abbreviation->aggregation);
        $this->assertEquals('Tilt, actual value', $abbreviation->description);
        $this->assertEquals(0, $abbreviation->precision);
        $this->assertEquals('°', $abbreviation->unit);
    }

    public function testGetTrackerMeasurements() {
        $json = file_get_contents(__DIR__ . '/responses/getTrackerMeasurements.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/trackers/30001,30002/abbreviations/AZIMUTH,ELEVATION/measurements'
                ),
                $this->identicalToUrl(
                    'from=2016-10-10T11:00:00+02:00&to=2016-10-10T11:05:00+02:00'
                )
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-10-10T11:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-10-10T11:05:00+02:00'));

        /** @var DevicesMeasurement $measurements */
        $measurements = $this->api->system('ABCDE')->tracker('30001,30002')
            ->abbreviation(['AZIMUTH', 'ELEVATION'])
            ->measurements()->get($criteria);

        $this->assertCount(2, $measurements);
        $abbreviationsMeasurements = $measurements['30001'];
        $values = $abbreviationsMeasurements['AZIMUTH'];
        $this->assertCount(2, $values);
        $this->assertEqualsWithDelta(80.762, $values[0]->value, 0.001);
        $this->assertEquals('2016-10-10T11:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEqualsWithDelta(80.782, $values[1]->value, 0.001);
        $this->assertEquals('2016-10-10T11:05:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $values = $abbreviationsMeasurements['ELEVATION'];
        $this->assertCount(2, $values);
        $this->assertEqualsWithDelta(134.762, $values[0]->value, 0.001);
        $this->assertEquals('2016-10-10T11:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEqualsWithDelta(134.782, $values[1]->value, 0.001);
        $this->assertEquals('2016-10-10T11:05:00+02:00', $values[1]->timestamp->format(DATE_ATOM));

        $abbreviationsMeasurements = $measurements['30002'];
        $values = $abbreviationsMeasurements['AZIMUTH'];
        $this->assertCount(2, $values);
        $this->assertEqualsWithDelta(80.772, $values[0]->value, 0.001);
        $this->assertEquals('2016-10-10T11:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEqualsWithDelta(80.792, $values[1]->value, 0.001);
        $this->assertEquals('2016-10-10T11:05:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $values = $abbreviationsMeasurements['ELEVATION'];
        $this->assertCount(2, $values);
        $this->assertEqualsWithDelta(134.772, $values[0]->value, 0.001);
        $this->assertEquals('2016-10-10T11:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEqualsWithDelta(134.792, $values[1]->value, 0.001);
        $this->assertEquals('2016-10-10T11:05:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
    }

    public function testGetTrackerMeasurementsWithIntervalIncluded() {
        $json = file_get_contents(__DIR__ . '/responses/getTrackerMeasurementsIncludeInterval.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/trackers/30001,30002/abbreviations/AZIMUTH,ELEVATION/measurements'
                ),
                $this->identicalToUrl(
                    'from=2016-10-10T11:00:00+02:00&to=2016-10-10T11:05:00+02:00&includeInterval=1'
                )
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-10-10T11:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-10-10T11:05:00+02:00'))
            ->withIntervalIncluded();

        /** @var DevicesMeasurementWithInterval $measurements */
        $measurements = $this->api->system('ABCDE')->tracker('30001,30002')
            ->abbreviation(['AZIMUTH', 'ELEVATION'])
            ->measurements()->get($criteria);

        $this->assertCount(2, $measurements);
        $abbreviationsMeasurements = $measurements['30001'];
        $values = $abbreviationsMeasurements['AZIMUTH'];
        $this->assertCount(2, $values);
        $this->assertEqualsWithDelta(80.762, $values[0]->value, 0.001);
        $this->assertEquals('2016-10-10T11:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[0]->interval);
        $this->assertEqualsWithDelta(80.782, $values[1]->value, 0.001);
        $this->assertEquals('2016-10-10T11:05:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[1]->interval);

        $values = $abbreviationsMeasurements['ELEVATION'];
        $this->assertCount(2, $values);
        $this->assertEqualsWithDelta(134.762, $values[0]->value, 0.001);
        $this->assertEquals('2016-10-10T11:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[0]->interval);
        $this->assertEqualsWithDelta(134.782, $values[1]->value, 0.001);
        $this->assertEquals('2016-10-10T11:05:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[1]->interval);


        $abbreviationsMeasurements = $measurements['30002'];
        $values = $abbreviationsMeasurements['AZIMUTH'];
        $this->assertCount(2, $values);
        $this->assertEqualsWithDelta(80.772, $values[0]->value, 0.001);
        $this->assertEquals('2016-10-10T11:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[0]->interval);
        $this->assertEqualsWithDelta(80.792, $values[1]->value, 0.001);
        $this->assertEquals('2016-10-10T11:05:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[1]->interval);

        $values = $abbreviationsMeasurements['ELEVATION'];
        $this->assertCount(2, $values);
        $this->assertEqualsWithDelta(134.772, $values[0]->value, 0.001);
        $this->assertEquals('2016-10-10T11:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[0]->interval);
        $this->assertEqualsWithDelta(134.792, $values[1]->value, 0.001);
        $this->assertEquals('2016-10-10T11:05:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(300, $values[1]->interval);
    }

    public function testGetTrackerMeasurementsWithIntervalIncludedWithResolution() {
        $json = file_get_contents(__DIR__ . '/responses/getTrackerMeasurements.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/trackers/30001,30002/abbreviations/AZIMUTH,ELEVATION/measurements'
                ),
                $this->identicalToUrl(
                    'from=2016-10-10T11:00:00+02:00&to=2016-10-10T11:05:00+02:00&resolution=interval&includeInterval=1'
                )
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-10-10T11:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-10-10T11:05:00+02:00'))
            ->withResolution(MeasurementsCriteria::RESOLUTION_INTERVAL)
            ->withIntervalIncluded();

        /** @var DevicesMeasurementWithInterval $measurements */
        @$measurements = $this->api->system('ABCDE')->tracker('30001,30002')
            ->abbreviation(['AZIMUTH', 'ELEVATION'])
            ->measurements()->get($criteria);

        $this->assertCount(2, $measurements);
        $abbreviationsMeasurements = $measurements['30001'];
        $values = $abbreviationsMeasurements['AZIMUTH'];
        $this->assertCount(2, $values);
        $this->assertEqualsWithDelta(80.762, $values[0]->value, 0.001);
        $this->assertEquals('2016-10-10T11:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $values[0]->interval);
        $this->assertEqualsWithDelta(80.782, $values[1]->value, 0.001);
        $this->assertEquals('2016-10-10T11:05:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $values[1]->interval);

        $values = $abbreviationsMeasurements['ELEVATION'];
        $this->assertCount(2, $values);
        $this->assertEqualsWithDelta(134.762, $values[0]->value, 0.001);
        $this->assertEquals('2016-10-10T11:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $values[0]->interval);
        $this->assertEqualsWithDelta(134.782, $values[1]->value, 0.001);
        $this->assertEquals('2016-10-10T11:05:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $values[1]->interval);


        $abbreviationsMeasurements = $measurements['30002'];
        $values = $abbreviationsMeasurements['AZIMUTH'];
        $this->assertCount(2, $values);
        $this->assertEqualsWithDelta(80.772, $values[0]->value, 0.001);
        $this->assertEquals('2016-10-10T11:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $values[0]->interval);
        $this->assertEqualsWithDelta(80.792, $values[1]->value, 0.001);
        $this->assertEquals('2016-10-10T11:05:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $values[1]->interval);

        $values = $abbreviationsMeasurements['ELEVATION'];
        $this->assertCount(2, $values);
        $this->assertEqualsWithDelta(134.772, $values[0]->value, 0.001);
        $this->assertEquals('2016-10-10T11:00:00+02:00', $values[0]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $values[0]->interval);
        $this->assertEqualsWithDelta(134.792, $values[1]->value, 0.001);
        $this->assertEquals('2016-10-10T11:05:00+02:00', $values[1]->timestamp->format(DATE_ATOM));
        $this->assertEquals(null, $values[1]->interval);
    }

    public function testGetTrackersBulkData() {
        $json = file_get_contents(__DIR__ . '/responses/getTrackerBulk.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/trackers/bulk/measurements'),
                $this->identicalToUrl('from=2016-10-10T11:00:00+02:00&to=2016-10-10T11:05:00+02:00')
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-10-10T11:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-10-10T11:05:00+02:00'));

        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->trackers()->bulk()->measurements()->get($criteria);

        $this->assertEquals($json, $bulkReader->getAsString());
        $this->assertEquals(json_decode($json, true), $bulkReader->getAsArray());
    }

    public function testGetTrackersBulkDataWithAbbreviationsFilter() {
        $json = file_get_contents(__DIR__ . '/responses/getTrackerBulkWithAbbreviationsFilter.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/trackers/bulk/measurements'),
                $this->identicalToUrl(
                    'from=2016-10-10T11:00:00+02:00&to=2016-10-10T11:05:00+02:00&abbreviations=AZIMUTH,STATE'
                )
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-10-10T11:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-10-10T11:05:00+02:00'))
            ->withAbbreviation(['AZIMUTH', 'STATE']);

        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->trackers()->bulk()->measurements()->get($criteria);

        $this->assertEquals($json, $bulkReader->getAsString());
        $this->assertEquals(json_decode($json, true), $bulkReader->getAsArray());
    }

    public function testGetTrackersBulkDataWithCsvFormat() {
        $cvsRawData = file_get_contents(__DIR__ . '/responses/bulkCsv/getTrackerBulk.csv');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/trackers/bulk/measurements'),
                $this->identicalToUrl(
                    'from=2016-09-01T10:00:00+02:00&to=2016-09-01T10:05:00+02:00&format=csv'
                )
            )
            ->willReturn($cvsRawData);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-09-01T10:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-09-01T10:05:00+02:00'))
            ->withFormat(CsvFormat::FORMAT_CSV);
        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->trackers()->bulk()->measurements()->get($criteria);

        $this->assertEquals($cvsRawData, $bulkReader->getAsString());
    }

    public function testGetTrackersBulkDataWithCsvFormatWithWrongParameter() {
        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-09-01T10:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-09-01T10:15:00+02:00'))
            ->withFormat(CsvFormat::FORMAT_CSV)
            ->withDelimiter(CsvFormat::DELIMITER_COMMA)
            ->withDecimalPoint(CsvFormat::DECIMAL_POINT_COMMA)
            ->withPrecision(CsvFormat::PRECISION_2);

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage("Delimiter and decimal point symbols can't be the same");

        $this->api->system('ABCDE')->trackers()->bulk()->measurements()->get($criteria);
    }
}
