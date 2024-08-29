<?php

namespace meteocontrol\client\vcomapi\tests\unit\tickets;

use DateTime;
use GuzzleHttp\RequestOptions;
use InvalidArgumentException;
use meteocontrol\client\vcomapi\filters\YieldLossesCriteria;
use meteocontrol\client\vcomapi\model\YieldLoss;
use meteocontrol\client\vcomapi\tests\unit\TestCase;

class YieldLossesTest extends TestCase {

    private int $ticketId = 12345;

    public function testGetSavedCalculationResult(): void {
        $json = file_get_contents(__DIR__ . '/responses/getYieldLosses.json');

        $this->api->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('tickets/12345/yield-losses'))
            ->willReturn($json);

        $yieldLosses = $this->api->ticket($this->ticketId)->yieldLosses()->get();

        $this->assertEquals('linear-equation', $yieldLosses->model);
        $this->assertEquals(69.32, $yieldLosses->result);
        $this->assertEquals(41.29, $yieldLosses->realLostYield);
        $this->assertEquals(7.34962, $yieldLosses->totalCompensation);
        $this->assertEquals('', $yieldLosses->comment);
    }

    /**
     * @dataProvider getSourceProvider
     */
    public function testGetCalculationResult(
        string $model,
        string $source,
        string $modelEndpoint,
        string $sourceEndpoint,
        string $expectedResponse,
        array $expected,
    ): void {
        $json = file_get_contents(__DIR__ . "/responses/{$expectedResponse}");

        $criteria = $this->getCriteria();

        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo("tickets/12345/yield-losses/{$modelEndpoint}/{$sourceEndpoint}"),
                $this->identicalToUrl([
                    RequestOptions::QUERY => 'from=2022-05-25T00:00:00+00:00' .
                        '&to=2022-05-31T23:59:59+00:00',
                ]),
            )
            ->willReturn($json);

        $actual = $this->api->ticket($this->ticketId)->yieldLosses()->{$model}()->{$source}()->get($criteria);

        $this->assertEquals($expected['result'], $actual->result);
        $this->assertEquals($expected['realLostYield'], $actual->realLostYield);
        $this->assertEquals($expected['comment'], $actual->comment);
        $this->assertEquals($expected['totalCompensation'], $actual->totalCompensation);
    }

    /**
     * @dataProvider getSourceProvider
     */
    public function testReplaceCalculationResult(
        string $model,
        string $source,
        string $modelEndpoint,
        string $sourceEndpoint,
    ): void {
        $criteria = $this->getCriteria();

        $yieldLoss = new YieldLoss();
        $yieldLoss->realLostYield = 1.1;
        $yieldLoss->comment = 'Test comment';

        $this->api->expects($this->once())
            ->method('put')
            ->with(
                $this->identicalTo("tickets/12345/yield-losses/{$modelEndpoint}/{$sourceEndpoint}"),
                [
                    RequestOptions::JSON => [
                        'realLostYield' => $yieldLoss->realLostYield,
                        'comment' => $yieldLoss->comment,
                    ],
                    RequestOptions::QUERY => http_build_query(
                        [
                            'from' => '2022-05-25T00:00:00+00:00',
                            'to' => '2022-05-31T23:59:59+00:00',
                        ],
                    ),
                ],
            );

        $this->api->ticket($this->ticketId)->yieldLosses()->{$model}()->{$source}()->replace($criteria, $yieldLoss);
    }

    /**
     * @dataProvider getSourceProvider
     */
    public function testReplaceCalculationResultWithoutRequiredFields(string $model, string $source): void {
        $criteria = $this->getCriteria();

        $yieldLoss = new YieldLoss();

        $this->expectException(InvalidArgumentException::class);

        $this->api->ticket($this->ticketId)->yieldLosses()->{$model}()->{$source}()->replace($criteria, $yieldLoss);
    }

    private function getSourceProvider(): array {
        return [
            [
                'flatRate',
                'gridOperator',
                'flat-rate',
                'grid-operator',
                'getFlatRateGridOperator.json',
                [
                    'result' => 1017.23,
                    'realLostYield' => 1017.23,
                    'comment' => '',
                    'totalCompensation' => 74.97,
                ]
            ],
            [
                'flatRate',
                'energyTrader',
                'flat-rate',
                'energy-trader',
                'getFlatRateEnergyTrader.json',
                [
                    'result' => 1017.23,
                    'realLostYield' => 1017.23,
                    'comment' => '',
                    'totalCompensation' => 74.97,
                ]
            ],
            [
                'flatRate',
                'manual',
                'flat-rate',
                'manual',
                'getFlatRateManual.json',
                [
                    'result' => 1017.23,
                    'realLostYield' => 1028.34,
                    'comment' => '',
                    'totalCompensation' => 74.97,
                ]
            ],
            [
                'flatRate',
                'static',
                'flat-rate',
                'static',
                'getFlatRateStatic.json',
                [
                    'result' => 1017.23,
                    'realLostYield' => 1039.45,
                    'comment' => '',
                    'totalCompensation' => 74.97,
                ]
            ],
            [
                'peak',
                'gridOperator',
                'peak',
                'grid-operator',
                'getPeakGridOperator.json',
                [
                    'result' => 1005.15,
                    'realLostYield' => 1005.15,
                    'comment' => '',
                    'totalCompensation' => 74.08,
                ]
            ],
            [
                'peak',
                'energyTrader',
                'peak',
                'energy-trader',
                'getPeakEnergyTrader.json',
                [
                    'result' => 1005.15,
                    'realLostYield' => 1005.15,
                    'comment' => '',
                    'totalCompensation' => 74.08,
                ]
            ],
            [
                'peak',
                'manual',
                'peak',
                'manual',
                'getPeakManual.json',
                [
                    'result' => 1682.19,
                    'realLostYield' => 1693.30,
                    'comment' => '',
                    'totalCompensation' => 123.98,
                ]
            ],
            [
                'peak',
                'static',
                'peak',
                'static',
                'getPeakStatic.json',
                [
                    'result' => 1682.19,
                    'realLostYield' => 1704.41,
                    'comment' => '',
                    'totalCompensation' => 123.98,
                ]
            ],
            [
                'simplifiedPeak',
                'gridOperator',
                'simplified-peak',
                'grid-operator',
                'getPeakGridOperator.json',
                [
                    'result' => 1005.15,
                    'realLostYield' => 1005.15,
                    'comment' => '',
                    'totalCompensation' => 74.08,
                ]
            ],
            [
                'simplifiedPeak',
                'energyTrader',
                'simplified-peak',
                'energy-trader',
                'getPeakEnergyTrader.json',
                [
                    'result' => 1005.15,
                    'realLostYield' => 1005.15,
                    'comment' => '',
                    'totalCompensation' => 74.08,
                ]
            ],
            [
                'simplifiedPeak',
                'manual',
                'simplified-peak',
                'manual',
                'getSimplifiedPeakManual.json',
                [
                    'result' => 697.70,
                    'realLostYield' => 708.81,
                    'comment' => '',
                    'totalCompensation' => 51.42
                ]
            ],
            [
                'simplifiedPeak',
                'static',
                'simplified-peak',
                'static',
                'getSimplifiedPeakStatic.json',
                [
                    'result' => 697.70,
                    'realLostYield' => 719.92,
                    'comment' => '',
                    'totalCompensation' => 51.42
                ]
            ],
        ];
    }

    private function getCriteria(): YieldLossesCriteria {
        $criteria = new YieldLossesCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2022-05-25T00:00:00+00:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2022-05-31T23:59:59+00:00'));
        return $criteria;
    }
}
