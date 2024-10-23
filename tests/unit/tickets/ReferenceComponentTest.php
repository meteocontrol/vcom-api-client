<?php

namespace meteocontrol\client\vcomapi\tests\unit\tickets;

use DateTime;
use GuzzleHttp\RequestOptions;
use InvalidArgumentException;
use meteocontrol\client\vcomapi\filters\ReferenceComponentCriteria;
use meteocontrol\client\vcomapi\model\YieldLoss;
use meteocontrol\client\vcomapi\tests\unit\TestCase;

class ReferenceComponentTest extends TestCase {

    private int $ticketId = 457564;

    private string $affectedInverterId = 'Id86460.4';

    private string $referenceInverterIds = 'Id86460.1,Id86460.2';

    public function testGetCalculationResult(): void {
        $json = file_get_contents(__DIR__ . '/responses/getReferenceComponentCalculationResult.json');

        $criteria = new ReferenceComponentCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-11-15T10:00:00+00:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-11-15T10:59:59+00:00'))
            ->withAffectedInverterId($this->affectedInverterId)
            ->withReferenceInverterIds($this->referenceInverterIds);

        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo('tickets/457564/yield-losses/reference-component'),
                $this->identicalToUrl([
                    RequestOptions::QUERY => 'from=2016-11-15T10:00:00+00:00' .
                        '&to=2016-11-15T10:59:59+00:00' .
                        '&affectedInverterId=Id86460.4' .
                        '&referenceInverterIds=Id86460.1,Id86460.2',
                ]),
            )
            ->willReturn($json);

        $calculationResult = $this->api->ticket($this->ticketId)->yieldLosses()->referenceComponent()
            ->get($criteria);

        $this->assertEquals(0, $calculationResult->result);
        $this->assertEquals(41.29, $calculationResult->realLostYield);
        $this->assertEmpty($calculationResult->comment);
        $this->assertEquals(7.34962, $calculationResult->totalCompensation);
    }

    public function testReplaceCalculationResult(): void {
        $criteria = new ReferenceComponentCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-11-15T10:00:00+00:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-11-15T10:59:59+00:00'))
            ->withAffectedInverterId($this->affectedInverterId)
            ->withReferenceInverterIds($this->referenceInverterIds);

        $yieldLoss = new YieldLoss();
        $yieldLoss->realLostYield = 1.1;
        $yieldLoss->comment = 'Test comment';

        $this->api->expects($this->once())
            ->method('put')
            ->with(
                $this->identicalTo('tickets/457564/yield-losses/reference-component'),
                [
                    RequestOptions::JSON => [
                        'realLostYield' => $yieldLoss->realLostYield,
                        'comment' => $yieldLoss->comment,
                    ],
                    RequestOptions::QUERY => http_build_query([
                        'from' => '2016-11-15T10:00:00+00:00',
                        'to' => '2016-11-15T10:59:59+00:00',
                        'affectedInverterId' => 'Id86460.4',
                        'referenceInverterIds' => 'Id86460.1,Id86460.2',
                    ]),
                ],
            );

        $this->api->ticket($this->ticketId)->yieldLosses()->referenceComponent()->replace($criteria, $yieldLoss);
    }

    public function testReplaceCalculationResultWithoutRequiredFields(): void {
        $criteria = new ReferenceComponentCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2016-11-15T10:00:00+00:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2016-11-15T10:59:59+00:00'))
            ->withAffectedInverterId($this->affectedInverterId)
            ->withReferenceInverterIds($this->referenceInverterIds);

        $yieldLoss = new YieldLoss();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Yield loss is invalid!');

        $this->api->ticket($this->ticketId)->yieldLosses()->referenceComponent()->replace($criteria, $yieldLoss);
    }
}
