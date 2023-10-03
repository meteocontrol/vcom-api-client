<?php

namespace meteocontrol\client\vcomapi\tests\unit\tickets;

use DateTime;
use meteocontrol\client\vcomapi\filters\LinearEquationCriteria;
use meteocontrol\client\vcomapi\tests\unit\TestCase;

class LinearEquationTest extends TestCase {

    public function testGetCalculationResult(): void {
        $json = file_get_contents(__DIR__ . '/responses/getLinearEquationCalculationResult.json');

        $criteria = new LinearEquationCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-11-15T10:00:00+00:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-11-15T10:59:59+00:00'))
            ->withDateReferenceFrom(DateTime::createFromFormat(DATE_ATOM, '2016-11-15T14:50:00+00:00'))
            ->withDateReferenceTo(DateTime::createFromFormat(DATE_ATOM, '2016-11-15T15:50:00+00:00'));

        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('tickets/457564/yield-losses/linear-equation'),
                $this->identicalToUrl(
                    'from=2016-11-15T10:00:00+00:00' .
                    '&to=2016-11-15T10:59:59+00:00' .
                    '&referenceFrom=2016-11-15T14:50:00+00:00' .
                    '&referenceTo=2016-11-15T15:50:00+00:00'
                )
            )
            ->willReturn($json);

        $calculationResult = $this->api->ticket(457564)->yieldLosses()->linearEquation()->get($criteria);

        $this->assertEquals(2.87754741, $calculationResult->result);
        $this->assertEquals(41.29, $calculationResult->realLostYield);
        $this->assertEmpty($calculationResult->comment);
        $this->assertEquals(7.34962, $calculationResult->totalCompensation);
    }
}
