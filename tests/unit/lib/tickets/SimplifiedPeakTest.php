<?php

namespace meteocontrol\client\vcomapi\tests\unit\tickets;

use DateTime;
use meteocontrol\client\vcomapi\filters\YieldLossesCriteria;
use meteocontrol\client\vcomapi\tests\unit\TestCase;

class SimplifiedPeakTest extends TestCase {

    public function testGridOperator(): void {
        $json = file_get_contents(__DIR__ . '/responses/getSimplifiedPeakGridOperator.json');

        $criteria = new YieldLossesCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2022-05-25T00:00:00+00:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2022-05-31T23:59:59+00:00'));

        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('tickets/457564/yield-losses/simplified-peak/grid-operator'),
                $this->identicalToUrl(
                    'from=2022-05-25T00:00:00+00:00' .
                    '&to=2022-05-31T23:59:59+00:00'
                )
            )
            ->willReturn($json);

        $calculationResult = $this->api->ticket(457564)->yieldLosses()->simplifiedPeak()->gridOperator()
            ->get($criteria);

        $this->assertEquals(697.70, $calculationResult->result);
        $this->assertEquals(697.70, $calculationResult->realLostYield);
        $this->assertEmpty($calculationResult->comment);
        $this->assertEquals(51.42, $calculationResult->totalCompensation);
    }

    public function testEnergyTrader(): void {
        $json = file_get_contents(__DIR__ . '/responses/getSimplifiedPeakEnergyTrader.json');

        $criteria = new YieldLossesCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2022-05-25T00:00:00+00:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2022-05-31T23:59:59+00:00'));

        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('tickets/457564/yield-losses/simplified-peak/energy-trader'),
                $this->identicalToUrl(
                    'from=2022-05-25T00:00:00+00:00' .
                    '&to=2022-05-31T23:59:59+00:00'
                )
            )
            ->willReturn($json);

        $calculationResult = $this->api->ticket(457564)->yieldLosses()->simplifiedPeak()->energyTrader()
            ->get($criteria);

        $this->assertEquals(697.70, $calculationResult->result);
        $this->assertEquals(697.70, $calculationResult->realLostYield);
        $this->assertEmpty($calculationResult->comment);
        $this->assertEquals(51.42, $calculationResult->totalCompensation);
    }
}
