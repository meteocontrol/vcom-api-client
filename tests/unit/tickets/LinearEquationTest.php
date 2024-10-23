<?php

namespace meteocontrol\client\vcomapi\tests\unit\tickets;

use DateTime;
use GuzzleHttp\RequestOptions;
use InvalidArgumentException;
use meteocontrol\client\vcomapi\filters\LinearEquationCriteria;
use meteocontrol\client\vcomapi\model\YieldLoss;
use meteocontrol\client\vcomapi\tests\unit\TestCase;

class LinearEquationTest extends TestCase {

    private int $ticketId = 457564;

    public function testGetCalculationResult(): void {
        $json = file_get_contents(__DIR__ . '/responses/getLinearEquationCalculationResult.json');

        $criteria = new LinearEquationCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-11-15T10:00:00+00:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-11-15T10:59:59+00:00'))
            ->withDateReferenceFrom(DateTime::createFromFormat(DATE_ATOM, '2016-11-15T14:50:00+00:00'))
            ->withDateReferenceTo(DateTime::createFromFormat(DATE_ATOM, '2016-11-15T15:50:00+00:00'));

        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo('tickets/457564/yield-losses/linear-equation'),
                $this->identicalToUrl(
                    [
                        RequestOptions::QUERY => 'from=2016-11-15T10:00:00+00:00' .
                            '&to=2016-11-15T10:59:59+00:00' .
                            '&referenceFrom=2016-11-15T14:50:00+00:00' .
                            '&referenceTo=2016-11-15T15:50:00+00:00',
                    ],
                ),
            )
            ->willReturn($json);

        $calculationResult = $this->api->ticket($this->ticketId)->yieldLosses()->linearEquation()->get($criteria);

        $this->assertEquals(2.87754741, $calculationResult->result);
        $this->assertEquals(41.29, $calculationResult->realLostYield);
        $this->assertEmpty($calculationResult->comment);
        $this->assertEquals(7.34962, $calculationResult->totalCompensation);
    }

    public function testReplaceCalculationResult(): void {
        $criteria = new LinearEquationCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-11-15T10:00:00+00:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-11-15T10:59:59+00:00'))
            ->withDateReferenceFrom(DateTime::createFromFormat(DATE_ATOM, '2016-11-15T14:50:00+00:00'))
            ->withDateReferenceTo(DateTime::createFromFormat(DATE_ATOM, '2016-11-15T15:50:00+00:00'));

        $yieldLoss = new YieldLoss();
        $yieldLoss->realLostYield = 1.1;
        $yieldLoss->comment = 'Test comment';

        $this->api->expects($this->once())
            ->method('put')
            ->with(
                $this->identicalTo('tickets/457564/yield-losses/linear-equation'),
                [
                    RequestOptions::JSON => [
                        'realLostYield' => $yieldLoss->realLostYield,
                        'comment' => $yieldLoss->comment,
                    ],
                    RequestOptions::QUERY => http_build_query([
                        'from' => '2016-11-15T10:00:00+00:00',
                        'to' => '2016-11-15T10:59:59+00:00',
                        'referenceFrom' => '2016-11-15T14:50:00+00:00',
                        'referenceTo' => '2016-11-15T15:50:00+00:00',
                    ]),
                ],
            );

        $this->api->ticket($this->ticketId)->yieldLosses()->linearEquation()->replace($criteria, $yieldLoss);
    }

    public function testReplaceCalculationResultWithoutRequiredFields(): void {
        $criteria = new LinearEquationCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-11-15T10:00:00+00:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-11-15T10:59:59+00:00'))
            ->withDateReferenceFrom(DateTime::createFromFormat(DATE_ATOM, '2016-11-15T14:50:00+00:00'))
            ->withDateReferenceTo(DateTime::createFromFormat(DATE_ATOM, '2016-11-15T15:50:00+00:00'));

        $yieldLoss = new YieldLoss();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Yield loss is invalid!');

        $this->api->ticket($this->ticketId)->yieldLosses()->linearEquation()->replace($criteria, $yieldLoss);
    }
}
