<?php

declare(strict_types=1);

namespace meteocontrol\client\vcomapi\tests\unit\systems;

use DateTime;
use InvalidArgumentException;
use meteocontrol\client\vcomapi\filters\MeasurementsCriteria;
use meteocontrol\client\vcomapi\tests\unit\TestCase;

class KpiTargetsTest extends TestCase {

    private const TARGETS = [85.0, 85.0, 85.0, 85.0, 90.0, 90.0, 90.0, 90.0, 90.0, 90.0, 85.0, 85.0];

    public function testGetPrTargets(): void {
        $json = file_get_contents(__DIR__ . '/responses/getKpiTargets.json');

        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/kpi-targets/pr'))
            ->willReturn($json);

        $targets = $this->api->system('ABCDE')->kpiTargets()->pr()->get();

        $this->assertEquals(self::TARGETS, $targets);
    }

    public function testSetPrTargets(): void {
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/kpi-targets/pr'),
                null,
                json_encode(self::TARGETS),
                'PUT',
            );

        $this->api->system('ABCDE')->kpiTargets()->pr()->set(self::TARGETS);
    }

    public function testDeletePrTargets(): void {
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/kpi-targets/pr'),
                null,
                null,
                'DELETE',
            );

        $this->api->system('ABCDE')->kpiTargets()->pr()->delete();
    }

    public function testGetAvailabilityTargets(): void {
        $json = file_get_contents(__DIR__ . '/responses/getKpiTargets.json');

        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/kpi-targets/availability'))
            ->willReturn($json);

        $targets = $this->api->system('ABCDE')->kpiTargets()->availability()->get();

        $this->assertEquals(self::TARGETS, $targets);
    }

    public function testSetAvailabilityTargets(): void {
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/kpi-targets/availability'),
                null,
                json_encode(self::TARGETS),
                'PUT',
            );

        $this->api->system('ABCDE')->kpiTargets()->availability()->set(self::TARGETS);
    }

    public function testDeleteAvailabilityTargets(): void {
        $this->api->expects($this->once())
            ->method('run')
            ->with(
                $this->identicalTo('systems/ABCDE/kpi-targets/availability'),
                null,
                null,
                'DELETE',
            );

        $this->api->system('ABCDE')->kpiTargets()->availability()->delete();
    }

    public function testSetTargetsWithInsufficientNumberOfTargets(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The size of target values must be 12 (targets for each month)');

        $this->api->system('ABCDE')->kpiTargets()->pr()->set([85.0, 85.0]);
    }

    public function testSetTargetsWithTargetsTooLarge(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Maximum allowed target is 100.00');

        $this->api->system('ABCDE')->kpiTargets()->pr()->set(array_fill(0, 12, 100.01));
    }

    public function testSetTargetsWithTargetsTooSmall(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Minimum allowed target is 0.00');

        $this->api->system('ABCDE')->kpiTargets()->pr()->set(array_fill(0, 12, -0.01));
    }
}
