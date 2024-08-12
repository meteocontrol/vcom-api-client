<?php

namespace meteocontrol\client\vcomapi\tests\unit\systems;

use DateTime;
use meteocontrol\client\vcomapi\model\TechnicalData;
use meteocontrol\client\vcomapi\tests\unit\TestCase;

class TechnicalDataTest extends TestCase {

    public function testGetTechnicalData() {
        $json = file_get_contents(__DIR__ . '/responses/getTechnicalData.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/technical-data'))
            ->willReturn($json);

        /** @var TechnicalData $technicalData */
        $technicalData = $this->api->system('ABCDE')->technicalData()->get();

        $this->assertEquals(79.24, $technicalData->nominalPower);
        $this->assertEquals(549.11, $technicalData->siteArea);
        $this->assertCount(1, $technicalData->panels);
        $this->assertEquals('Suntech', $technicalData->panels[0]->vendor);
        $this->assertEquals('STP280-24/Vb', $technicalData->panels[0]->model);
        $this->assertEquals(283, $technicalData->panels[0]->count);
        $this->assertCount(2, $technicalData->inverters);
        $this->assertEquals('KACO new energy', $technicalData->inverters[0]->vendor);
        $this->assertEquals('Powador 8000xi', $technicalData->inverters[0]->model);
        $this->assertEquals(9, $technicalData->inverters[0]->count);
        $this->assertEquals('KACO new energy', $technicalData->inverters[1]->vendor);
        $this->assertEquals('Powador 3500xi', $technicalData->inverters[1]->model);
        $this->assertEquals(1, $technicalData->inverters[1]->count);
    }

    public function testGetLastDataInput() {
        $json = file_get_contents(__DIR__ . '/responses/getLastDataInput.json');
        $this->api->expects($this->once())
            ->method('run')
            ->with($this->identicalTo('systems/ABCDE/technical-data/last-data-input'))
            ->willReturn($json);

        $lastDataInput = $this->api->system('ABCDE')->technicalData()->lastDataInput()->get();

        $this->assertEquals(new DateTime('2024-08-06T12:59:59+00:00'), $lastDataInput->timestamp);
    }
}
