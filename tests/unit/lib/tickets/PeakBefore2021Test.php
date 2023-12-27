<?php

namespace meteocontrol\client\vcomapi\tests\unit\tickets;

use DateTime;
use meteocontrol\client\vcomapi\filters\YieldLossesCriteria;
use meteocontrol\client\vcomapi\tests\unit\TestCase;

class PeakBefore2021Test extends TestCase {

    public function testGridOperator(): void {
        $json = file_get_contents(__DIR__ . '/responses/getPeakBefore2021GridOperator.json');

        $criteria = new YieldLossesCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2022-05-25T00:00:00+00:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2022-05-31T23:59:59+00:00'));

        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('tickets/457564/yield-losses/peak-before-2021/grid-operator'),
                $this->identicalToUrl(
                    'from=2022-05-25T00:00:00+00:00' .
                    '&to=2022-05-31T23:59:59+00:00'
                )
            )
            ->willReturn($json);

        $calculationResult = $this->api->ticket(457564)->yieldLosses()->peakBefore2021()->gridOperator()
            ->get($criteria);

        $this->assertEquals(1682.19, $calculationResult->result);
        $this->assertEquals(1682.19, $calculationResult->realLostYield);
        $this->assertEmpty($calculationResult->comment);
        $this->assertEquals(123.98, $calculationResult->totalCompensation);
    }

    public function testEnergyTrader(): void {
        $json = file_get_contents(__DIR__ . '/responses/getPeakBefore2021EnergyTrader.json');

        $criteria = new YieldLossesCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2022-05-25T00:00:00+00:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2022-05-31T23:59:59+00:00'));

        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('tickets/457564/yield-losses/peak-before-2021/energy-trader'),
                $this->identicalToUrl(
                    'from=2022-05-25T00:00:00+00:00' .
                    '&to=2022-05-31T23:59:59+00:00'
                )
            )
            ->willReturn($json);

        $calculationResult = $this->api->ticket(457564)->yieldLosses()->peakBefore2021()->energyTrader()
            ->get($criteria);

        $this->assertEquals(1682.19, $calculationResult->result);
        $this->assertEquals(1682.19, $calculationResult->realLostYield);
        $this->assertEmpty($calculationResult->comment);
        $this->assertEquals(123.98, $calculationResult->totalCompensation);
    }
}
