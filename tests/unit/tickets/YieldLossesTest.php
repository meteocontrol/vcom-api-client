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
        ?int $resolution = null,
        string $expectedResponse,
        array $expected,
    ): void {
        $json = file_get_contents(__DIR__ . "/responses/{$expectedResponse}");

        $criteria = $this->getCriteria($resolution);

        $expectedQuery = 'from=2022-05-25T00:00:00+00:00&to=2022-05-31T23:59:59+00:00';
        if ($resolution !== null) {
            $expectedQuery .= "&resolution={$resolution}";
        }

        $this->api->expects($this->once())
            ->method('get')
            ->with(
                $this->identicalTo("tickets/12345/yield-losses/{$modelEndpoint}/{$sourceEndpoint}"),
                $this->identicalToUrl([
                    RequestOptions::QUERY => $expectedQuery,
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
        $yieldLoss->comment = 'Test comment';

        $this->api->expects($this->once())
            ->method('put')
            ->with(
                $this->identicalTo("tickets/12345/yield-losses/{$modelEndpoint}/{$sourceEndpoint}"),
                [
                    RequestOptions::JSON => [
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
    public function testReplaceCalculationResultWithoutCommentField(string $model, string $source): void {
        $criteria = $this->getCriteria();

        $yieldLoss = new YieldLoss();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The comment field is empty.');

        $this->api->ticket($this->ticketId)->yieldLosses()->{$model}()->{$source}()->replace($criteria, $yieldLoss);
    }

    private function getSourceProvider(): array {
        return [
            [
                'flatRate',
                'gridOperator',
                'flat-rate',
                'grid-operator',
                900,
                'getFlatRateGridOperator.json',
                [
                    'result' => 1017.23,
                    'realLostYield' => 1017.23,
                    'comment' => '',
                    'totalCompensation' => 74.97,
                ],
            ],
            [
                'flatRate',
                'energyTrader',
                'flat-rate',
                'energy-trader',
                900,
                'getFlatRateEnergyTrader.json',
                [
                    'result' => 1017.23,
                    'realLostYield' => 1017.23,
                    'comment' => '',
                    'totalCompensation' => 74.97,
                ],
            ],
            [
                'flatRate',
                'manual',
                'flat-rate',
                'manual',
                null,
                'getFlatRateManual.json',
                [
                    'result' => 1017.23,
                    'realLostYield' => 1028.34,
                    'comment' => '',
                    'totalCompensation' => 74.97,
                ],
            ],
            [
                'flatRate',
                'static',
                'flat-rate',
                'static',
                null,
                'getFlatRateStatic.json',
                [
                    'result' => 1017.23,
                    'realLostYield' => 1039.45,
                    'comment' => '',
                    'totalCompensation' => 74.97,
                ],
            ],
            [
                'flatRate',
                'undetermined',
                'flat-rate',
                'undetermined',
                900,
                'getFlatRateUndetermined.json',
                [
                    'result' => 20.56,
                    'realLostYield' => 20.56,
                    'comment' => '',
                    'totalCompensation' => 1.51,
                ],
            ],
            [
                'peak',
                'gridOperator',
                'peak',
                'grid-operator',
                900,
                'getPeakGridOperator.json',
                [
                    'result' => 1005.15,
                    'realLostYield' => 1005.15,
                    'comment' => '',
                    'totalCompensation' => 74.08,
                ],
            ],
            [
                'peak',
                'energyTrader',
                'peak',
                'energy-trader',
                900,
                'getPeakEnergyTrader.json',
                [
                    'result' => 1005.15,
                    'realLostYield' => 1005.15,
                    'comment' => '',
                    'totalCompensation' => 74.08,
                ],
            ],
            [
                'peak',
                'manual',
                'peak',
                'manual',
                null,
                'getPeakManual.json',
                [
                    'result' => 1682.19,
                    'realLostYield' => 1693.30,
                    'comment' => '',
                    'totalCompensation' => 123.98,
                ],
            ],
            [
                'peak',
                'static',
                'peak',
                'static',
                null,
                'getPeakStatic.json',
                [
                    'result' => 1682.19,
                    'realLostYield' => 1704.41,
                    'comment' => '',
                    'totalCompensation' => 123.98,
                ],
            ],
            [
                'peak',
                'undetermined',
                'peak',
                'undetermined',
                900,
                'getPeakUndetermined.json',
                [
                    'result' => 4.34,
                    'realLostYield' => 4.34,
                    'comment' => '',
                    'totalCompensation' => 0.31,
                ],
            ],
            [
                'simplifiedPeak',
                'gridOperator',
                'simplified-peak',
                'grid-operator',
                900,
                'getPeakGridOperator.json',
                [
                    'result' => 1005.15,
                    'realLostYield' => 1005.15,
                    'comment' => '',
                    'totalCompensation' => 74.08,
                ],
            ],
            [
                'simplifiedPeak',
                'energyTrader',
                'simplified-peak',
                'energy-trader',
                900,
                'getPeakEnergyTrader.json',
                [
                    'result' => 1005.15,
                    'realLostYield' => 1005.15,
                    'comment' => '',
                    'totalCompensation' => 74.08,
                ],
            ],
            [
                'simplifiedPeak',
                'manual',
                'simplified-peak',
                'manual',
                null,
                'getSimplifiedPeakManual.json',
                [
                    'result' => 697.70,
                    'realLostYield' => 708.81,
                    'comment' => '',
                    'totalCompensation' => 51.42
                ],
            ],
            [
                'simplifiedPeak',
                'static',
                'simplified-peak',
                'static',
                null,
                'getSimplifiedPeakStatic.json',
                [
                    'result' => 697.70,
                    'realLostYield' => 719.92,
                    'comment' => '',
                    'totalCompensation' => 51.42
                ],
            ],
            [
                'simplifiedPeak',
                'undetermined',
                'simplified-peak',
                'undetermined',
                900,
                'getSimplifiedPeakUndetermined.json',
                [
                    'result' => 23.83,
                    'realLostYield' => 23.83,
                    'comment' => '',
                    'totalCompensation' => 1.75,
                ],
            ],
        ];
    }

    private function getCriteria(?int $resolution = null): YieldLossesCriteria {
        $criteria = new YieldLossesCriteria();
        $criteria->withDateFrom(DateTime::createFromFormat(DATE_ATOM, '2022-05-25T00:00:00+00:00'))
            ->withDateTo(DateTime::createFromFormat(DATE_ATOM, '2022-05-31T23:59:59+00:00'));
        if ($resolution !== null) {
            $criteria->withResolution($resolution);
        }
        return $criteria;
    }
}
