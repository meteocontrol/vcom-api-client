<?php

namespace meteocontrol\client\vcomapi\tests\unit\tickets;

use DateTime;
use meteocontrol\client\vcomapi\filters\YieldLossesCriteria;
use meteocontrol\client\vcomapi\tests\unit\TestCase;

class FlatRateTest extends TestCase {

    public function testHasNoValidConfiguration(): void {
        $json = file_get_contents(__DIR__ . '/responses/getFlatRateCalculationResult.json');

        $criteria = new YieldLossesCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DateTime::RFC3339, '2016-10-01T00:00:00+00:00'))
            ->withDateTo(DateTime::createFromFormat(DateTime::RFC3339, '2016-10-31T23:59:59+00:00'));

        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('tickets/457564/yield-losses/flat-rate'),
                $this->identicalToUrl(
                    'from=2016-10-01T00:00:00+00:00' .
                    '&to=2016-10-31T23:59:59+00:00'
                )
            )
            ->willReturn($json);

        $calculationResult = $this->api->ticket(457564)->yieldLosses()->flatRate()->get($criteria);

        $this->assertEquals(0, $calculationResult->result);
        $this->assertEquals(41.29, $calculationResult->realLostYield);
        $this->assertEmpty($calculationResult->comment);
        $this->assertEquals(7.34962, $calculationResult->totalCompensation);
    }
}
