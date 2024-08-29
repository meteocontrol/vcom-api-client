<?php

namespace meteocontrol\client\vcomapi\tests\unit\cmms;

use DateTime;
use meteocontrol\client\vcomapi\tests\unit\TestCase;
use meteocontrol\vcomapi\model\WorkOrderFormDetail;

class WorkOrderFromTest extends TestCase {

    public function testGetWorkOrderForms() {
        $orderId = 3280;
        $formId = 3656;
        $json = file_get_contents(__DIR__ . "/responses/getWorkOrderForm.json");

        $this->api->expects($this->once())
            ->method("get")
            ->with($this->identicalTo("cmms/workorders/{$orderId}/forms/{$formId}"))
            ->willReturn($json);

        $actualForm = $this->api->cmms()->workOrder($orderId)->form($formId)->get();
        $this->assertEquals($this->getExpectedForm(), $actualForm);
    }

    /**
     * @return WorkOrderFormDetail
     */
    private function getExpectedForm(): WorkOrderFormDetail {
        $form = new WorkOrderFormDetail();
        $form->formId = 3656;
        $form->workOrderId = 3280;
        $form->title = 'app-api test workOrderForm';
        $form->form = '{"elements":[{"label":"Erste Seite","component":"page","config":{},' .
            '"children":[],"id":"page-1","description":"-"}]}';
        $form->description = 'test form';
        $form->data = '{}';
        $form->originalData = null;
        $form->savedAt = new DateTime('2018-05-18T13:14:45Z');
        $form->createdAt = new DateTime('2018-05-18T13:14:45Z');
        $form->completedAt = new DateTime('2020-10-19T07:40:57Z');
        $form->lastChangedAt = new DateTime('2020-10-19T07:40:57Z');
        $form->completedBy = null;
        $form->changedBy = null;
        $form->editable = false;

        return $form;
    }
}
