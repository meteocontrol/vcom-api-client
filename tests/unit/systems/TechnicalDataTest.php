<?php

namespace meteocontrol\client\vcomapi\tests\unit\systems;

use DateTime;
use meteocontrol\client\vcomapi\model\TechnicalData;
use meteocontrol\client\vcomapi\tests\unit\TestCase;

class TechnicalDataTest extends TestCase {

    public function testGetTechnicalData() {
        $json = file_get_contents(__DIR__ . '/responses/getTechnicalData.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('systems/ABCDE/technical-data'))
            ->willReturn($json);

        /** @var TechnicalData $technicalData */
        $technicalData = $this->api->system('ABCDE')->technicalData()->get();

        $this->assertEquals(79.24, $technicalData->nominalPower);
        $this->assertEquals(549.11, $technicalData->siteArea);

        $this->assertCount(1, $technicalData->panels);
        $panel = $technicalData->panels[0];
        $this->assertEquals('Suntech', $panel->vendor);
        $this->assertEquals('STP280-24/Vb', $panel->model);
        $this->assertEquals(283, $panel->count);

        $this->assertCount(2, $technicalData->inverters);
        $inverter1 = $technicalData->inverters[0];
        $inverter2 = $technicalData->inverters[1];
        $this->assertEquals('KACO new energy', $inverter1->vendor);
        $this->assertEquals('Powador 8000xi', $inverter1->model);
        $this->assertEquals(9, $inverter1->count);
        $this->assertEquals('KACO new energy', $inverter2->vendor);
        $this->assertEquals('Powador 3500xi', $inverter2->model);
        $this->assertEquals(1, $inverter2->count);

        $this->assertCount(2, $technicalData->systemConfigurations);
        $systemConfiguration1 = $technicalData->systemConfigurations[0];
        $systemConfiguration2 = $technicalData->systemConfigurations[1];
        $this->assertEquals('KACO new energy', $systemConfiguration1->inverter->vendor);
        $this->assertEquals('Powador 8000xi', $systemConfiguration1->inverter->model);
        $this->assertEquals(3, $systemConfiguration1->inverter->count);
        $this->assertEquals(3, $systemConfiguration1->mpptCount);
        $this->assertEquals(230, $systemConfiguration1->numberOfModules);
        $this->assertCount(3, $systemConfiguration1->mpptInputs);
        $mpptInputs1 = $systemConfiguration1->mpptInputs[1];
        $mpptInputs2 = $systemConfiguration1->mpptInputs[2];
        $mpptInputs3 = $systemConfiguration1->mpptInputs[3];
        $this->assertEquals('Suntech', $mpptInputs1->module->vendor);
        $this->assertEquals('STP280-24/Vb', $mpptInputs1->module->model);
        $this->assertEquals('facade', $mpptInputs1->type);
        $this->assertEquals(40, $mpptInputs1->modulesPerString);
        $this->assertEquals(2, $mpptInputs1->stringCount);
        $this->assertEquals(30, $mpptInputs2->modulesPerString);
        $this->assertEquals(3, $mpptInputs2->stringCount);
        $this->assertEquals(60, $mpptInputs3->modulesPerString);
        $this->assertEquals(1, $mpptInputs3->stringCount);

        $this->assertEquals('KACO new energy', $systemConfiguration2->inverter->vendor);
        $this->assertEquals('Powador 3500xi', $systemConfiguration2->inverter->model);
        $this->assertEquals(1, $systemConfiguration2->inverter->count);
        $this->assertEquals(1, $systemConfiguration2->mpptCount);
        $this->assertEquals(43, $systemConfiguration2->numberOfModules);
        $this->assertCount(1, $systemConfiguration2->mpptInputs);
        $mpptInputs1 = $systemConfiguration2->mpptInputs[5];
        $this->assertEquals('Suntech', $mpptInputs1->module->vendor);
        $this->assertEquals('STP280-24/Vb', $mpptInputs1->module->model);
        $this->assertEquals('ground', $mpptInputs1->type);
        $this->assertEquals(43, $mpptInputs1->modulesPerString);
        $this->assertEquals(1, $mpptInputs1->stringCount);
    }

    public function testGetLastDataInput() {
        $json = file_get_contents(__DIR__ . '/responses/getLastDataInput.json');
        $this->api->expects($this->once())
            ->method('get')
            ->with($this->identicalTo('systems/ABCDE/technical-data/last-data-input'))
            ->willReturn($json);

        $lastDataInput = $this->api->system('ABCDE')->technicalData()->lastDataInput()->get();

        $this->assertEquals(new DateTime('2024-08-06T12:59:59+00:00'), $lastDataInput->timestamp);
    }
}
