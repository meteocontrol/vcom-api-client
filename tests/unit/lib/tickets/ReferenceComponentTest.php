<?php

namespace meteocontrol\client\vcomapi\tests\unit\tickets;

use DateTime;
use meteocontrol\client\vcomapi\filters\ReferenceComponentCriteria;
use meteocontrol\client\vcomapi\tests\unit\TestCase;

class ReferenceComponentTest extends TestCase {

    public function testGetCalculationResult(): void {
        $json = file_get_contents(__DIR__ . '/responses/getReferenceComponentCalculationResult.json');

        $criteria = new ReferenceComponentCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-11-15T10:00:00+00:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-11-15T10:59:59+00:00'))
            ->withAffectedInverterId('Id86460.4')
            ->withReferenceInverterIds('Id86460.1,Id86460.2');

        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('tickets/457564/yield-losses/reference-component'),
                $this->identicalToUrl(
                    'from=2016-11-15T10:00:00+00:00' .
                    '&to=2016-11-15T10:59:59+00:00' .
                    '&affectedInverterId=Id86460.4' .
                    '&referenceInverterIds=Id86460.1,Id86460.2'
                )
            )
            ->willReturn($json);

        $calculationResult = $this->api->ticket(457564)->yieldLosses()->referenceComponent()->get($criteria);

        $this->assertEquals(0, $calculationResult->result);
        $this->assertEquals(41.29, $calculationResult->realLostYield);
        $this->assertEmpty($calculationResult->comment);
        $this->assertEquals(7.34962, $calculationResult->totalCompensation);
    }
}
