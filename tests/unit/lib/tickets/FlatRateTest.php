<?php

namespace meteocontrol\client\vcomapi\tests\unit\tickets;

use DateTime;
use meteocontrol\client\vcomapi\filters\YieldLossesCriteria;
use meteocontrol\client\vcomapi\tests\unit\TestCase;

class FlatRateTest extends TestCase {

    public function testGridOperator(): void {
        $json = file_get_contents(__DIR__ . '/responses/getFlatRateGridOperator.json');

        $criteria = new YieldLossesCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-10-01T00:00:00+00:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-10-31T23:59:59+00:00'));

        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('tickets/457564/yield-losses/flat-rate/grid-operator'),
                $this->identicalToUrl(
                    'from=2016-10-01T00:00:00+00:00' .
                    '&to=2016-10-31T23:59:59+00:00'
                )
            )
            ->willReturn($json);

        $calculationResult = $this->api->ticket(457564)->yieldLosses()->flatRate()->gridOperator()->get($criteria);

        $this->assertEquals(18.52, $calculationResult->result);
        $this->assertEquals(18.52, $calculationResult->realLostYield);
        $this->assertEmpty($calculationResult->comment);
        $this->assertEquals(1.36, $calculationResult->totalCompensation);
    }

    public function testDirectMarketing(): void {
        $json = file_get_contents(__DIR__ . '/responses/getFlatRateDirectMarketing.json');

        $criteria = new YieldLossesCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-10-01T00:00:00+00:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-10-31T23:59:59+00:00'));

        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('tickets/457564/yield-losses/flat-rate/direct-marketing'),
                $this->identicalToUrl(
                    'from=2016-10-01T00:00:00+00:00' .
                    '&to=2016-10-31T23:59:59+00:00'
                )
            )
            ->willReturn($json);

        $calculationResult = $this->api->ticket(457564)->yieldLosses()->flatRate()->directMarketing()->get($criteria);

        $this->assertEquals(4790.68, $calculationResult->result);
        $this->assertEquals(4790.68, $calculationResult->realLostYield);
        $this->assertEmpty($calculationResult->comment);
        $this->assertEquals(353.07, $calculationResult->totalCompensation);
    }
}
