<?php

namespace meteocontrol\client\vcomapi\tests\unit\systems;

use DateTime;
use meteocontrol\client\vcomapi\filters\ForecastCriteria;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\client\vcomapi\readers\CsvFormat;
use meteocontrol\client\vcomapi\tests\unit\TestCase;

class ForecastsTest extends TestCase {

    public function testGetForecastsYield() {
        $json = file_get_contents(__DIR__ . '/responses/testGetForecastsYield.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/forecasts/yield/specific-energy'
                ),
                $this->identicalToUrl(
                    'from=2016-10-01T00:00:00+02:00&to=2016-12-31T23:59:59+01:00'
                )
            )
            ->willReturn($json);

        $measurementsCriteria = (new MeasurementsCriteria())
            ->withDateFrom(DateTime::createFromFormat(DateTime::ATOM, '2016-10-01T00:00:00+02:00'))
            ->withDateTo(DateTime::createFromFormat(DateTime::ATOM, '2016-12-31T23:59:59+01:00'));

        $yields = $this->api->system('ABCDE')
            ->forecasts()->forecastsYield()->specificEnergy()->get($measurementsCriteria);

        $this->assertEquals('2016-10-01T00:00:00+02:00', $yields[0]->timestamp->format(DateTime::RFC3339));
        $this->assertEquals(59.759999999999998, $yields[0]->value);
        $this->assertEquals('2016-11-01T00:00:00+01:00', $yields[1]->timestamp->format(DateTime::RFC3339));
        $this->assertEquals(33.709620000000001, $yields[1]->value);
        $this->assertEquals('2016-12-01T00:00:00+01:00', $yields[2]->timestamp->format(DateTime::RFC3339));
        $this->assertEquals(24.437856, $yields[2]->value);
    }

    public function testGetForecastInJson() {
        $json = file_get_contents(__DIR__ . '/responses/testGetForecast.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/forecasts/forecast'
                ),
                $this->identicalToUrl(
                    'hours_to_future=1&timezone=Europe/Berlin&resolution=fifteen-minutes&format=json'
                )
            )
            ->willReturn($json);

        $forecastCriteria = (new ForecastCriteria())
            ->withHoursToFuture(1)
            ->withTimezone('Europe/Berlin')
            ->withResolution(ForecastCriteria::RESOLUTION_FIFTEEN_MINUTES)
            ->withFormat(CsvFormat::FORMAT_JSON);

        $forecasts = $this->api->system('ABCDE')->forecasts()->forecast()->get($forecastCriteria);
        $forecasts = $forecasts->getAsArray()['data'];
        $this->assertEquals('2021-11-06T12:30:00+01:00', $forecasts[0]['timestamp']);
        $this->assertEquals('2021-11-05T12:30:00+01:00', $forecasts[0]['calculation_timestamp']);
        $this->assertEquals('ABCDE', $forecasts[0]['systemKey']);
        $this->assertEquals(35.31, $forecasts[0]['power']);

        $this->assertEquals('2021-11-06T12:45:00+01:00', $forecasts[1]['timestamp']);
        $this->assertEquals('2021-11-05T12:30:00+01:00', $forecasts[1]['calculation_timestamp']);
        $this->assertEquals('ABCDE', $forecasts[1]['systemKey']);
        $this->assertEquals(40.31, $forecasts[1]['power']);

        $this->assertEquals('2021-11-06T13:00:00+01:00', $forecasts[2]['timestamp']);
        $this->assertEquals('2021-11-05T12:30:00+01:00', $forecasts[2]['calculation_timestamp']);
        $this->assertEquals('ABCDE', $forecasts[2]['systemKey']);
        $this->assertEquals(50.0, $forecasts[2]['power']);

        $this->assertEquals('2021-11-06T13:15:00+01:00', $forecasts[3]['timestamp']);
        $this->assertEquals('2021-11-05T12:30:00+01:00', $forecasts[3]['calculation_timestamp']);
        $this->assertEquals('ABCDE', $forecasts[3]['systemKey']);
        $this->assertEquals(0, $forecasts[3]['power']);
    }

    public function testGetForecastInCsv() {
        $csvContent = file_get_contents(__DIR__ . '/responses/testGetForecast.csv');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/forecasts/forecast'
                ),
                $this->identicalToUrl(
                    'hours_to_future=1&timezone=Europe/Berlin&resolution=fifteen-minutes&format=csv'
                )
            )
            ->willReturn($csvContent);

        $forecastCriteria = (new ForecastCriteria())
            ->withHoursToFuture(1)
            ->withTimezone('Europe/Berlin')
            ->withResolution(ForecastCriteria::RESOLUTION_FIFTEEN_MINUTES)
            ->withFormat(CsvFormat::FORMAT_CSV);

        $forecasts = $this->api->system('ABCDE')->forecasts()->forecast()->get($forecastCriteria);

        $this->assertStringEqualsFile(__DIR__ . '/responses/testGetForecast.csv', $forecasts->getAsString());
    }
}
