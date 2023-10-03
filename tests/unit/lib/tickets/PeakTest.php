<?php

namespace meteocontrol\client\vcomapi\tests\unit\tickets;

use DateTime;
use meteocontrol\client\vcomapi\filters\YieldLossesCriteria;
use meteocontrol\client\vcomapi\tests\unit\TestCase;

class PeakTest extends TestCase {

    public function testGridOperator(): void {
        $json = file_get_contents(__DIR__ . '/responses/getPeakGridOperator.json');

        $criteria = new YieldLossesCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-10-01T00:00:00+00:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-10-31T23:59:59+00:00'));

        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('tickets/457564/yield-losses/peak/grid-operator'),
                $this->identicalToUrl(
                    'from=2016-10-01T00:00:00+00:00' .
                    '&to=2016-10-31T23:59:59+00:00'
                )
            )
            ->willReturn($json);

        $calculationResult = $this->api->ticket(457564)->yieldLosses()->peak()->gridOperator()->get($criteria);

        $this->assertEquals(16.82, $calculationResult->result);
        $this->assertEquals(16.82, $calculationResult->realLostYield);
        $this->assertEmpty($calculationResult->comment);
        $this->assertEquals(1.23, $calculationResult->totalCompensation);
    }

    public function testDirectMarketing(): void {
        $json = file_get_contents(__DIR__ . '/responses/getPeakDirectMarketing.json');

        $criteria = new YieldLossesCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-10-01T00:00:00+00:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-10-31T23:59:59+00:00'));

        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('tickets/457564/yield-losses/peak/direct-marketing'),
                $this->identicalToUrl(
                    'from=2016-10-01T00:00:00+00:00' .
                    '&to=2016-10-31T23:59:59+00:00'
                )
            )
            ->willReturn($json);

        $calculationResult = $this->api->ticket(457564)->yieldLosses()->peak()->directMarketing()->get($criteria);

        $this->assertEquals(1752.19, $calculationResult->result);
        $this->assertEquals(1752.19, $calculationResult->realLostYield);
        $this->assertEmpty($calculationResult->comment);
        $this->assertEquals(129.13, $calculationResult->totalCompensation);
    }
}
