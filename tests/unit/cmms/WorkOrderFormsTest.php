<?php

namespace meteocontrol\client\vcomapi\tests\unit\cmms;

use DateTime;
use meteocontrol\client\vcomapi\tests\unit\TestCase;
use meteocontrol\client\vcomapi\model\WorkOrderForm;

class WorkOrderFormsTest extends TestCase {

    public function testGetWorkOrderForm() {
        $orderId = 12345;
        $json = file_get_contents(__DIR__ . "/responses/getWorkOrderForms.json");

        $this->api->expects($this->once())
            ->method("get")
            ->with($this->identicalTo("cmms/workorders/{$orderId}/forms"))
            ->willReturn($json);

        $actualForms = $this->api->cmms()->workOrder($orderId)->forms()->get();
        $this->assertEquals($this->getExpectedForm(), $actualForms);
    }

    private function getExpectedForm(): array {
        $form1 = new WorkOrderForm();
        $form1->formId = 3656;
        $form1->title = 'app-api test workOrderForm';
        $form1->lastChangedAt = new DateTime('2020-10-19T07:40:57Z');

        $form2 = new WorkOrderForm();
        $form2->formId = 3657;
        $form2->title = 'app-api test workOrderForm';

        return [$form1, $form2];
    }
}
