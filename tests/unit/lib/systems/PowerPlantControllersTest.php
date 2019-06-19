<?php

namespace meteocontrol\client\vcomapi\tests\unit\systems;

use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\client\vcomapi\model\DevicesMeasurement;
use meteocontrol\client\vcomapi\readers\CsvFormat;
use meteocontrol\client\vcomapi\readers\MeasurementsBulkReader;
use meteocontrol\client\vcomapi\tests\unit\TestCase;
use meteocontrol\vcomapi\model\Abbreviation;
use meteocontrol\vcomapi\model\PowerPlantController;
use meteocontrol\vcomapi\model\PowerPlantControllerDetail;

class PowerPlantControllersTest extends TestCase {

    public function testGetPowerPlantControllers() {
        $json = file_get_contents(__DIR__ . '/responses/getPowerPlantControllers.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/power-plant-controllers'))
            ->willReturn($json);

        /** @var PowerPlantController[] $powerPlantControllers */
        $powerPlantControllers = $this->api->system('ABCDE')->powerPlantControllers()->get();

        $this->assertEquals(1, count($powerPlantControllers));
        $this->assertEquals('163784', $powerPlantControllers[0]->id);
        $this->assertEquals('ppc-5bbc370ddb808', $powerPlantControllers[0]->name);
    }

    public function testGetSinglePowerPlantController() {
        $json = file_get_contents(__DIR__ . '/responses/getPowerPlantController.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/power-plant-controllers/163784'))
            ->willReturn($json);

        /** @var PowerPlantControllerDetail $powerPlantControllerDetail */
        $powerPlantControllerDetail = $this->api->system('ABCDE')->powerPlantController('163784')->get();

        $this->assertEquals('163784', $powerPlantControllerDetail->id);
        $this->assertEquals('ppc-5bbc370ddb808', $powerPlantControllerDetail->name);
        $this->assertEquals('ppc-5bbc370ddb808', $powerPlantControllerDetail->address);
        $this->assertNull($powerPlantControllerDetail->firmware);
    }

    public function testGetPowerPlantControllerAbbreviations() {
        $json = file_get_contents(__DIR__ . '/responses/getPowerPlantControllerAbbreviations.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/power-plant-controllers/163784/abbreviations'))
            ->willReturn($json);

        /** @var string[] $abbreviations */
        $abbreviations = $this->api->system('ABCDE')->powerPlantController('163784')->abbreviations()->get();

        $this->assertEquals(15, count($abbreviations));
        $this->assertEquals('PPC_P_AC', $abbreviations[0]);
        $this->assertEquals('PPC_P_AC_AVAIL', $abbreviations[1]);
        $this->assertEquals('PPC_P_AC_GRIDOP_MAX', $abbreviations[2]);
        $this->assertEquals('PPC_P_AC_INV', $abbreviations[3]);
        $this->assertEquals('PPC_P_AC_RPC_MAX', $abbreviations[4]);
        $this->assertEquals('PPC_P_SET_GRIDOP_ABS', $abbreviations[5]);
        $this->assertEquals('PPC_P_SET_GRIDOP_REL', $abbreviations[6]);
        $this->assertEquals('PPC_P_SET_REL', $abbreviations[7]);
        $this->assertEquals('PPC_P_SET_RPC_REL', $abbreviations[8]);
        $this->assertEquals('PPC_PF', $abbreviations[9]);
        $this->assertEquals('PPC_PF_SET', $abbreviations[10]);
        $this->assertEquals('PPC_Q_AC', $abbreviations[11]);
        $this->assertEquals('PPC_Q_AC_AVAIL', $abbreviations[12]);
        $this->assertEquals('PPC_Q_SET_ABS', $abbreviations[13]);
        $this->assertEquals('PPC_Q_SET_REL', $abbreviations[14]);
    }

    public function testGetPowerPlantControllerSingleAbbreviation() {
        $json = file_get_contents(__DIR__ . '/responses/getPowerPlantControllerSingleAbbreviation.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/power-plant-controllers/163784/abbreviations/PPC_P_AC'))
            ->willReturn($json);

        /** @var Abbreviation $abbreviation */
        $abbreviation = $this->api->system('ABCDE')->powerPlantController('163784')->abbreviation('PPC_P_AC')->get();

        $this->assertEquals('AVG', $abbreviation->aggregation);
        $this->assertEquals('Actual active power', $abbreviation->description);
        $this->assertEquals(3, $abbreviation->precision);
        $this->assertEquals('W', $abbreviation->unit);
    }

    public function testGetPowerPlantControllerMeasurements() {
        $json = file_get_contents(__DIR__ . '/responses/getPowerPlantControllerMeasurements.json');
        $this->api->expects($this->exactly(1))
            ->method('run')
            ->with(
                $this->identicalTo(
                    'systems/ABCDE/power-plant-controllers/163784/abbreviations/PPC_P_AC_AVAIL,PPC_P_AC/measurements'
                ),
                $this->identicalTo(
                    'from=2016-10-29T12%3A00%3A00%2B02%3A00&to=2016-10-29T12%3A05%3A00%2B02%3A00'
                )
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-10-29T12:00:00+02:00'))
            ->withDateTo(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-10-29T12:05:00+02:00'));

        /** @var DevicesMeasurement $measurements */
        $measurements = $this->api->system('ABCDE')->powerPlantController('163784')
            ->abbreviation(['PPC_P_AC_AVAIL', 'PPC_P_AC'])
            ->measurements()->get($criteria);
        $this->assertEquals(1, count($measurements));
        $abbreviationsMeasurements = $measurements['163784'];
        $values = $abbreviationsMeasurements['PPC_P_AC_AVAIL'];
        $this->assertEquals(2, count($values));
        $this->assertEquals(12.52, $values[0]->value);
        $this->assertEquals('2016-10-29T12:00:00+02:00', $values[0]->timestamp->format(\DateTime::RFC3339));
        $this->assertEquals(35.53, $values[1]->value);
        $this->assertEquals('2016-10-29T12:05:00+02:00', $values[1]->timestamp->format(\DateTime::RFC3339));
        $values = $abbreviationsMeasurements['PPC_P_AC'];
        $this->assertEquals(2, count($values));
        $this->assertEquals(65.84, $values[0]->value);
        $this->assertEquals('2016-10-29T12:00:00+02:00', $values[0]->timestamp->format(\DateTime::RFC3339));
        $this->assertEquals(22.01, $values[1]->value);
        $this->assertEquals('2016-10-29T12:05:00+02:00', $values[1]->timestamp->format(\DateTime::RFC3339));
    }

    public function testGetPowerPlantControllersBulkData() {
        $json = file_get_contents(__DIR__ . '/responses/getPowerPlantControllerBulk.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/power-plant-controllers/bulk/measurements'),
                $this->identicalTo('from=2016-10-29T12%3A00%3A00%2B02%3A00&to=2016-10-29T12%3A05%3A00%2B02%3A00')
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-10-29T12:00:00+02:00'))
            ->withDateTo(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-10-29T12:05:00+02:00'));

        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->powerPlantControllers()->bulk()->measurements()->get($criteria);

        $this->assertEquals($json, $bulkReader->getAsString());
        $this->assertEquals(json_decode($json, true), $bulkReader->getAsArray());
    }

    public function testGetPowerPlantControllersBulkDataWithAbbreviationsFilter() {
        $json = file_get_contents(__DIR__ . '/responses/getPowerPlantControllerBulkWithAbbreviationsFilter.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/power-plant-controllers/bulk/measurements'),
                $this->identicalTo(
                    'from=2016-10-29T12%3A00%3A00%2B02%3A00&to=2016-10-29T12%3A05%3A00%2B02%3A00'
                    . '&abbreviations=PPC_P_AC_INV%2CPPC_Q_AC_AVAIL%2CPPC_Q_SET_REL'
                )
            )
            ->willReturn($json);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-10-29T12:00:00+02:00'))
            ->withDateTo(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-10-29T12:05:00+02:00'))
            ->withAbbreviation("PPC_P_AC_INV")
            ->withAbbreviation("PPC_Q_AC_AVAIL")
            ->withAbbreviation("PPC_Q_SET_REL");

        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->powerPlantControllers()->bulk()->measurements()->get($criteria);

        $this->assertEquals($json, $bulkReader->getAsString());
        $this->assertEquals(json_decode($json, true), $bulkReader->getAsArray());
    }

    public function testGetPowerPlantControllersBulkDataWithCsvFormat() {
        $cvsRawData = file_get_contents(__DIR__ . '/responses/bulkCsv/getPowerPlantControllerBulk.csv');
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/power-plant-controllers/bulk/measurements'),
                $this->identicalTo(
                    'from=2016-10-29T12%3A00%3A00%2B02%3A00&to=2016-10-29T12%3A05%3A00%2B02%3A00&format=csv'
                )
            )
            ->willReturn($cvsRawData);

        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-10-29T12:00:00+02:00'))
            ->withDateTo(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-10-29T12:05:00+02:00'))
            ->withFormat(CsvFormat::FORMAT_CSV);
        /** @var MeasurementsBulkReader $bulkReader */
        $bulkReader = $this->api->system('ABCDE')->powerPlantControllers()->bulk()->measurements()->get($criteria);

        $this->assertEquals($cvsRawData, $bulkReader->getAsString());
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Delimiter and decimal point symbols can't be the same
     */
    public function testGetPowerPlantControllersBulkDataWithCsvFormatWithWrongParameter() {
        $criteria = new MeasurementsCriteria();
        $criteria->withDateFrom(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-09-01T10:00:00+02:00'))
            ->withDateTo(\DateTime::createFromFormat(\DateTime::RFC3339, '2016-09-01T10:15:00+02:00'))
            ->withFormat(CsvFormat::FORMAT_CSV)
            ->withDelimiter(CsvFormat::DELIMITER_COMMA)
            ->withDecimalPoint(CsvFormat::DECIMAL_POINT_COMMA)
            ->withPrecision(CsvFormat::PRECISION_2);
        $this->api->system('ABCDE')->powerPlantControllers()->bulk()->measurements()->get($criteria);
    }
}
