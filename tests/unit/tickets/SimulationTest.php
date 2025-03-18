<?php

namespace meteocontrol\client\vcomapi\tests\unit\tickets;

use DateTime;
use GuzzleHttp\RequestOptions;
use InvalidArgumentException;
use meteocontrol\client\vcomapi\filters\YieldLossesCriteria;
use meteocontrol\client\vcomapi\model\YieldLoss;
use meteocontrol\client\vcomapi\tests\unit\TestCase;

class SimulationTest extends TestCase {

    private int $ticketId = 457564;
    private string $from = '2016-11-15T10:00:00+00:00';
    private string $to = '2016-11-15T10:59:59+00:00';

    public function testGetCalculationResult(): void {
        $json = file_get_contents(__DIR__ . '/responses/getSimulation.json');

        $criteria = new YieldLossesCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, $this->from))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, $this->to));

        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo("tickets/{$this->ticketId}/yield-losses/simulation"),
                $this->identicalToUrl([
                    RequestOptions::QUERY => "from={$this->from}&to={$this->to}",
                ]),
            )
            ->willReturn($json);

        $calculationResult = $this->api->ticket($this->ticketId)->yieldLosses()->simulation()
            ->get($criteria);

        $this->assertEquals(214385.23, $calculationResult->result);
        $this->assertEquals(214380, $calculationResult->realLostYield);
        $this->assertEmpty($calculationResult->comment);
        $this->assertEquals(15800.19, $calculationResult->totalCompensation);
    }

    public function testReplaceCalculationResult(): void {
        $criteria = new YieldLossesCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, $this->from))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, $this->to));

        $yieldLoss = new YieldLoss();
        $yieldLoss->realLostYield = 1.1;
        $yieldLoss->comment = 'Test comment';

        $this->api->expects($this->once())
            ->method('put')
            ->with(
                $this->identicalTo("tickets/{$this->ticketId}/yield-losses/simulation"),
                [
                    RequestOptions::JSON => [
                        'realLostYield' => $yieldLoss->realLostYield,
                        'comment' => $yieldLoss->comment,
                    ],
                    RequestOptions::QUERY => http_build_query([
                        'from' => $this->from,
                        'to' => $this->to,
                    ]),
                ],
            );

        $this->api->ticket($this->ticketId)->yieldLosses()->simulation()->replace($criteria, $yieldLoss);
    }

    public function testReplaceCalculationResultWithoutRequiredFields(): void {
        $criteria = new YieldLossesCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, $this->from))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, $this->to));

        $yieldLoss = new YieldLoss();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Yield loss is invalid!');

        $this->api->ticket($this->ticketId)->yieldLosses()->simulation()->replace($criteria, $yieldLoss);
    }
}
